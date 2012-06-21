<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    if( $_SESSION['in_process_order_id'] && $_GET['abandon_order'] )
    {
        $abandon_order = $_SESSION['in_process_order_id'];
        mysql_update('orders',array("state" => "ABANDONED"),'id',$abandon_order);
        $_SESSION['in_process_order_id'] = FALSE;
    }

    $artist_url = '';
    $http_host = $_SERVER["HTTP_HOST"];
    if( "http://" . $http_host == trueSiteUrl() )
    {
        $artist_url = $_GET["url"];
    }
    else if( "http://www." . $http_host == trueSiteUrl() )
    {
        if( $_GET["url"] )
        {
            $artist_url = $_GET["url"];
        }
        else
        {
            header("Location: " . trueSiteUrl());
            die();
        }
    }
    else 
    {
        $host_parts = explode('.',$http_host);
        $trailing_parts = array_slice($host_parts,-2);
        $trailing = implode('.',$trailing_parts);
        $leading_parts = array_slice($host_parts,0,-2);
        $leading = implode('.',$leading_parts);
        if( "http://www." . $trailing == trueSiteUrl() )
        {
            $artist_url = $leading;
        }
        else
        {
            $row = mf(mq("SELECT * FROM mydna_musicplayer WHERE custom_domain = '$http_host'"));
            if( $row )
                $artist_url = $row['url'];
        }
    }
    
    if( !$artist_url )
    {
        header("Location: /");
        die();
    }
    
    $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE url='$artist_url' LIMIT 1"));
    if( $artist_data == FALSE )
    {
        header("HTTP/1.0 404 Not Found");
        die();
    }
    $artist_id = $artist_data['id'];

    $cart_list = store_get_cart();
    $cart_list_json = json_encode($cart_list);

    include_once 'templates/cart.html';
?>