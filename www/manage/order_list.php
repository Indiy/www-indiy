<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
	$artist_id = $_REQUEST['artist_id'];
    if( !$artist_id )
    {
        if( $_SESSION['sess_userType'] == 'ARTIST' )
        {
            $artist_id = $_SESSION['sess_userId'];
        }
        else
        {
            header("Location: dashboard.php");
            exit();
        }
    }

    $order_q = mq("SELECT * FROM orders WHERE artist_id='$artist_id'");

    $order_list_html = "";

    while( $order = mf($order_q) )
    {
        $id = $order['id'];
        $customer_name = $order['customer_name'];
        $customer_email = $order['customer_email'];
        
        $shippping_amount = $order['shipping_amount'];
        $charge_amount = $order['charge_amount'];
        $shipping_info = json_decode($order['shipping_json'],TRUE);
        $payment_info = json_decode($order['payment_json'],TRUE);
        
        if( $order_data['state'] == 'PENDING_CONFIRM' )
            $order_status = "Waiting For Customer Confirmation";
        else if( $order_data['state'] == 'PENDING_PAYMENT' )
            $order_status = "Payment Processing Pending";
        else if( $order_data['state'] == 'PENDING_SHIPMENT' )
            $order_status = "Waiting For Shipment";
        else if( $order_data['state'] == 'SHIPPED' )
            $order_status = "Shipped";
        else if( $order_data['state'] == 'CLOSED' )
            $order_status = "Closed";
        else if( $order_data['state'] == 'CANCELED' )
            $order_status = "Canceled";
        else if( $order_data['state'] == 'ABANDONED' )
            $order_status = "Order Abandoned";
        else
            $order_status = "Unknown";
    
        $html = "";
        $html .= "<a href='/order_status.html?order_id=$id'";
        $html .= " <div class='item'>";
        $html .= "  <div class='order_num'>$id</div>";
        $html .= "  <div class='name'>$customer_name</div>";
        $html .= "  <div class='email'>$customer_email</div>";
        $html .= "  <div class='status'>$order_status</div>";
        $html .= "  <div class='amount'>$charge_amount</div>";
        $html .= " </div>";
        $html .= "</a>";
    
        $order_list_html .= $html;
    }
    
    include_once "templates/order_list.html";

?>