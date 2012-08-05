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
    
    $shipping_amount = $order_data['shipping_amount'];
    $charge_amount = $order_data['charge_amount'];
    $shipping_info = json_decode($order_data['shipping_json'],TRUE);
    $payment_info = json_decode($order_data['payment_json'],TRUE);
    
    $order_email = $order_data['customer_email'];
    $order_date = strftime("%m-%d-%Y",strtotime($order_data['order_date']));

    if( $order_data['state'] == 'PENDING_CONFIRM' )
        $order_status = "Waiting For Customer Confirmation";
    else if( $order_data['state'] == 'PENDING_PAYMENT' )
        $order_status = "Payment Processing Pending";
    else if( $order_data['state'] == 'PENDING_SHIPMENT' )
        $order_status = "Waiting For Shipment";
    else if( $order_data['state'] == 'SHIPPED' )
        $order_status = "Shipped";
    else if( $order_data['state'] == 'CLOSED' )
        $order_status = "Complete";
    else if( $order_data['state'] == 'CANCELED' )
        $order_status = "Canceled";
    else if( $order_data['state'] == 'ABANDONED' )
        $order_status = "Order Abandoned";
    else
        $order_status = "Unknown";

    $order_items = array();
    $order_item_html = "";
    $order_items = store_get_order($order_id);
    
    foreach( $order_items as $i => $item )
    {
        $name = $item['name'];
        $description = $item['description'];
        $color = $item['color'];
        $size = $item['size'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $image = $item['image'];
        $type = $item['type'];
        
        $num = $i + 1;
        
        if( $i % 2 == 0 )
            $odd = " odd";
        else
            $odd = "";
        
        $html = "";
        $html .= "<div class='item$odd'>";
        $html .= " <div class='quantity'>$quantity</div>";
        $html .= " <div class='image'>";
        $html .= "  <div class='image_holder'><img src='$image'></div>";
        $html .= " </div>";
        $html .= " <div class='name_description wide'>";
        $html .= "  <div class='name'>$name</div>";
        $html .= "  <div class='description'>$description</div>";
        $html .= " </div>";
        $html .= " <div class='price'>\$$price</div>";
        $html .= "</div>";
        
        $order_item_html .= $html;
    }
    
    $tracking_number = FALSE;
    if( isset( $shipping_info['tracking_number'] ) )
        $tracking_number = $shipping_info['tracking_number'];
    
    include_once 'templates/order_details.html';

?>