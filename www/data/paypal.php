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
    
    $cart = store_get_cart();
    
    $payment_amount = 0.0;
    $subTotal = 0.0;
    $shipping_total = 0.0;
    for( $i = 0 ; $i < count($cart) ; $i++ )
    {
        $c = $cart[$i];
        $qty = $c['quantity'];
        $sub_total += $qty * $c['price'];
        $shipping_total += $qty * $c['shipping'];
    }
    $payment_amount = $shipping_total + $sub_total;
    
    $order = array("artist_id" => $artist_id,
                   "created_date" => date("Y-m-d H:i:s"),
                   "state" => "PENDING_PAYMENT",
                   "shipping_amount" => $shipping_total,
                   "charge_amount" => $payment_amount
                   );
    
    if( !mysql_insert('orders',$order) )
        die("Error in order processing");
    
    $order_id = mysql_insert_id();


    for( $i = 0 ; $i < count($cart) ; ++$i )
    {
        $c = $cart[$i];
        $color = $c['color'];
        $size = $c['size'];
        $description = $c['name'];
        if( strlen($color) > 0 )
            $description .= " - $color";
        if( strlen($size) > 0 )
            $description .= " - $size";
        
        $order_item = array("order_id" => $order_id,
                            "quantity" => $c['quantity'],
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
    }
    
    $_SESSION['in_process_order_id'] = $order_id;
    $_SESSION['paypal_token'] = FALSE;

    $currencyCodeType = "USD";
    $paymentType = "Sale";
    
    $http_host = $_SERVER["HTTP_HOST"];

    $returnURL = "http://$http_host/paypal_order_confirm.php";
    $cancelURL = "http://$http_host/cart.php?abandon_order=1";
    
    $resArray = CallShortcutExpressCheckout($payment_amount, $currencyCodeType, $paymentType, $returnURL, $cancelURL);
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
                     );
        echo json_encode($ret);
        die();
    }
    
?>