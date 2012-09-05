<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    require_once 'includes/paypal_adaptive.php';

    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    print "<html><pre>\n\n";

    if( isset($_REQUEST['cancel']) )
    {
        print "Cancel endpoint\n";
        die("Done done");
    }
    if( isset($_REQUEST['return']) )
    {
        print "Return endpoint\n";
        die("Done done");
    }
    if( isset($_REQUEST['transaction_id']) )
    {
        $transaction_id = $_REQUEST['transaction_id'];
        
        $info = paypal_get_transaction_info($transaction_id);
        
        print "transaction_info: \n";
        var_dump($info);
        
        die("Done done");
    }

    
    $extra_args = array("requestEnvelope.errorLanguage" => "en_US",
                        "actionType" => "PAY",
                        "receiverList.receiver(0).email" => "mad_1346558535_biz@myartistdna.com",
                        "receiverList.receiver(0).amount" => "100.00",
                        "currencyCode" => "USD",
                        "feesPayer" => "EACHRECEIVER",
                        "cancelUrl" => trueSiteUrl() . "/debug.php?cancel=1",
                        "returnUrl" => trueSiteUrl() . "/debug.php?return=1",
                        "ipnNotificationUrl" => trueSiteUrl() . "/data/paypal_ipn.php?order_id=42",
                        );

    print "extra_args:\n";
    var_dump($extra_args);

    print "paypal_checkout done ========================\n";
    
    $ret = paypal_get_paykey($extra_args);

    print "\n\n";
    print "paypal_checkout done ========================\n";
    
    print "ret:\n";
    var_dump($ret);
    
    $pay_status = $ret['paymentExecStatus'];
    
    if( $pay_status == 'CREATED' )
    {
        $pay_key = $ret['payKey'];
        
        $args = array("payKey" => $pay_key,
                      "requestEnvelope.errorLanguage" => "en_US",
                      "displayOptions.businessName" => "Jim Lake - MyArtistDNA Store",
                      "senderOptions.requireShippingAddressSelection" => "Jim Lake - MyArtistDNA Store",
                      "receiverOptions[0].invoiceData.totalTax" => 0,
                      "receiverOptions[0].invoiceData.totalShipping" => 25.0,
                      "receiverOptions[0].invoiceData.item[0].name" => "MAD Single",
                      "receiverOptions[0].invoiceData.item[0].price" => 25.0,
                      "receiverOptions[0].invoiceData.item[0].itemCount" => 1,
                      "receiverOptions[0].invoiceData.item[0].itemPrice" => 25.0,
                      "receiverOptions[0].invoiceData.item[1].name" => "MAD T-Shirts, cheap ones",
                      "receiverOptions[0].invoiceData.item[1].price" => 50.0,
                      "receiverOptions[0].invoiceData.item[1].itemCount" => 5,
                      "receiverOptions[0].invoiceData.item[1].itemPrice" => 50.0,
                      );
        
        paypal_set_payment_options($args);
        
        $url = paypal_get_url($pay_key);
        
        print "</pre>";
        print "<br/>";
        print "<a href='$url'>Pay URL: $url</a><br/>";
        print "<br/>";
        print "<pre>\n";
    }
    else
    {
        print "Error, status: $pay_status\n";
    }
    
    
    print "done done";

?>