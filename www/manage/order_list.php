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

    $i = 0;
    $total_sold = 0.0;

    while( $order = mf($order_q) )
    {
        $id = $order['id'];
        $customer_name = $order['customer_name'];
        $customer_email = strtolower($order['customer_email']);
        
        $shippping_amount = $order['shipping_amount'];
        $charge_amount = $order['charge_amount'];
        $shipping_info = json_decode($order['shipping_json'],TRUE);
        $payment_info = json_decode($order['payment_json'],TRUE);
        
        if( $order['state'] == 'PENDING_CONFIRM' )
        {
            $order_status = "waiting for customer confirmation";
            continue;
        }
        else if( $order['state'] == 'PENDING_PAYMENT' )
        {
            $order_status = "Payment Processing Pending";
            continue;
        }
        else if( $order['state'] == 'PENDING_SHIPMENT' )
        {
            $order_status = "waiting for shipment";
        }
        else if( $order['state'] == 'SHIPPED' )
        {
            $order_status = "shipped";
        }
        else if( $order['state'] == 'CLOSED' )
        {
            $order_status = "complete";
        }
        else if( $order['state'] == 'CANCELED' )
        {
            $order_status = "canceled";
        }
        else if( $order['state'] == 'ABANDONED' )
        {
            $order_status = "order abandoned";
            continue;
        }
        else
        {
            $order_status = "unknown";
        }
    
        $order_date = strftime("%D",strtotime($order['order_date']));

        $odd = "";
        if( $i % 2 == 0 )
            $odd = " odd";
    
        $html = "";
        $html .= "<a href='edit_order.php?order_id=$id'>";
        $html .= " <div class='item$odd'>";
        $html .= "  <div class='date'>$order_date</div>";
        $html .= "  <div class='order_num'>$id</div>";
        $html .= "  <div class='name'>$customer_name</div>";
        $html .= "  <div class='email'>$customer_email</div>";
        $html .= "  <div class='status'>$order_status</div>";
        $html .= "  <div class='amount'>$charge_amount</div>";
        $html .= " </div>";
        $html .= "</a>";
    
        $order_list_html .= $html;
        
        if( $order['state'] != 'CANCELED' )
        {
            $total_sold += $charge_amount;
        }
        $i++;
    }
    
    $total_orders = $i;
    $total_sold = number_format($total_sold,2);
    
    $include_order = FALSE;
    $include_editor = FALSE;
    
    $artist_edit_url = "/manage/artist_management.php?userId=$artist_id";
    
    include_once "templates/order_list.html";

?>