<?php

    $PAYPAL_API_VERSION = "64";

    $PAYPAL_USERNAME = "mad_1346558535_biz_api1.myartistdna.com";
    $PAYPAL_PASSWORD = "1346558558";
    $PAYPAL_SIGNATURE = "Ab.Ua9MmJioLkDJWgEubbcrQ8dONA9x1bbDIhJetM9P6ktHGYZ6AK3D-";
    $PAYPAL_APPID = "APP-80W284485P519543T";

    $PAYPAL_HASH_API_ENDPOINT = "https://api-3t.sandbox.paypal.com/nvp";
    $PAYPAL_PAY_API_ENDPOINT = "https://svcs.sandbox.paypal.com/AdaptivePayments/Pay";
    
    //$PAYPAL_URL = "https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay?paykey=";
    $PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=";
    
    
    function paypal_get_transaction_info($transaction_id)
    {
        $args = array("METHOD" => "GetTransactionDetails",
                      "TRANSACTIONID" => $transaction_id,
                      );
        return paypal_hash_call($args);
    }
    
    function paypal_checkout($extra_args)
    {
        return paypal_app_header_call($PAYPAL_PAY_API_ENDPOINT,$extra_args);
    }

    function paypal_hash_call($nvp_array)
    {
        global $PAYPAL_API_VERSION;
        global $PAYPAL_API_ENDPOINT;
        global $PAYPAL_USERNAME;
        global $PAYPAL_PASSWORD;
        global $PAYPAL_SIGNATURE;
        global $PAYPAL_APPID;

        $sign_params = array("VERSION" => $PAYPAL_API_VERSION,
                             "PWD" => $PAYPAL_PASSWORD,
                             "USER" => $PAYPAL_USERNAME,
                             "SIGNATURE" => $PAYPAL_SIGNATURE,
                             "USER" => $PAYPAL_USERNAME,
                             );
        $call_array = array_merge($sign_params,$nvp_array);
        
        $nvpstr = "";
        foreach( $call_array as $key => $val )
        {
            $nvpstr .= "&$key=" . urlencode($val);
        }

        print "nvpstr: $nvpstr\n";
        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$PAYPAL_HASH_API_ENDPOINT);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
        
		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
        
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr);
        
		$response = curl_exec($ch);
        
        print "response: $response\n";
        
        $ret = paypal_deformat_nvp($response);
        
        print "ret: \n";
        var_dump($ret);
        
		if (curl_errno($ch))
		{
            print "curl_errno: " . curl_errno($ch) . "\n";
            print "curl_error: " . curl_error($ch) . "\n";
		}
		else
		{
		  	curl_close($ch);
		}
        
		return $ret;
    }


    function paypal_app_header_call($url,$nvp_array)
	{
		global $PAYPAL_API_ENDPOINT;
        global $PAYPAL_USERNAME;
        global $PAYPAL_PASSWORD;
        global $PAYPAL_SIGNATURE;
        global $PAYPAL_APPID;
        
        $nvpstr = "";
        
        foreach( $nvp_array as $key => $val )
        {
            $nvpstr .= "&$key=" . urlencode($val);
        }

        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
        
		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array("X-PAYPAL-SECURITY-USERID: $PAYPAL_USERNAME",
                         "X-PAYPAL-SECURITY-PASSWORD: $PAYPAL_PASSWORD",
                         "X-PAYPAL-SECURITY-SIGNATURE: $PAYPAL_SIGNATURE",
                         "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
                         "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
                         "X-PAYPAL-APPLICATION-ID: $PAYPAL_APPID",
                         );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr);
        
		$response = curl_exec($ch);
        
        var_dump($response);
        
        $ret = json_decode($response,1);
        
		if (curl_errno($ch))
		{
            print "curl_errno: " . curl_errno($ch) . "\n";
            print "curl_error: " . curl_error($ch) . "\n";
		}
		else 
		{
		  	curl_close($ch);
		}
        
		return $ret;
	}
    
    function paypal_deformat_nvp($nvpstr)
	{
		$intial=0;
	 	$nvpArray = array();
        
		while(strlen($nvpstr))
		{
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
            
			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
        }
		return $nvpArray;
	}

?>