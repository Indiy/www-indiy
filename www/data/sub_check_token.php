<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/functions.php';
    require_once '../includes/config.php';

    $artist_id = $_REQUEST['artist_id'];
    $secret_token = $_REQUEST['secret_token'];

    $sub = mf(mq("SELECT * FROM subscriptions WHERE artist_id = '$artist_id' AND secret_token = '$secret_token'"));
    if( $sub )
    {
        die(json_encode(array('success' => TRUE)));
    }
    else
    {
        die(json_encode(array('error' => "not_found")));
    }
?>
