<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once 'Twilio.php';

    function gen_code($length = 4)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for( $i = 0 ; $i < $length ; $i++ )
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    $to = $_REQUEST['to'];

    $ret = array("success" => 0,"error" => "unknown");

    if( strlen($to) > 0 )
    {
        $account_sid = 'ACcc57cd6bc794d8b1a04691e900d4176d';
        $auth_token = '1424e97eead39de5f910719fb904f3ce';
        $client = new Services_Twilio($account_sid, $auth_token);

        $code = gen_code();

        $client->account->messages->create(array(
            'To' => $to,
            'From' => "+12672974818",
            'Body' => "Welcome to MyChannel. Your verification code is $code",
        ));

        $ret = array("success" => 1,"code" => $code);
    }
    else
    {
        $ret = array("success" => 0,"error" => "to is required");
    }

    $json = json_encode($ret);
    if( isset($_REQUEST['callback']) )
    {
        $callback = $_REQUEST['callback'];
        die("$callback($json);");
    }
    else
    {
        die($json);
    }

?>
