<?php

    require_once 'includes/functions.php';   
    require_once 'includes/config.php';
    require_once 'includes/paypalfunctions.php';
    
    
    $order_id = $_REQUEST['order_id'];
    
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

    $order_items = array();
    $order_item_html = "";
    
    $q_order_items = mq("SELECT * FROM order_items WHERE order_id='$order_id'");
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
        $html = "";
        $html .= "<div class='item'>";
        $html .= " <div class='num'>$i</div>";
        $html .= " <div class='description'>$description</div>";
        $html .= " <div class='price'>$price</div>";
        $html .= " <div class='quantity'>$quantity</div>";
        $html .= "</div>";
        
        $order_item_html .= $html;
    }
    
    include_once 'templates/order_status.html';

?>