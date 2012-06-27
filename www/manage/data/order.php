<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
    require_once '../../includes/paypalfunctions.php';
    
    if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
    session_write_close();
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
        do_POST();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
    
    function get_data($id)
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer_content WHERE id='$id'"));
        
        array_walk($row,cleanup_row_element);
        
        $image_path = "../artists/images/" . $row['image'];
        if( !empty($row['image']) )
            $row['image_url'] = $image_path;
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        return $row;
    }
    
    
    function do_POST()
    {
        $order_id = $_REQUEST["order_id"];
        $method = $_REQUEST["refund"];

        
        if( $method == "refund" )
        {
            if( do_refund($order_id) )
                $postedValues['failure'] = "1";
            else
                $postedValues['success'] = "1";
        }
        else if( $method == "ship" )
        {
            if( do_ship($order_id) )
            
        }
        
        $postedValues['postedValues'] = $_REQUEST;
        $postedValues['tab_data'] = get_data($tab_id);
        echo json_encode($postedValues);
        exit();
    }
    
    function do_refund($order_id)
    {
        $postedValues = array();
    
        $order = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
        
        $payment_info = json_decode($order['payment_json'],TRUE);
        $transation_id = $payment_info['transaction_id'];
        $resArray = CallRefundTransaction($transation_id);
        $ack = strtoupper($resArray["ACK"]);
        if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING" )
        {
            $updates = array("state" => "CANCELED");
            mysql_update('orders',$updates,'id',$order_id);
            return FALSE;
        }
        else
        {
            var_dump($resArray);
            return TRUE;
        }
    }
    
?>