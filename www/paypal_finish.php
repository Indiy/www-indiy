<?php

    require_once 'includes/functions.php';   
    require_once 'includes/config.php';
    require_once 'includes/paypalfunctions.php';
    
    $expire = time() + 60*24*60*60;
    $cookie_domain = str_replace("http://www.","",trueSiteUrl());
    setcookie("FAN_HAS_ORDERED","1",$expire,"/",$cookie_domain);
    
    $artist_id = $_REQUEST['artist_id'];
    
    $order_id = $_SESSION['in_process_order_id'];
    
    $order_data = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
    $order_json = json_encode($order_data);
    
    $shipping_amount = $order_data['shipping_amount'];
    $charge_amount = $order_data['charge_amount'];
    
    $fan_email = $order_data['customer_email'];
    
    
    $resArray = ConfirmPayment( $order_data['charge_amount'] );
    $ack = strtoupper($resArray["ACK"]);
    if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
    {
        $transaction_id = $resArray["PAYMENTINFO_0_TRANSACTIONID"]; 
        $transaction_type = $resArray["PAYMENTINFO_0_TRANSACTIONTYPE"];
        $payment_type = $resArray["PAYMENTINFO_0_PAYMENTTYPE"];
        $order_time = $resArray["PAYMENTINFO_0_ORDERTIME"];
        $amt = floatval( $resArray["PAYMENTINFO_0_AMT"]);
        $currency_code = $resArray["PAYMENTINFO_0_CURRENCYCODE"];
        $fee_amt = floatval($resArray["PAYMENTINFO_0_FEEAMT"]);
        $settle_amt = floatval($resArray["PAYMENTINFO_0_SETTLEAMT"]);
        $tax_amt = floatval($resArray["PAYMENTINFO_0_TAXAMT"]);
        $exchange_rate = $resArray["PAYMENTINFO_0_EXCHANGERATE"];
        $payment_status = strtoupper( $resArray["PAYMENTINFO_0_PAYMENTSTATUS"] ); 
        $pending_reason = strtoupper( $resArray["PAYMENTINFO_0_PENDINGREASON"] );  
        $reason_code = $resArray["PAYMENTINFO_0_REASONCODE"];
        
        if( $payment_status == "COMPLETED" )
            $state = "PENDING_SHIPMENT";
        else if( $payment_status == "PENDING" )
            $state = "PENDING_PAYMENT";

        $shipping_info = json_decode($order_data['shipping_json'],TRUE);
        $payment_info = json_decode($order_data['payment_json'],TRUE);
        
        $payment_info['transaction_id'] = $transaction_id;
        $payment_info['transaction_type'] = $transaction_type;
        $payment_info['payment_type'] = $payment_type;
        $payment_info['order_time'] = $order_time;
        $payment_info['amt'] = $amt;
        $payment_info['fee_amt'] = $fee_amt;
        $payment_info['settle_amt'] = $settle_amt;
        $payment_info['tax_amt'] = $tax_amt;
        $payment_info['payment_status'] = $payment_status;
        $payment_info['pending_reason'] = $pending_reason;
        
        $payment_json = json_encode($payment_info);
        
        $from_processor_amount = $amt - $fee_amt;
        $to_artist_amount = $from_processor_amount * ARTIST_PAYOUT_PERCENT;
        
        $time = strtotime($order_time);
        $order_date = strftime("%F %T",$time);
        
        $updates = array("tax_amount" => $tax_amt,
                         "from_processor_amount" => $from_processor_amount,
                         "to_artist_amount" => $to_artist_amount,
                         "state" => $state,
                         "payment_json" => $payment_json,
                         "order_date" => $order_date,
                         );

        mysql_update('orders',$updates,'id',$order_id);

        $_SESSION['in_process_order_id'] = FALSE;
        $_SESSION['cart_id'] = '';
        $_SESSION['paypal_token'] = FALSE;
        
        $contains_digital_items = FALSE;
        $all_digital = TRUE;
        
        $order_items = array();
        $order_item_html = "";
        
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
            
            if( $type == 'DIGITAL' )
                $contains_digital_items = TRUE;
            else
                $all_digital = FALSE;
            
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
            $html .= " <div class='name_description'>";
            $html .= "  <div class='name'>$name</div>";
            $html .= "  <div class='description'>$description</div>";
            $html .= " </div>";
            $html .= " <div class='price'>\$$price</div>";
            $html .= " <div class='action'>";
            if( $type == 'DIGITAL' )
            {
                $html .= "<a href='/fan'>";
                $html .= " <div class='download_button'>";
                $html .= "  <div class='icon'></div>";
                $html .= "  <div class='label'>Download</div>";
                $html .= " </div>";
                $html .= "</a>";
            }
            $html .= " </div>";
            $html .= "</div>";
            
            $order_item_html .= $html;
        }
        
        $fan_needs_register = TRUE;
        $register_token = FALSE;
        
        $fan_data = mf(mq("SELECT * FROM fans WHERE email='$fan_email'"));
        if( $fan_data )
        {
            if( strlen($fan_data['password']) > 0 )
            {
                $fan_needs_register = FALSE;
            }
            else
            {
                $register_token = random_string(32);
                $fan_data['register_token'] = $register_token;
                $updates = array("register_token" => $register_token);
                mysql_update('fans',$updates,'id',$fan_data['id']);
            }
        }
        else
        {
            $register_token = random_string(32);
            $fan_data = array("email" => $fan_email,
                              "register_token" => $register_token,
                              );
            mysql_insert('fans',$fan_data);
            $fan_data['id'] = mysql_insert_id();
        }
        $fan_id = $fan_data['id'];
        $_SESSION['fan_id'] = $fan_id;
        
        if( $contains_digital_items )
        {
            for( $i = 0 ; $i < count($order_items) ; $i++ )
            {
                $order_item = $order_items[$i];
                $product_id = $order_item['product_id'];
                $product = get_product_data($product_id);
                $digital_downloads = $product['digital_downloads'];
                for( $j = 0 ; $j < count($digital_downloads) ; ++$j )
                {
                    $download = $digital_downloads[$j];
                    $product_file_id = $download['id'];
                    $inserts = array("fan_id" => $fan_id,
                                     "product_file_id" => $product_file_id,
                                     );
                    mysql_insert('fan_files',$inserts);
                }
            }
            if( $all_digital )
            {
                $updates = array("state" => "CLOSED");
                mysql_update('orders',$updates,'id',$order_id);
            }
        }
        
        $shipping_info = json_decode($shipping_info,TRUE);
        $payment_info = json_decode($payment_info,TRUE);

        include_once 'templates/finish_order.html';
        
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));

        $to = $artist['email'];
        $subject = "Order Made on MyArtistDNA";
        $message = <<<END
Your store on MyArtistDNA just got an order.  Login to your admin portal to see details on the order.

http://www.myartistdna.com/manage

Be Heard. Be Seen. Be Independent.
        
END;

        $from = "no-reply@myartistdna.com";
        $headers = "From:" . $from;
        mail($to,$subject,$message,$headers);
    }
    else  
    {
        //Display a user friendly Error on the page using any of the following error information returned by PayPal
        $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
        $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
        $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
        $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
        
        echo "<html>";
        echo "<body>";
        echo "<h1>Checkout Failed</h1>";
        echo "<pre>";
        
        echo "GetExpressCheckoutDetails API call failed.\n";
        echo "Detailed Error Message: $ErrorLongMsg\n";
        echo "Short Error Message: $ErrorShortMsg\n";
        echo "Error Code: $ErrorCode\n";
        echo "Error Severity Code: $ErrorSeverityCode\n";
    }
?>