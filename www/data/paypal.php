<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    require_once '../includes/functions.php';   
    require_once '../includes/config.php';
    require_once '../includes/paypalfunctions.php';
    
    if( $_SESSION['cart_id'] == '' )	
        $_SESSION['cart_id'] = rand(1111111,9999999);
    //session_write_close();
    
    $cart_id = $_SESSION['cart_id'];
    $artist_id = $_GET['artist_id'];
    
    $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
    $artist_name = $artist_data['artist'];
    
    $cart = store_get_cart($artist_id,$cart_id);
    
    $payment_amount = 0.0;
    $subTotal = 0.0;
    $shipping_total = 0.0;
    for( $i = 0 ; $i < count($cart) ; $i++ )
    {
        $c = $cart[$i];
        $qty = floatval($c['quantity']);
        $price = floatval($c['price']);
        $shipping = floatval($c['shipping']);
        $sub_total += $qty * $price;
        $shipping_total += $qty * $shipping;
    }
    $payment_amount = $shipping_total + $sub_total;
    
    $order = array("artist_id" => $artist_id,
                   "created_date" => date("Y-m-d H:i:s"),
                   "state" => "PENDING_CONFIRM",
                   "shipping_amount" => $shipping_total,
                   "charge_amount" => $payment_amount,
                   );
    
    if( !mysql_insert('orders',$order) )
        die("Error in order processing");
    
    $order_id = mysql_insert_id();
    
    $order_item_args = array();

    for( $i = 0 ; $i < count($cart) ; ++$i )
    {
        $c = $cart[$i];
        $color = $c['color'];
        $size = $c['size'];
        $price = $c['price'];
        $name = $c['name'];
        $qty = $c['quantity'];

        $desc_extra = "";
        if( strlen($color) > 0 )
        {
            $desc_extra .= $color;
        }
        if( strlen($size) > 0 )
        {
            if( strlen($desc_extra) > 0 )
                $desc_extra .= " - ";
            $desc_extra .= $size;
        }
        $description = $name;
        if( strlen($desc_extra) > 0 )
            $description .= " - $desc_extra";
        
        $order_item = array("order_id" => $order_id,
                            "quantity" => $qty,
                            "product_id" => $c['product_id'],
                            "color" => $color,
                            "size" => $size,
                            "description" => $description,
                            "price" => $price,
                            );
        $ret = mysql_insert('order_items',$order_item);
        if( !$ret )
        {
            print "Failure: " . mysql_error();
        }
        
        $order_item_args["L_PAYMENTREQUEST_0_NAME$i"] = $name;
        $order_item_args["L_PAYMENTREQUEST_0_AMT$i"] = $price;
        $order_item_args["L_PAYMENTREQUEST_0_QTY$i"] = $qty;
        if( strlen($desc_extra) > 0 )
            $order_item_args["L_PAYMENTREQUEST_0_DESC$i"] = $desc_extra;
    }
    
    $_SESSION['in_process_order_id'] = $order_id;
    $_SESSION['paypal_token'] = FALSE;

    $currencyCodeType = "USD";
    $paymentType = "Sale";
    
    $http_host = $_SERVER["HTTP_HOST"];

    $returnURL = "http://$http_host/paypal_order_confirm.php?artist_id=$artist_id";
    $cancelURL = "http://$http_host/cart.php?artist_id=$artist_id&abandon_order=1";
    
    $extra_args = array("BRANDNAME" => "$artist_name - MyArtistDNA Store",
                        "CUSTOMERSERVICENUMBER" => "347-775-5638",
                        "PAYMENTREQUEST_0_ITEMAMT" => $sub_total,
                        "PAYMENTREQUEST_0_SHIPPINGAMT" => $shipping_total,
                        );
                        
    $extra_args = array_merge($extra_args,$order_item_args);
    
    $resArray = CallShortcutExpressCheckout($payment_amount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $extra_args);
    $ack = strtoupper($resArray["ACK"]);
    if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
    {
        $token = $resArray["TOKEN"];
        global $PAYPAL_URL;
		$payPalURL = $PAYPAL_URL . $token;
        $ret = array("success" => 1,
                     "url" => $payPalURL,
                     );
        echo json_encode($ret);
        die();
    } 
    else  
    {
        //Display a user friendly Error on the page using any of the following error information returned by PayPal
        $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
        $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
        $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
        $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
        
        $ret = array("failure" => 1,
                     "ErrorCode" => $ErrorCode,
                     "ErrorShortMsg" => $ErrorShortMsg,
                     "ErrorLongMsg" => $ErrorLongMsg,
                     "ErrorSeverityCode" => $ErrorSeverityCode,
                     "returnURL" => $returnURL,
                     "extra_args" => $extra_args,
                     "shipping_total" => $shipping_total,
                     "payment_amount" => $payment_amount,
                     );
        echo json_encode($ret);
        die();
    }
    
?>