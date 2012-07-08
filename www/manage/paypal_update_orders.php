<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	require_once '../includes/paypalfunctions.php';
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}


    $order_q = mq("SELECT * FROM orders WHERE charge_amount > 0.0 AND from_processor_amount == 0.0");
    while( $order = mf($order_q) )
    {
        $paypal_json = $order['payment_json'];
        $paypal_info = json_decode($paypal_json,TRUE);
        
        $transaction_id = $paypal_info['transaction_id'];
        
        if( !$transaction_id )
            continue;
        
        $res = CallGetTransactionDetails($transaction_id);
        
        var_dump($res);
        
        break;
    }

?>