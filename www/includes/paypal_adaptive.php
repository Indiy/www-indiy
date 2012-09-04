<?php

    $PAYPAL_USERNAME = "mad_1346558535_biz_api1.myartistdna.com";
    $PAYPAL_PASSWORD = "1346558558";
    $PAYPAL_SIGNATURE = "Ab.Ua9MmJioLkDJWgEubbcrQ8dONA9x1bbDIhJetM9P6ktHGYZ6AK3D-";
    $PAYPAL_APPID = "APP-80W284485P519543T";

    $PAYPAL_API_ENDPOINT = "https://svcs.sandbox.paypal.com/AdaptivePayments/Pay";
    
    //$PAYPAL_URL = "https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay?paykey=";
    $PAYPAL_URL = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=";
    
    function paypal_checkout($extra_args)
    {
        $nvpstr = "";
        
        foreach( $extra_args as $key => $val )
        {
            $nvpstr .= "&$key=" . urlencode($val);
        }
    
        return hash_call($nvpstr);
    }


    function hash_call($nvpStr)
	{
		global $PAYPAL_API_ENDPOINT;
        global $PAYPAL_USERNAME;
        global $PAYPAL_PASSWORD;
        global $PAYPAL_SIGNATURE;
        global $PAYPAL_APPID;
        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$PAYPAL_API_ENDPOINT);
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
        
		$nvpreq = $nvpStr;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
        
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

?>