<?php

    require_once 'includes/functions.php';   
    require_once 'includes/config.php';
    require_once 'includes/paypalfunctions.php';
    
    
    $order_id = $_SESSION['in_process_order_id'];
    
    $order_data = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
    $order_json = json_encode($order_data);
    
    $shippping_amount = $order_data['shipping_amount'];
    $charge_amount = $order_data['charge_amount'];
    
    
    $resArray = ConfirmPayment( $order_data['charge_amount'] );
    $ack = strtoupper($resArray["ACK"]);
    if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
    {
        $transaction_id = $resArray["PAYMENTINFO_0_TRANSACTIONID"]; 
        $transaction_type = $resArray["PAYMENTINFO_0_TRANSACTIONTYPE"];
        $payment_type = $resArray["PAYMENTINFO_0_PAYMENTTYPE"];
        $order_time 	= $resArray["PAYMENTINFO_0_ORDERTIME"];
        $amt = $resArray["PAYMENTINFO_0_AMT"];
        $currency_code = $resArray["PAYMENTINFO_0_CURRENCYCODE"];
        $fee_amt = $resArray["PAYMENTINFO_0_FEEAMT"];
        $settle_amt = $resArray["PAYMENTINFO_0_SETTLEAMT"];
        $tax_amt = $resArray["PAYMENTINFO_0_TAXAMT"];
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
        $payment_info['payment_status'] = $payment_status;
        $payment_info['pending_reason'] = $pending_reason;
        
        $payment_json = json_encode($payment_info);
        
        $updates = array("tax_amount" => $tax_amt,
                         "from_processor_amount" => $settle_amt,
                         "to_artist_amount" => $settle_amt * ARTIST_PAYOUT_PERCENT,
                         "state" => $state,
                         "payment_json" => $payment_json,
                         );

        mysql_update('orders',$updates,'id',$order_id);

        $_SESSION['in_process_order_id'] = FALSE;
        $_SESSION['cart_id'] = '';
        $_SESSION['paypal_token'] = FALSE;
        
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
        
        include_once 'templates/finish_order.php';
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