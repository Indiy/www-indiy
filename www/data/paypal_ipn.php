<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    require_once '../includes/functions.php';
    require_once '../includes/config.php';
 
    function deformatNVP($nvpstr)
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
    
    $post_body = file_get_contents('php://input');
    
    $nvpArray = deformatNVP($post_body);
    
    $request_json = json_encode($_REQUEST);
    
    $nvp_json = json_encode($nvpArray);
    
    $to = "jim@blueskylabs.com";
    $subject = "IPN Message";

    $message = <<<END

post_body: $post_body

request_json: $request_json

nvp_json: $nvp_json

-----------------

Be Heard. Be Seen. Be Independent.

END;
    $from = "no-reply@myartistdna.com";
    $headers = "From:" . $from;

    mail($to,$subject,$message,$headers);
    
    

?>