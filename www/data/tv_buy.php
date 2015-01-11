<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/functions.php';
    require_once '../includes/config.php';

    require_once 'Stripe.php';
    Stripe::setApiKey($g_stripe_secret_key);

    $artist_id = $_REQUEST['artist_id'];
    $template_id = $_REQUEST['template_id'];
    $token = $_REQUEST['token'];
    $email = $_REQUEST['email'];
    $url = $_REQUEST['url'];

    $sub = mf(mq("SELECT * FROM subscriptions WHERE artist_id = '$artist_id' AND 'email' = $email"));
    $template = mf(mq("SELECT * FROM templates WHERE id='$template_id'"));

    if( $sub )
    {
        die(json_encode(array('error' => "existing_sub")));
    }
    else if( $template )
    {
        $template_params = json_decode($template['params_json'],TRUE);
        $checkout_amount = $template_params['checkout_amount'];
        $amount = floor($checkout_amount * 100);
        if( $amount > 0 )
        {
            try
            {
                $customer = Stripe_Customer::create(array(
                    'email' => $email,
                    'card' => $token,
                ));

                $charge = Stripe_Charge::create(array(
                    'customer' => $customer->id,
                    'amount' => $amount,
                    'currency' => 'usd',
                ));

                $stripe_charge_id = $charge['charge_id'];
                $secret_token = random_string(32);

                $values = array(
                    'artist_id' => $artist_id,
                    'email' => $email,
                    'stripe_charge_id' => $stripe_charge_id,
                    'secret_token' => $secret_token,
                );
                mysql_insert("subscriptions",$values);

                $channel_url = "$url?token=$secret_token";

                $to = $email;
                $subject = "Your Channel Subscription";
                $message = <<<END
                
Thank you for subscribing.

Use this link to access to access the channel:

$channel_url

END;
                $from = "no-reply@myartistdna.com";
                $headers = "From:" . $from;
                mail($to,$subject,$message,$headers);

                die(json_encode(array('secret_token' => $secret_token)));
            }
            catch(Exception $e)
            {
                die(json_encode(array('error' => "stripe_charge")));
            }
        }
        else
        {
            die(json_encode(array('error' => "bad_amount")));
        }
    }
    else
    {
        die(json_encode(array('error' => "no_template")));
    }
?>
