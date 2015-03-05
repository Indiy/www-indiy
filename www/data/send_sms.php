<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once 'Twilio.php';

    $account_sid = 'ACcc57cd6bc794d8b1a04691e900d4176d';
    $auth_token = '1424e97eead39de5f910719fb904f3ce';
    $client = new Services_Twilio($account_sid, $auth_token);

    $client->account->messages->create(array(
        'To' => "2134446630",
        'From' => "+12672974818",
        'Body' => "Test test test",
    ));

    $ret = array("success" => 1);

    $json = json_encode($ret);
    if( isset($_REQUEST['callback']) )
    {
        $callback = $_REQUEST['callback'];
        echo "$callback($json);";
    }
    else
    {
        echo $json;
    }

?>
