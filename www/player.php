<?php

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
    
    if( !$artist_url )
    {
        header("HTTP/1.0 404 Not Found");
        die();
    }

    $IPHONE = FALSE;
    $IOS = FALSE;
    $IPAD = FALSE;
    if( strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") !== FALSE
       || strpos($_SERVER['HTTP_USER_AGENT'],"Googlebot-Mobile") !== FALSE
       )
    {
        $IPHONE = TRUE;
        $IOS = TRUE;
    }
    else if( strpos($_SERVER['HTTP_USER_AGENT'],"iPad") )
    {
        $IOS = TRUE;
        $IPAD = TRUE;
    }
    $NARROW_SCREEN = FALSE;
    if( $IOS )
    {
        $NARROW_SCREEN = TRUE;
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
    $artist_views = artist_get_total_views($artist_id);
    $artist_twitter = FALSE;
    if( $artist_data['tw_setting'] != 'DISABLED' && $artist_data['twitter'] )
        $artist_twitter = $artist_data['twitter'];
    
    $artist_facebook_page = FALSE;
    if( $artist_data['fb_setting'] != 'DISABLED' && $artist_data['fb_page_url'] )
        $artist_facebook_page = $artist_data['fb_page_url'];
    
    $artist_url = "http://" . $_SERVER["HTTP_HOST"];
    
    $extra = json_decode($artist_data['extra_json'],TRUE);

    $product_list = array();
    $product_list_html = "";
    $q_store = mq("SELECT * FROM mydna_musicplayer_ecommerce_products WHERE artistid='$artist_id' ORDER BY `order` ASC, `id` DESC");
    $i = 0;
    while( $product = mf($q_store) )
    {
        $sizes = FALSE;
        if( $product["size"] != "" )
            $sizes = explode(",", $pro["size"]);
        
        $colors = FALSE;
        if( $product["color"] != "" )
            $colors = explode(",", $pro["color"]);
        
        if( $product["image"] )
            $image = "/artists/products/" . $product["image"];
        else
            $image = "/images/default_product_image.jpg";

        $name = stripslashes($product['name']);
        $price = $product['price'];
        $item = array("id" => $product['id'],
                      "image" => $image,
                      "name" => $name,
                      "description" => $product['description'],
                      "price" => $price,
                      "sizes" => $sizes,
                      "colors" => $colors,
                      );
        $product_list[] = $item;
        
        $html = "";
        $html .= "<div class='item' onclick='storeShowProduct($i);'>";
        $html .= " <div class='image'>";
        $html .= "  <img src='$image'/>";
        $html .= " </div>";
        $html .= " <div class='name_price'>";
        $html .= "  <div class='price'>\$$price</div>";
        $html .= "  <div class='name'>$name</div>";
        $html .= " </div>";
        $html .= "</div>";
        
        $product_list_html .= $html;
        
        $i++;
    }
    $product_list_json = json_encode($product_list);
    $store_enabled = FALSE;
    if( count($product_list) > 0 )
        $store_enabled = TRUE;
    
    $q_tabs = mq("SELECT * FROM mydna_musicplayer_content WHERE artistid='$artist_id' ORDER BY `order` ASC, `id` DESC");
    $tab_list = array();
    while( $tab = mf($q_tabs) )
    {
        $title = stripslashes($tab['name']);
        $content = stripslashes($tab['body']);
        $image = FALSE;
        if( $tab['image'] != '' )
        {
            $image = '/artists/images/' . $tab['image'];
        }
        $item = array("id" => $tab['id'],
                      "title" => $title,
                      "image" => $image,
                      "content" => $content,
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
    $i = 0;
    while( $music = mf($q_music) )
    {
        $music_image = '/artists/images/' . $music["image"];
        $music_audio = '/artists/audio/' . $music["audio"];
        
        $music_name = stripslashes($music["name"]);
        $music_listens = $music["views"];
        $music_free_download = $music["download"] != "0";
        
        $item = array("id" => $music['id'],
                      "name" => $music_name,
                      "mp3" => $music_audio,
                      "free_download" => $music_free_download,
                      "image" => $music_image,
                      "bgcolor" => $music['bgcolor'],
                      "bg_style" => $music['bg_style'],
                      "amazon" => $music['amazon'],
                      "itunes" => $music['itunes'],
                      "product_id" => $music['product_id'],
                      "loaded" => FALSE,
                      "listens" => $music_listens,
                      "image_data" => json_decode($music['image_data']),
                      );
        $music_list[] = $item;

        $buy = FALSE;
        if( $music["amazon"] || $music["itunes"] || $music["product_id"] )
            $buy = TRUE;
        
        $num = $i + 1;
        
        if( $num % 2 == 1 )
            $odd = "odd";
        else
            $odd = "";
        
        $html = "";
        $html .= "<div id='song_playlist_$i' class='play_line $odd'>";
        $html .= " <div class='love_song_name'>";
        $html .= "  <div class='love' onclick='musicToggleLoveIndex($i);'></div>";
        $html .= "  <div onclick='musicChange($i); closeBottom(true);' class='song_name'>$num. $music_name</div>";
        $html .= " </div>";
        $html .= " <div class='buy_length_listens'>";
        if( $music_free_download )
            $html .= "  <div class='buy free' onclick='clickFreeDownload($i);'>FREE</div>";
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
    
    
    $q_video = mq("SELECT * from mydna_musicplayer_video WHERE artistid='$artist_id' AND LENGTH(video) > 0 ORDER BY `order` ASC, `id` DESC");
    $video_list = array();
    $video_list_html = "";
    $i = 0;
    while( $video = mf($q_video) )
    {
        $vid_error = $video["error"];
        if( strlen($vid_error) > 0 )
            continue;
        
        $video_file = trueSiteUrl() . '/vid/' . $video['video'];
        $video_image = '/artists/images/' . $video['image'];
        $video_name = $video['name'];
        
        
        $item = array("id" => $video['id'],
                      "name" => $video_name,
                      "image" => $video_image,
                      "video_file" => $video_file,
                      "views" => $video['views'],
                      "bg_color" => "000",
                      "bg_style" => "LETTERBOX",
                      "image_data" => json_decode($video['image_data']),
                      "loaded" => FALSE,
                      );
        $video_list[] = $item;
        
        $html = "";
        $html .= "<div class='item' onclick='videoPlayIndex($i); closeBottom(true);'>";
        $html .= " <div class='picture'>";
        $html .= "  <img src='$video_image'/>";
        $html .= " </div>";
        $html .= " <div class='label'>$video_name</div>";
        $html .= "</div>";
        $video_list_html .= $html;
        
        $i++;
    }
    $video_list_json = json_encode($video_list);
    $video_nav_show = FALSE;
    if( count($video_list) > 3 )
        $video_nav_show = TRUE;
    $video0_image = "";
    $video0_sources_html = "";
    if( count($video_list) > 0 )
    {
        $video0 = $video_list[0];
        $video0_image = $video0["image"];

        $html = "";
        $html .= "<source src='$video0_mp4' type='video/mp4' />";
        $html .= "<source src='$video0_ogg' type='video/ogg' />";

        $video0_sources_html = $html;
    }
    
    $q_photo = mq("SELECT * from photos WHERE artist_id='$artist_id' ORDER BY `order` ASC, `id` DESC");
    $photo_list = array();
    $photo_list_html = "";
    $i = 0;
    while( $photo = mf($q_photo) )
    {
        $photo_image = '/artists/photo/' . $photo['image'];
        $photo_name = $photo['name'];
        
        $item = array("id" => $photo['id'],
                      "name" => $photo_name,
                      "location" => $photo['location'],
                      "image" => $photo_image,
                      "bg_color" => $photo['bg_color'],
                      "bg_style" => $photo['bg_style'],
                      "views" => $photo['views'],
                      "loaded" => FALSE,
                      "image_data" => json_decode($photo['image_data']),
                      );
        $photo_list[] = $item;
        
        $html = "";
        $html .= "<div class='item' onclick='photoChangeIndex($i); closeBottom(true);'>";
        $html .= " <div class='picture'>";
        $html .= "  <img src='$photo_image'/>";
        $html .= " </div>";
        $html .= " <div class='label'>$photo_name</div>";
        $html .= "</div>";
        $photo_list_html .= $html;
        
        $i++;
    }
    $photo_list_json = json_encode($photo_list);
    $photo_nav_show = FALSE;
    if( count($photo_list) > 3 )
        $photo_nav_show = TRUE;
    
    if( $_COOKIE['FAN_HAS_ORDERED'] == "1" )
        $show_order_status = TRUE;
    else
        $show_order_status = FALSE;

    $login_url = FALSE;
    if( strlen($_COOKIE['FAN_EMAIL']) > 0 
       || strlen($_COOKIE['LOGIN_EMAIL']) > 0 )
        $login_url = trueSiteUrl() . "/landing.php";
        
    $signup_url = trueSiteUrl() . "/signup.php";
    
    $fan_email = "";
    if( strlen($_COOKIE['FAN_EMAIL']) > 0 )
    {
        $fan_email = $_COOKIE['FAN_EMAIL'];
    }
    elseif( strlen($_COOKIE['PAGE_VIEWER_EMAIL']) > 0 )
    {
        $fan_email = $_COOKIE['PAGE_VIEWER_EMAIL'];
    }
    
    $start_media_type = $extra['start_media_type'];
    
    function build_scrollbar($style='')
    {
        
        $scrollbar_html = <<<END
        
<div class='scrollbar-handle-container'>
    <div class='scrollbar-handle $style'>
        <div class='inner'>
            <div class='fingers'>
                <div class='finger'></div>
                <div class='finger'></div>
                <div class='finger'></div>
                <div class='finger'></div>
            </div>
        </div>
    </div>
</div>    

END;
        
        return $scrollbar_html;

    }
    
    $body_style = "";
    if( $NARROW_SCREEN )
    {
        $body_style .= "narrow_screen";
    }
    
    $hide_volume = FALSE;
    if( $IOS )
    {
        $hide_volume = TRUE;
    }
    
    include_once 'templates/player2.html';

?>