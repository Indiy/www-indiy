<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    if( $_SESSION['in_process_order_id'] && $_GET['abandon_order'] )
    {
        $abandon_order = $_SESSION['in_process_order_id'];
        mysql_update('orders',array("state" => "ABANDONED"),'id',$abandon_order);
        $_SESSION['in_process_order_id'] = FALSE;
    }

    $cart_id = $_SESSION['cart_id'];
    $artist_id = $_REQUEST['artist_id'];
    
    $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));

    $cart_list = store_get_cart($artist_id,$cart_id);
    $cart_list_json = json_encode($cart_list);

    include_once 'templates/cart.html';
?>