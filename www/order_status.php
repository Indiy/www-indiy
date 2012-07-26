<?php

    require_once 'includes/functions.php';   
    require_once 'includes/config.php';
    require_once 'includes/paypalfunctions.php';
    
    
    $order_id = $_REQUEST['order_id'];
    
    if( !$order_id )
    {
        include_once 'templates/order_status.html';
        die();
    }
    
    $order_data = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
    
    if( !$order_data )
    {
        die("Unknown order id");
    }
    
    $order_json = json_encode($order_data);
    
    $shippping_amount = $order_data['shipping_amount'];
    $charge_amount = $order_data['charge_amount'];
    $shipping_info = json_decode($order_data['shipping_json'],TRUE);
    $payment_info = json_decode($order_data['payment_json'],TRUE);
    
    $order_email = $order_data['email'];

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

    $order_items = array();
    $order_item_html = "";
    
    $q_order_items = mq("SELECT * FROM order_items WHERE order_id='$order_id'");
    $i = 0;
    while( $item = mf($q_order_items) )
    {
        $description = $item['description'];
        $color = $item['color'];
        $size = $item['size'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $order_item = array("quantity" => $quantity,
                            "description" => $description,
                            "product_id" => $item['product_id'],
                            "color" => $color,
                            "size" => $size);
        $num = $i + 1;
        $html = "";
        $html .= "<div class='item'>";
        $html .= " <div class='num'>$num</div>";
        $html .= " <div class='description'>$description</div>";
        $html .= " <div class='price'>$price</div>";
        $html .= " <div class='quantity'>$quantity</div>";
        $html .= "</div>";
        
        $order_item_html .= $html;
        
        $i++;
    }
    
    include_once 'templates/order_details.html';

?>