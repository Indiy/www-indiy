<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	require_once '../includes/paypalfunctions.php';
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}

    echo "<html><pre>";

    $order_q = mq("SELECT * FROM orders WHERE charge_amount > 0.0 AND from_processor_amount = 0.0");
    while( $order = mf($order_q) )
    {
        $id = $order['id'];
        $state = $order['state'];
    
        print "Order Number: $id\n";
        print " State: $state\n\n";


        if( $state == 'CANCELED' )
        {
            $updates = array("from_processor_amount" => 0.0,
                             "to_artist_amount" => 0.0,
                             );
            var_dump($updates);
            //mysql_update('orders',$updates,'id',$id);
        }
        else if( $state == 'PENDING_CONFIRM' )
        {
            
        }
        else if( $state == 'PENDING_PAYMENT' 
                || $state == 'PENDING_SHIPMENT' 
                || $state == 'SHIPPED' 
                )
        {
            
            $paypal_json = $order['payment_json'];
            $paypal_info = json_decode($paypal_json,TRUE);
            
            $transaction_id = $paypal_info['transaction_id'];
            
            if( !$transaction_id )
                continue;
            
            $res = CallGetTransactionDetails($transaction_id);
            
            $fee_amount = floatval($res['FEEAMT']);
            $charge_amount = floatval($res['AMT']);
            $tax_amount = floatval($res['TAXAMT']);

            $from_processor_amount = $charge_amount - $fee_amount;
            $to_artist_amount = $from_processor_amount * ARTIST_PAYOUT_PERCENT;

            $updates = array("fee_amount" => $fee_amount,
                             "charge_amount" => $charge_amount,
                             "tax_amount" => $tax_amount,
                             "from_processor_amount" => $from_processor_amount,
                             "to_artist_amount" => $to_artist_amount,
                             );
            
            var_dump($updates);
            
            print "\n\n=============================\n\n";
            
        }
        else if( $state == 'CLOSED' )
        {
            
        }
        else if( $state == 'ABANDONED' )
        {
            
        }
    }

?>