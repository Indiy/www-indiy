<?php

    require_once 'includes/functions.php';   
    require_once 'includes/config.php';
    require_once 'includes/paypalfunctions.php';

    $token = "";
    if( isset($_REQUEST['token']) )
    {
        $token = $_REQUEST['token'];
    }
    
    if( $token == "" )
    {
        header("Location: /cart.php?abandon_order=1");
        die();
    }
    
    $_SESSION['paypal_token'] = $token;
    
    $order_id = $_SESSION['in_process_order_id'];
    
    $resArray = GetShippingDetails( $token );
    $ack = strtoupper($resArray["ACK"]);
    if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING" ) 
    {
        $paypal_info = array();
        $shipping_info = array();
        
        $customer_email = $resArray["EMAIL"]; 
        $paypal_info["PAYERID"] = $resArray["PAYERID"]; 
        $paypal_info["PAYERSTATUS"] = $resArray["PAYERSTATUS"];
        $salutation = $resArray["SALUTATION"];
        $first_name = $resArray["FIRSTNAME"];
        $middle_name = $resArray["MIDDLENAME"];
        $last_name = $resArray["LASTNAME"];
        $suffix = $resArray["SUFFIX"];

        $customer_name = "$firstName $middleName $lastName";

        $cntryCode = $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
        $business = $resArray["BUSINESS"]; // ' Payer's business name.
        
        $shipping_info['name'] = $resArray["PAYMENTREQUEST_0_SHIPTONAME"]; 
        $shipping_info['street1'] = $resArray["PAYMENTREQUEST_0_SHIPTOSTREET"];
        $shipping_info['street2'] = $resArray["PAYMENTREQUEST_0_SHIPTOSTREET2"]; 
        $shipping_info['city'] = $resArray["PAYMENTREQUEST_0_SHIPTOCITY"]; 
        $shipping_info['state'] = $resArray["PAYMENTREQUEST_0_SHIPTOSTATE"];
        $shipping_info['country_code'] = $resArray["PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE"];
        $shipping_info['zip'] = $resArray["PAYMENTREQUEST_0_SHIPTOZIP"];
        $shipping_info['paypal_address_status'] = $resArray["ADDRESSSTATUS"];
        $shipping_info['phone_number'] =  $resArray["PHONENUM"];
        
        //$invoiceNumber = $resArray["INVNUM"];

        $shipping_json = json_encode($shipping_info);
        $paypal_json = json_encode($paypal_info);
        
        $updates = array("customer_name" => $customer_name,
                         "customer_email" => $customer_email,
                         "shipping_json" => $shipping_json,
                         "payment_type" => "PAYPAL",
                         "payment_json" => $paypal_json,
                         );
        mysql_update('orders',$updates,'id',$order_id);
        
        $order_data = mf(mq("SELECT * FROM orders WHERE id='$order_id'"));
        $order_json = json_encode($order_data);

        $shippping_amount = $order_data['shipping_amount'];
        $charge_amount = $order_data['charge_amount'];

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
        
        include_once 'templates/confirm_order.html';
    }
    else  
    {
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