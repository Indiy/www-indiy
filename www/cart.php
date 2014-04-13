<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    session_start();
    if( $_SESSION['in_process_order_id'] && $_GET['abandon_order'] )
    {
        $abandon_order = $_SESSION['in_process_order_id'];
        mysql_update('orders',array("state" => "ABANDONED"),'id',$abandon_order);
        $_SESSION['in_process_order_id'] = FALSE;
    }
    session_write_close();

    $cart_id = $_SESSION['cart_id'];
    $artist_id = $_REQUEST['artist_id'];
    
    $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));

    $cart = store_get_cart($artist_id,$cart_id);
    $cart_json = json_encode($cart);
    
    $artist_url = $artist_data['url'];
    $full_artist_url = str_replace("http://www.","http://$artist_url.",trueSiteUrl());

    $fan_email = get_fan_email();
    
    include_once 'templates/cart.html';
?>