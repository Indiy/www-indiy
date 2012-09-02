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
    $artist_email = $artist_data['email'];
    
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

    $artist_amt = round($payment_amount * ARTIST_PAYOUT_PERCENT,2);
    $mad_amt = $payment_amount - $artist_amt;
    
    $extra_args = array(
                        "BRANDNAME" => "$artist_name - MyArtistDNA Store",
                        
                        //"CUSTOMERSERVICENUMBER" => "347-775-5638",
                        //"PAYMENTREQUEST_0_ITEMAMT" => $sub_total,
                        //"PAYMENTREQUEST_0_SHIPPINGAMT" => $shipping_total,

                        "RETURNURL" => $returnURL,
                        "CANCELURL" => $cancelURL,
                        
                        "PAYMENTACTION" => "Order",
                        
                        "PAYMENTREQUEST_0_CURRENCYCODE" => "USD",
                        "PAYMENTREQUEST_0_AMT" => $mad_amt,
                        //"PAYMENTREQUEST_0_ITEMAMT" => $mad_amt,
                        //"PAYMENTREQUEST_0_TAXAMT" => "0",
                        //"PAYMENTREQUEST_0_DESC" => "Summer Vacation trip",
                        //"PAYMENTREQUEST_0_INSURANCEAMT" => "0",
                        //"PAYMENTREQUEST_0_SHIPDISCAMT" => "0",
                        "PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID" => "mad_1346558535_biz@myartistdna.com",
                        //"PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED" => "false",
                        "PAYMENTREQUEST_0_PAYMENTACTION" => "Order",
                        "PAYMENTREQUEST_0_PAYMENTREQUESTID" => "MAD$order_id-PAYMENT0",
                        
                        "PAYMENTREQUEST_1_CURRENCYCODE" => "USD",
                        "PAYMENTREQUEST_1_AMT" => $artist_amt,
                        //"PAYMENTREQUEST_1_ITEMAMT" =>  $artist_amt,
                        //"PAYMENTREQUEST_1_SHIPPINGAMT" => "0",
                        //"PAYMENTREQUEST_1_HANDLINGAMT" => "0",
                        //"PAYMENTREQUEST_1_TAXAMT" => "0",
                        //"PAYMENTREQUEST_1_DESC" => "Summer Vacation trip",
                        //"PAYMENTREQUEST_1_INSURANCEAMT" => "0",
                        //"PAYMENTREQUEST_1_SHIPDISCAMT" => "0",
                        "PAYMENTREQUEST_1_SELLERPAYPALACCOUNTID" => "artist_1346622743_per@myartistdna.com",
                        //"PAYMENTREQUEST_1_INSURANCEOPTIONOFFERED" => "false",
                        "PAYMENTREQUEST_1_PAYMENTACTION" => "Order",
                        "PAYMENTREQUEST_1_PAYMENTREQUESTID" => "MAD$order_id-PAYMENT1",
/*
                        "L_PAYMENTREQUEST_0_NAME0" => "Depart San Jose Feb 12 at 12:10PM Arrive in Baltimore at 10:22PM",
                        "L_PAYMENTREQUEST_0_NUMBER0" => "0",
                        "L_PAYMENTREQUEST_0_QTY0" => "1",
                        "L_PAYMENTREQUEST_0_TAXAMT0" => "0",
                        "L_PAYMENTREQUEST_0_AMT0" => $mad_amt,
                        "L_PAYMENTREQUEST_0_DESC0" => "SJC Terminal 1. Flight time: 7 hours 12 minutes",

                        "L_PAYMENTREQUEST_1_NAME0" => "Night(s) stay at 9990 Deereco Road, Timonium, MD 21093",
                        "L_PAYMENTREQUEST_1_NUMBER0" => "1",
                        "L_PAYMENTREQUEST_1_QTY0" => "1",
                        "L_PAYMENTREQUEST_1_TAXAMT0" => "0",
                        "L_PAYMENTREQUEST_1_AMT0" => $artist_amt,
                        "L_PAYMENTREQUEST_1_DESC0" => "King No-Smoking; Check in after 4:00 PM; Check out by 1:00 PM",
*/
                        );

    //$extra_args = array_merge($extra_args,$order_item_args);
    
    //$resArray = CallShortcutExpressCheckout($payment_amount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $extra_args);
    $resArray = SimpleCallShortcutExpressCheckout($extra_args);
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
                     "resArray" => $resArray,
                     "returnURL" => $returnURL,
                     "extra_args" => $extra_args,
                     "shipping_total" => $shipping_total,
                     "payment_amount" => $payment_amount,
                     );
        echo json_encode($ret);
        die();
    }
    
?>