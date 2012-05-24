<?php

    $browser = get_browser(null,TRUE);
    if( $browser['browser'] == 'IE' && $browser['majorver'] < 8 )
    {
        include('unsupported_browser.php');
        die();
    }
    
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
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
    
    if( $artist_url == "" )
    {
        if( $_SESSION['sess_userId'] > 0 )
        {
            $user_id =  $_SESSION['sess_userId'];
            if( $_SESSION['sess_userType'] == 'ARTIST' )
            {
                header("Location: /manage/artist_management.php?userId=$user_id");
                die();
            }
            else if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' )
            {
                header("Location: /manage/dashboard.php");
                die();
            }
            else if( $_SESSION['sess_userType'] == 'LABEL' )
            {
                header("Location: /manage/dashboard.php");
                die();
            }
        }
        if( strlen($_COOKIE['LOGIN_EMAIL']) > 0 )
        {
            include 'landing.php';
        }
        else
        {
            include 'home.php';
        }
        die();
    }
    
    $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE url='$artist_url' LIMIT 1"));
    if( $artist_data == FALSE )
    {
        header("HTTP/1.0 404 Not Found");
        die();
    }
    $artist_id = $artist_data['id'];
    $artist_name = $artist_data['artist'];
    $artist_email = $artist_data['email'];
    $artist_views = $artist_data['views'];
    
    $store_enabled = FALSE;
    $artist_paypal = '';
    
    $ecommerce_check = mf(mq("SELECT * FROM mydna_musicplayer_ecommerce WHERE userid='$artist_id' LIMIT 1"));
    if( $ecommerce_check )
    {
        $artist_paypal = $ecommerce_check["paypal"];
        $store_enabled = $artist_paypal != '';
    }
    
    
    $q_tabs = mq("SELECT * FROM mydna_musicplayer_content WHERE artistid='$artist_id' ORDER BY `order` ASC, `id` DESC");
    $tab_list = array();
    while( $tab = mf($q_tabs) )
    {
        $title = $tab['name'];
        $image = FALSE;
        if( $tab['image'] != '' )
        {
            $image = '/artists/images/' . $tab['image'];
        }
        $item = array("id" => $tab['id'],
                      "title" => $title,
                      "image" => $image,
                      "content" => $tab['body'],
                      );
        $tab_list[] = $item;
    }
    $tab_list_json = json_encode($tab_list);

    $content_tabs_html = '';
    foreach( $tab_list as $i => $tab )
    {
        $title = $tab['title'];
        $content_tabs_html .= "<div class='tab' onclick='showUserPage($i);'>$title</div>\n";
    }
    
    if( $store_enabled )
    {
        $content_tabs_html .= "<div class='tab' onclick='showStore();'>Store</div>";
    }
    $content_tabs_html .= "<div class='tab' onclick='showComments();'>Comment</div>";
    if( $artist_email )
    {
        $content_tabs_html .= "<div class='tab' onclick='showContact();'>Contact</div>";
    }
    
    $q_music = mq("SELECT * FROM mydna_musicplayer_audio WHERE artistid='$artist_id' ORDER BY `order` ASC, `id` DESC");
    $music_list = array();
    $music_list_html = "";
    $i = 1;
    while( $music = mf($q_music) )
    {
        $music_image = '/artists/images/' . $music["image"];
        $music_audio = '/artists/audio/' . $music["audio"];
        
        $music_name = stripslashes($music["name"]);
        $music_listens = $music["views"];
        $music_free_download = $music["download"] != "0";
        
        $item = array("id" => $music["id"],
                      "name" => $music_name,
                      "mp3" => $music_audio,
                      "free_download" => $music_free_download,
                      "image" => $music_image,
                      "bgcolor" => $music["bgcolor"],
                      "bg_style" => $music["bg_style"],
                      "amazon" => $music["amazon"],
                      "itunes" => $music["itunes"],
                      "product_id" => $music["product_id"],
                      "loaded" => FALSE,
                      "listens" => $music_listens,
                      "image_data" => json_decode($music['image_data']),
                      );
        $music_list[] = $item;

        $buy = FALSE;
        if( $music["amazon"] || $music["itunes"] || $music["product_id"] )
            $buy = TRUE;
        
        if( $i % 2 == 1 )
            $odd = "odd";
        else
            $odd = "";
        
        $html = "";
        $html .= "<div class='play_line $odd'>";
        $html .= " <div class='love_song_name'>";
        $html .= "  <div class='love'></div>";
        $html .= "  <div class='song_name'>$i. $music_name</div>";
        $html .= " </div>";
        $html .= " <div class='buy_length_listens'>";
        if( $music_free_download )
            $html .= "  <div class='buy'>FREE</div>";
        else if( $buy )
            $html .= "  <div class='buy'>BUY</div>";
        $html .= "  <div class='sep'></div>";
        $html .= "  <div class='length'>4:05</div>";
        $html .= "  <div class='sep'></div>";
        $html .= "  <div class='played'>$music_listens</div>";
        $html .= " </div>";
        $html .= "</div>";
        $music_list_html .= $html;
    
        $i++;
    }
    $music_list_json = json_encode($music_list);
    
    include_once 'templates/player.html';

?>