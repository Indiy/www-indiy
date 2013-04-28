<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    check_unsupported_browser();
    session_start();
    
    if( !$artist_url )
    {
        $artist_url = get_artist_url_for_page();
    }
    
    if( !$artist_url )
    {
        header("HTTP/1.0 404 Not Found");
        header("Cache-Control: no-cache");
        header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
        include_once "error404.php";
        die();
    }
    $hide_volume = FALSE;
    $single_media_button = FALSE;
    $all_links_blank = "";
    $thin_footer = FALSE;
    $media_auto_start = TRUE;

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
        $hide_volume = TRUE;
    }
    if( isset($_REQUEST['embed']) )
    {
        $NARROW_SCREEN = TRUE;
        $hide_volume = TRUE;
        $single_media_button = TRUE;
        $all_links_blank = " target='_blank' ";
        $thin_footer = TRUE;
        $media_auto_start = FALSE;
    }
    
    $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE url='$artist_url' LIMIT 1"));
    if( $artist_data == FALSE )
    {
        header("HTTP/1.0 404 Not Found");
        header("Cache-Control: no-cache");
        header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
        include_once "error404.php";
        die();
    }
    if( strlen($artist_data['preview_key']) > 0 )
    {
        $preview_key = $_REQUEST['preview_key'];
        if( $preview_key != $artist_data['preview_key'] )
        {
            header("HTTP/1.0 404 Not Found");
            header("Cache-Control: no-cache");
            header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
            include_once "error404.php";
            die();
        }
    }
    
    header("X-UA-Compatible: chrome=1");
    
    $artist_id = $artist_data['id'];
    $artist_name = $artist_data['artist'];
    $artist_email = $artist_data['email'];
    $artist_views = artist_get_total_views($artist_id);
    $artist_logo = $artist_data['logo'];
    $template_id = $artist_data['template_id'];
    
    if( isset($_REQUEST['preview_template']) )
    {
        $template_id = $_REQUEST['preview_template'];
    }
    
    $artist_twitter = FALSE;
    if( $artist_data['tw_setting'] != 'DISABLED' && $artist_data['twitter'] )
        $artist_twitter = $artist_data['twitter'];
    
    $artist_facebook_page = FALSE;
    if( $artist_data['fb_setting'] != 'DISABLED' && $artist_data['fb_page_url'] )
        $artist_facebook_page = $artist_data['fb_page_url'];
    
    $artist_base_url = str_replace("http://www.","http://$artist_url.",trueSiteUrl());
    
    $artist_extra = json_decode($artist_data['extra_json'],TRUE);
    $start_media_type = $artist_extra['start_media_type'];
    
    $fb_like_url = $artist_base_url;
    if( $artist_data['custom_domain'] )
    {
        $fb_like_url = "http://www." . $artist_data['custom_domain'];
    }
    
    $iphone_version = FALSE;
    if( strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") !== FALSE )
    {
        $iphone_version = TRUE;
    }
    if( isset($_REQUEST['desktop']) && $_REQUEST['desktop'] == 'true' )
    {
        $iphone_version = FALSE;
    }
    if( $template_id )
    {
        $iphone_version = FALSE;
    }
    if( $iphone_version )
    {
        //require_once 'player_iphone.php';
        //die();
    }

    $product_list = array();
    $product_list_html = "";
    $q_store = mq("SELECT * FROM mydna_musicplayer_ecommerce_products WHERE artistid='$artist_id' ORDER BY `order` ASC, `id` DESC");
    $i = 0;
    while( $product = mf($q_store) )
    {
        $extra = array();
        $extra_json = $product['extra_json'];
        if( strlen($extra_json) > 0 )
        {
            $extra = json_decode($extra_json,TRUE);
        }
        
        $sizes = FALSE;
        if( $product["size"] != "" )
            $sizes = explode(",", $product["size"]);
        
        $colors = FALSE;
        if( $product["color"] != "" )
            $colors = explode(",", $product["color"]);
        
        if( $product["image"] )
            $image = artist_file_url($product["image"]);
        else
            $image = "/images/default_product_image.jpg";

        $name = stripslashes($product['name']);
        $price = floatval($product['price']);
        $item = array("id" => $product['id'],
                      "image" => $image,
                      "name" => $name,
                      "description" => $product['description'],
                      "price" => $price,
                      "sizes" => $sizes,
                      "colors" => $colors,
                      "extra" => $extra,
                      );
        $product_list[] = $item;
        
        $html = "";
        $html .= "<div class='item' onclick='storeShowProduct($i);'>";
        $html .= " <div class='image'>";
        $html .= "  <img src='$image'/>";
        $html .= " </div>";
        $html .= " <div class='name_price'>";
        if( $price > 0.0 )
        {
            $html .= "  <div class='price'>\$$price</div>";
        }
        else
        {
            $html .= "  <div class='price'>FREE</div>";
        }
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
            $image = artist_file_url($tab['image']);
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
    //$content_tabs_html .= "<div class='tab' onclick='showComments();'>Comment</div>";
    if( $artist_email )
    {
        $content_tabs_html .= "<div class='tab' onclick='showContact();'>Contact</div>";
    }
    
    $sql = "SELECT mydna_musicplayer_audio.* , af1.extra_json AS audio_extra_json, af2.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_audio";
    $sql .= " JOIN artist_files AS af1 ON mydna_musicplayer_audio.audio = af1.filename";
    $sql .= " JOIN artist_files AS af2 ON mydna_musicplayer_audio.image = af2.filename";
    $sql .= " WHERE mydna_musicplayer_audio.artistid='$artist_id'";
    $sql .= " ORDER BY mydna_musicplayer_audio.order ASC, mydna_musicplayer_audio.id DESC";

    $q = mq($sql);
    $music_list = array();
    $music_list_html = "";
    $i = 0;
    while( $music = mf($q) )
    {
        $music_image = artist_file_url($music['image']);
        $music_audio = artist_file_url($music['audio']);
        
        $audio_extra = json_decode($music['audio_extra_json'],TRUE);
        $image_extra = json_decode($music['image_extra_json'],TRUE);
        
        $music_name = stripslashes($music["name"]);
        $music_listens = $music["views"];
        $music_free_download = $music["download"] != "0";
        $product_id = intval($music['product_id']);
        if( !$product_id )
            $product_id = FALSE;
        
        if( $music['extra_json'] )
        {
            $extra = json_decode($music['extra_json'],TRUE);
        }
        else
        {
            $extra = array();
        }
        
        if( isset($audio_extra['media_length']) )
        {
            $song_len = ceil($audio_extra['media_length']);
        }
        else
        {
            $song_len = 4*60 + 5;
        }
        $length_string = sprintf("%02d:%02d",$song_len / 60,$song_len % 60);
        
        $item = array("id" => $music['id'],
                      "name" => $music_name,
                      "mp3" => $music_audio,
                      "free_download" => $music_free_download,
                      "image" => $music_image,
                      "bgcolor" => $music['bgcolor'],
                      "bg_style" => $music['bg_style'],
                      "amazon" => $music['amazon'],
                      "itunes" => $music['itunes'],
                      "product_id" => $product_id,
                      "loaded" => FALSE,
                      "listens" => $music_listens,
                      "image_data" => $image_extra['image_data'],
                      "image_extra" => $image_extra,
                      "audio_extra" => $audio_extra,
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
            $html .= "  <div class='buy' onclick='clickBuySong($i);'>BUY</div>";
        $html .= "  <div class='sep'></div>";
        $html .= "  <div class='length'>$length_string</div>";
        $html .= "  <div class='sep'></div>";
        $html .= "  <div class='played'>$music_listens</div>";
        $html .= " </div>";
        $html .= "</div>";
        $music_list_html .= $html;
    
        $i++;
    }
    $music_list_json = json_encode($music_list);
    
    $sql = "SELECT mydna_musicplayer_video.* , af1.extra_json AS video_extra_json, af2.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_video";
    $sql .= " JOIN artist_files AS af1 ON mydna_musicplayer_video.video = af1.filename";
    $sql .= " JOIN artist_files AS af2 ON mydna_musicplayer_video.image = af2.filename";
    $sql .= " WHERE mydna_musicplayer_video.artistid='$artist_id' AND LENGTH(mydna_musicplayer_video.video) > 0";
    $sql .= " ORDER BY mydna_musicplayer_video.order ASC, mydna_musicplayer_video.id DESC";
    
    $q_video = mq($sql);
    $video_list = array();
    $video_list_html = "";
    $i = 0;
    while( $video = mf($q_video) )
    {
        $vid_error = $video["error"];
        if( strlen($vid_error) > 0 )
            continue;
        
        $video_file = artist_file_url($video['video']);
        $video_image = artist_file_url($video['image']);
        $video_name = $video['name'];
        
        $image_extra = json_decode($video['image_extra_json'],TRUE);
        $video_extra = json_decode($video['video_extra_json'],TRUE);
        
        $img_url = get_image_thumbnail($video_image,$video_img_extra,200);
        
        $item = array("id" => $video['id'],
                      "name" => $video_name,
                      "image" => $video_image,
                      "video_file" => $video_file,
                      "views" => $video['views'],
                      "bg_color" => "000",
                      "bg_style" => "LETTERBOX",
                      "image_data" => $image_extra['image_data'],
                      "video_data" => $video_extra['video_data'],
                      "loaded" => FALSE,
                      "image_extra" => $image_extra,
                      "video_extra" => $video_extra,
                      );
        $video_list[] = $item;
        
        $html = "";
        $html .= "<div class='item' onclick='videoPlayIndex($i); closeBottom(true);'>";
        $html .= " <div class='picture'>";
        $html .= "  <img src='$img_url'/>";
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
    
    $sql = "SELECT photos.*, af1.extra_json AS image_extra_json ";
    $sql .= " FROM photos";
    $sql .= " JOIN artist_files AS af1 ON photos.image = af1.filename";
    $sql .= " WHERE photos.artist_id='$artist_id'";
    $sql .= " ORDER BY photos.order ASC, photos.id DESC";

    $q_photo = mq($sql);
    $photo_list = array();
    $photo_list_html = "";
    $i = 0;
    while( $photo = mf($q_photo) )
    {
        $photo_image = artist_file_url($photo['image']);
        $image_extra = json_decode($photo['image_extra_json'],TRUE);
        
        $img_url = get_image_thumbnail($photo_image,$image_extra,200);
        
        $photo_name = $photo['name'];
        
        $item = array("id" => $photo['id'],
                      "name" => $photo_name,
                      "location" => $photo['location'],
                      "image" => $photo_image,
                      "bg_color" => $photo['bg_color'],
                      "bg_style" => $photo['bg_style'],
                      "views" => $photo['views'],
                      "loaded" => FALSE,
                      "image_data" => $image_extra['image_data'],
                      "image_extra" => $image_extra,
                      );
        $photo_list[] = $item;
        
        $html = "";
        $html .= "<div class='item' onclick='photoChangeIndex($i); closeBottom(true);'>";
        $html .= " <div class='picture'>";
        $html .= "  <img src='$img_url'/>";
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


    $is_logged_in_text = FALSE;
    $is_logged_in_url = FALSE;
    if( isset($_SESSION['fan_email']) )
    {
        $is_logged_in_text = $_SESSION['fan_email'];
        $is_logged_in_url = trueSiteUrl() . "/fan/";
    }
    if( isset($_SESSION['sess_userEmail']) )
    {
        $is_logged_in_text = $_SESSION['sess_userEmail'];
        $is_logged_in_url = trueSiteUrl() . "/manage/";
    }
    else if( isset($_SESSION['sess_userName']) )
    {
        $is_logged_in_text = $_SESSION['sess_userName'];
        $is_logged_in_url = trueSiteUrl() . "/manage/";
    }
    if( $is_logged_in_text )
    {
        $is_logged_in_text = "Logged in as: <a $all_links_blank href='$is_logged_in_url'>$is_logged_in_text</a>";
    }


    $login_url = FALSE;
    if( strlen($_COOKIE['FAN_EMAIL']) > 0 
       || strlen($_COOKIE['LOGIN_EMAIL']) > 0 )
        $login_url = trueSiteUrl() . "/login.php";
        
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
    
    $body_style = "";
    if( $NARROW_SCREEN )
    {
        $body_style .= " narrow_screen";
    }
    
    if( $hide_volume  )
    {
        $body_style .= " hide_volume";
    }
    if( $thin_footer )
    {
        $body_style .= " thin_footer";
    }
    
    function make_comments_for_list($base_url,$type,$list)
    {
        $ret_html = "";
        foreach( $list as $index => $item )
        {
            $id = $item['id'];
            $url = "$base_url/#{$type}_id=$id";
            $id_tag = "{$type}_id_$id";
        
            $html = "";
            $html .= "<div id='$id_tag' class='fb_container'>";
            $html .= " <fb:comments href='$url' num_posts='10' width='470' colorscheme='dark'></fb:comments>";
            $html .= "</div>";
            
            $ret_html .= $html;
        }
        
        return $ret_html;
    }
    
    $comments_html = "";
    $comments_html .= make_comments_for_list($artist_base_url,"song",$music_list);
    $comments_html .= make_comments_for_list($artist_base_url,"video",$video_list);
    $comments_html .= make_comments_for_list($artist_base_url,"photo",$photo_list);
    
    if( $template_id )
    {
        $template = mf(mq("SELECT * FROM templates WHERE id='$template_id'"));
    }
    if( $template )
    {
        $template_type = $template['type'];
        $template_params = json_decode($template['params_json'],TRUE);
        foreach( $template_params as $key => $val )
        {
            if( is_array($val) && isset($val['image_file_id']) )
            {
                $file_id = $val['image_file_id'];
                $file = mf(mq("SELECT * FROM artist_files WHERE id='$file_id'"));
                if( $file )
                {
                    $image_url = artist_file_url($file['filename']);
                    $image_extra = json_decode($file['extra_json'],TRUE);
                    
                    $bg_color = "#000";
                    $bg_style = "STRETCH";
                    if( isset($val['bg_color']) )
                    {
                        $bg_color = $val['bg_color'];
                    }
                    if( isset($val['bg_style']) )
                    {
                        $bg_style = $val['bg_style'];
                    }
                    
                    $item = array("image" => $image_url,
                                  "bg_color" => $bg_color,
                                  "bg_style" => $bg_style,
                                  "loaded" => FALSE,
                                  "image_data" => $image_extra['image_data'],
                                  "image_extra" => $image_extra,
                                  );
                    $template_params[$key] = $item;
                }
                else
                {
                    $template_params[$key] = FALSE;
                }
            }
            else if( is_array($val) && isset($val['video_file_id']) )
            {
                $file_id = $val['video_file_id'];
                $file = mf(mq("SELECT * FROM artist_files WHERE id='$file_id'"));
                if( $file )
                {
                    $video_url = artist_file_url($file['filename']);
                    $image_extra = json_decode($file['extra_json'],TRUE);
                    
                    $item = array(
                                  "video_file" => $video_url,
                                  "video_data" => $video_extra['video_data'],
                                  "loaded" => FALSE,
                                  "video_extra" => $video_extra,
                                  );
                    $template_params[$key] = $item;
                }
                else
                {
                    $template_params[$key] = FALSE;
                }
            }
            else if( is_array($val) && isset($val['misc_file_id']) )
            {
                $file_id = $val['misc_file_id'];
                $file = mf(mq("SELECT * FROM artist_files WHERE id='$file_id'"));
                if( $file )
                {
                    $file_url = artist_file_url($file['filename']);
                    $template_params[$key] = $file_url;
                }
                else
                {
                    $template_params[$key] = FALSE;
                }
            }
        }
        $template_params_json = json_encode($template_params);
        
        
        if( $template_type == 'PLAYER_PRINCE' )
        {
            include_once 'templates/player_prince.html';
        }
        else if( $template_type == 'PLAYER_MEEK_SPLASH' )
        {
            include_once 'templates/meek_splash.html';
        }
        else if( $template_type == 'PLAYER_MEEK_VIDEO' )
        {
            include_once 'templates/meek_video.html';
        }
        else if( $template_type == 'PLAYER_MEEK_STREAM' )
        {
            include_once 'templates/meek_stream.html';
        }
        else if( $template_type == 'PLAYER_SPLASH_AUDIO' )
        {
            include_once 'templates/splash_audio.html';
        }
        else if( $template_type == 'PLAYER_COUNTDOWN_AUDIO' )
        {
            include_once 'templates/countdown_audio.html';
        }
    }
    else
    {
        if( $iphone_version )
        {
            $button_count = 0;
            if( count($music_list) > 0 )
                $button_count++;
            if( count($video_list) > 0 )
                $button_count++;
            if( count($photo_list) > 0 )
                $button_count++;
            
            $button_style = "buttons_$button_count";
        
            $copyright_text = "&copy;MyArtistDNA";
            $head_title_text = "MyAritstDNA | Be Heard. Be Seen. Be Independent.";
            
            include_once 'templates/player_iphone.html';
        }
        else
        {
            include_once 'templates/player.html';
        }
    }

?>