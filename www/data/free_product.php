<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    include('../includes/functions.php');   
    include('../includes/config.php');

    $artist_id = $_REQUEST['artist_id'];
    $product_id = $_REQUEST['product_id'];

    if( isset($_SESSION['fan_id']) )
    {
        add_free_product_to_fan($product_id);
        $ret = array("success" => 1);
        send_response($ret);
        die();
    }
    else
    {
        $_SESSION['free_product_to_buy'] = $product_id;
        $ret = array("error" => "need_login_signup");
        send_response($ret);
        die();
    }
    
    function send_response($ret)
    {
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
    }

?>

