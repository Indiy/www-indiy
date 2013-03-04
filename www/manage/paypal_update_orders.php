<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	require_once '../includes/paypalfunctions.php';

    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}

    echo "<html><pre>";

    $sql = "SELECT * FROM orders WHERE charge_amount > 0.0 AND from_processor_amount = 0.0";
    $sql = "SELECT * FROM orders WHERE 1";
    $order_q = mq($sql);
    while( $order = mf($order_q) )
    {
        $id = $order['id'];
        $state = $order['state'];
    
        print "Order Number: $id\n";
        print " State: $state\n\n";


        if( $state == 'CANCELED' )
        {
            $order_time = strtotime($res['ORDERTIME']);
            $order_date = strftime("%F %T",$order_time);
            print "Order Date: $order_date\n";

            $updates = array("from_processor_amount" => 0.0,
                             "to_artist_amount" => 0.0,
                             "order_date" => $order_date,
                             );
            var_dump($updates);
            $success = mysql_update('orders',$updates,'id',$id);
            print " Update success: $success\n";
        }
        else if( $state == 'PENDING_CONFIRM' )
        {
            
        }
        else if( $state == 'PENDING_PAYMENT' 
                || $state == 'PENDING_SHIPMENT' 
                || $state == 'SHIPPED'
                || $state == 'CLOSED'
                )
        {
            
            $paypal_json = $order['payment_json'];
            $paypal_info = json_decode($paypal_json,TRUE);
            
            $transaction_id = $paypal_info['transaction_id'];
            
            if( !$transaction_id )
                continue;
            
            $res = CallGetTransactionDetails($transaction_id);
            
            $order_time = strtotime($res['ORDERTIME']);
            $order_date = strftime("%F %T",$order_time);
            print "Order Date: $order_date\n";
            //continue;
            
            $fee_amount = floatval($res['FEEAMT']);
            $charge_amount = floatval($res['AMT']);
            $tax_amount = floatval($res['TAXAMT']);

            $from_processor_amount = $charge_amount - $fee_amount;
            $to_artist_amount = $from_processor_amount * ARTIST_PAYOUT_PERCENT;

            $updates = array("charge_amount" => $charge_amount,
                             "tax_amount" => $tax_amount,
                             "from_processor_amount" => $from_processor_amount,
                             "to_artist_amount" => $to_artist_amount,
                             "order_date" => $order_date,
                             );
            
            var_dump($updates);
            
            $success = mysql_update('orders',$updates,'id',$id);
            
            print " Update success: $success\n";
        }
        else if( $state == 'ABANDONED' )
        {
            
        }
        print "\n\n=============================\n\n";
    }

?>