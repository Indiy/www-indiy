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
    
    $postedValues = array();
    
    function do_POST()
    {
        global $postedValues;
        
        $order_id = $_REQUEST["order_id"];
        $method = $_REQUEST["method"];
        
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
                $postedValues['failure'] = "1";
            else
                $postedValues['success'] = "1";
        }
        else
        {
            header("HTTP/1.0 400 Unknown method");
        }
        
        $postedValues['postedValues'] = $_REQUEST;
        echo json_encode($postedValues);
        exit();
    }
    
    function do_refund($order_id)
    {
        global $postedValues;

        $order = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
        
        $payment_info = json_decode($order['payment_json'],TRUE);
        $transation_id = $payment_info['transaction_id'];
        $resArray = CallRefundTransaction($transation_id);
        $ack = strtoupper($resArray["ACK"]);
        $postedValues['paypal_res'] = $resArray;

        if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING" )
        {
            $updates = array("state" => "CANCELED");
            mysql_update('orders',$updates,'id',$order_id);
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    function do_ship($order_id)
    {
        global $postedValues;

        $tracking_number = $_REQUEST['tracking_number'];
        $ship_time = strtotime($_REQUEST['ship_date']);
        if( $ship_time === FALSE )
            $ship_time = time();
        
        $ship_date = strftime("%F %T",$ship_time);

        $updates = array("state" => "SHIPPED",
                         "ship_date" => $ship_date,
                         );

        if( $tracking_number )
        {
            $order = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
            $shipping_info = json_decode($order['shipping_json'],TRUE);
            $shipping_info['tracking_number'] = $tracking_number;
            $shipping_json = json_encode($shipping_info);
            $updates['shipping_json'] = $shipping_json;
        }
        
        mysql_update('orders',$updates,'id',$order_id);
        $postedValues['ship_date'] = $ship_date;
        return FALSE;
    }
    
?>