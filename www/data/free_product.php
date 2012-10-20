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
        echo json_encode($ret);
        die();
    }
    else
    {
        $_SEESION['free_product_to_buy'] = $product_id;
        $ret = array("error" => "need_login_signup");
        echo json_encode($ret);
        die();
    }

?>

