<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/functions.php';
    require_once '../includes/config.php';

    require_once 'Stripe.php';

    Stripe::setApiKey($g_stripe_secret_key);

    $token = $_REQUEST['token'];
    $email = $_REQUEST['email'];

    $customer = Stripe_Customer::create(array(
        'email' => $email,
        'card'  => $token,
    ));

    $charge = Stripe_Charge::create(array(
        'customer' => $customer->id,
        'amount'   => 5000,
        'currency' => 'usd',
    ));

    echo json_encode(array("charge" => $charge,"customer" => $customer));
    die();
?>
