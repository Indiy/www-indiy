<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    require_once '../../includes/paypalfunctions.php';
    
    session_write_close();

    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        die();
    }
    if( $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
    {
        header("HTTP/1.0 403 Forbidden");
        die();
    }


    $method = $_REQUEST['method'];
    if( $method == 'pay_invoice' )
    {
        do_pay_invoice();
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        print "Unknown request\n";
        var_dump($_REQUEST);
        die();
    }
    
    function do_pay_invoice()
    {
        $invoice_id = $_REQUEST['invoice_id'];
        
        $invoice = mf(mq("SELECT * FROM artist_invoices WHERE id='$invoice_id'"));
        
        if( !$invoice )
        {
            $ret = array("error" => "Unknown invoice.");
            echo json_encode($ret);
            die();
        }
        $artist_id = $invoice['artist_id'];
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
        
        $email = $artist['email'];
        if( !$email )
        {
            $ret = array("error" => "Artist has no email address, can't pay.");
            echo json_encode($ret);
            die();
        }

        $amount = $invoice['amount'];
        $paid_amount = $invoice['paid_amount'];
        
        if( $paid_amount >= $amount )
        {
            $ret = array("error" => "Invoice already paid in full.");
            echo json_encode($ret);
            die();
        }
        $to_pay_amount = $amount - $paid_amount;
        
        $resArray = paypal_masspay($email,$to_pay_amount);
        
        $ack = $resArray['ACK'];
        
        if( $ack == 'Success' )
        {
            $timestamp = $resArray['TIMESTAMP'];
            $ts = strtotime($timestamp);
            $paid_date = date('Y-m-d H:i:s',$ts);
            
            $correlation_id = $resArray['CORRELATIONID'];
            
            $extra = json_decode($invoice['extra_json'],TRUE);
            if( !$extra )
            {
                $extra = array();
            }
            if( !isset($extra['payments']) )
            {
                $extra['payments'] = array();
            }
            $extra['payments'][] = array("correlation_id" => $correlation_id,
                                         "amount" => $to_pay_amount,
                                         "timestamp" => $timestamp,
                                         "method" => "paypal masspay",
                                         );
            $extra_json = json_encode($extra);
            
            $values = array("extra_json" => $extra_json,
                            "paid_amount" => $to_pay_amount + $paid_amount,
                            "paid_date" => $paid_date,
                            );
            
            //print "values: "; var_dump($values);
            
            mysql_update("artist_invoices",$values,'id',$invoice_id);
            
            $invoice = mf(mq("SELECT * FROM artist_invoices WHERE id='$invoice_id'"));
            
            $ret = array("success" => 1,
                         "invoice" => $invoice,
                         );
            echo json_encode($ret);
            die();
        }
        else
        {
            $ret = array("error" => "Masspay failed!");
            echo json_encode($ret);
            die();
        }
    }
    

?>