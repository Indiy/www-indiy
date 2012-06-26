<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
    $order_id = $_REQUEST['order_id'];
    
    $order = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
    
    $id = $order['id'];
    $customer_name = $order['customer_name'];
    $customer_email = $order['customer_email'];
    
    $shippping_amount = $order['shipping_amount'];
    $charge_amount = $order['charge_amount'];
    $shipping_info = json_decode($order['shipping_json'],TRUE);
    $payment_info = json_decode($order['payment_json'],TRUE);
    
    if( $order['state'] == 'PENDING_CONFIRM' )
        $order_status = "Waiting For Customer Confirmation";
    else if( $order['state'] == 'PENDING_PAYMENT' )
        $order_status = "Payment Processing Pending";
    else if( $order['state'] == 'PENDING_SHIPMENT' )
        $order_status = "Waiting For Shipment";
    else if( $order['state'] == 'SHIPPED' )
        $order_status = "Shipped";
    else if( $order['state'] == 'CLOSED' )
        $order_status = "Closed";
    else if( $order['state'] == 'CANCELED' )
        $order_status = "Canceled";
    else if( $order['state'] == 'ABANDONED' )
        $order_status = "Order Abandoned";
    else
        $order_status = "Unknown";
    
    include_once "templates/edit_order.html";

?>