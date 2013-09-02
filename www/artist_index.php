<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    list($uri,$query)  = explode('?',$_SERVER['REQUEST_URI'],2);
    if( strlen($uri) == 0 )
    {
        $uri = "/";
    }
    
    $artist_id = get_artist_id_for_page();
    if( !$artist_id )
    {
        if( $uri == '/favicon.ico' )
        {
            header("Content-Type: image/x-icon");
            header("Cache-Control: public, max-age=3600");
            readfile('favicon.ico');
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            header("Cache-Control: no-cache");
            header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
            include_once "error404.php";
        }
        die();
    }
    
    if( $uri == '/favicon.ico' )
    {
        // Custom ICO per artist code goes here
        header("Content-Type: image/x-icon");
        header("Cache-Control: public, max-age=3600");
        readfile('favicon.ico');
        die();
    }
    
    $page = mf(mq("SELECT * FROM pages WHERE artist_id = '$artist_id' AND uri = '$uri'"));
    if( !$page )
    {
        if( $uri == '/' )
        {
            // This may be an old style page, passthrough
            include_once 'player.php';
            die();
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            header("Cache-Control: no-cache");
            header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
            include_once 'error404.php';
            die();
        }
    }
    
    $page_id = $page['page_id'];
    
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
    
    $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id' LIMIT 1"));
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
    $template_id = $page['template_id'];
    
    if( isset($_REQUEST['preview_template']) )
    {
        $template_id = $_REQUEST['preview_template'];
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
    
    $sql = "SELECT mydna_musicplayer_content.* ";
    $sql .= " FROM page_tabs ";
    $sql .= " JOIN mydna_musicplayer_content ON mydna_musicplayer_content.id = page_tabs.tab_id ";
    $sql .= " WHERE page_tabs.page_id = '$page_id' ";
    $sql .= " ORDER BY page_tabs.order ASC, page_tabs.page_tab_id DESC";
    $q = mq($sql);
    $tab_list = array();
    while( $tab = mf($q) )
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

    $music_list = array();
    $video_list = array();
    $photo_list = array();
    
    $sql = "SELECT playlists.* ";
    $sql .= " FROM page_playlists ";
    $sql .= " JOIN playlists ON playlists.playlist_id = page_playlists.playlist_id ";
    $sql .= " WHERE page_playlists.page_id = '$page_id' ";
    $sql .= " ORDER BY page_playlists.order ASC, page_playlists.page_playlist_id DESC";
    $q = mq($sql);
    $playlist_list = array();
    while( $row = mf($q) )
    {
        $row['items'] = array();
        $playlist_list[] = $row;
    }

    for( $i = 0 ; $i < count($playlist_list) ; ++$i )
    {
        $playlist = $playlist_list[$i];
        $playlist_id = $playlist['playlist_id'];
        
        $sql = "SELECT playlist_items.* ";
        $sql .= " ,af1.filename AS image_filename, af1.extra_json AS image_extra_json ";
        $sql .= " ,af2.filename AS media_filename, af2.extra_json AS media_extra_json ";
        $sql .= " FROM playlist_items ";
        $sql .= " LEFT JOIN artist_files AS af1 ON playlist_items.image_id = af1.id ";
        $sql .= " LEFT JOIN artist_files AS af2 ON playlist_items.media_id = af2.id ";
        $sql .= " WHERE playlist_items.playlist_id = '$playlist_id' ";
        $sql .= " ORDER BY playlist_items.order ASC, playlist_items.playlist_item_id DESC ";
        $q = mq($sql);
        while( $row = mf($q) )
        {
            $image_extra = array();
            if( $row['image_extra_json'] )
            {
                $image_extra = json_decode($row['image_extra_json'],TRUE);
            }
            json_decode($row['image_extra_json'],TRUE);
            
            $media_extra = array();
            if( $row['media_extra_json'] )
            {
                $media_extra = json_decode($row['media_extra_json'],TRUE);
            }
            
            $image_url = FALSE;
            if( $row['image_filename'] )
            {
                $image_url = artist_file_url($row['image_filename']);
            }
            $media_url = FALSE;
            if( $row['media_filename'] )
            {
                $media_url = artist_file_url($row['media_filename']);
            }
            
            $row['loaded'] = FALSE;
            $row['listens'] = $row['views'];

            $row['free_download'] = FALSE;
            $row['product_id'] = FALSE;
            $row['amazon'] = "";
            $row['itunes'] = "";
            $row['bgcolor'] = $row['bg_color'];
            if( isset($media_extra['media_length']) )
            {
                $row['media_length'] = ceil($media_extra['media_length']);
            }
            else
            {
                $row['media_length'] = 4*60 + 5;
            }

            $row['location'] = "";

            $row['image'] = $image_url;
            if( isset($image_extra['image_data']) )
            {
                $row['image_data'] = $image_extra['image_data'];
            }
            else
            {
                $row['image_data'] = array();
            }
            $row['image_extra'] = $image_extra;

            $row['mp3'] = $media_url;
            $row['video_file'] = $media_url;
            $row['audio_extra'] = $media_extra;
            $row['video_extra'] = $media_extra;
            $row['media_extra'] = $media_extra;
            if( isset($media_extra['video_data']) )
            {
                $row['video_data'] = $media_extra['video_data'];
            }
            else
            {
                $row['video_data'] = array();
            }
            
            $playlist_list[$i]['items'][] = $row;
        }
        
        if( $playlist['type'] == 'AUDIO' && empty($music_list) )
        {
            $music_list = $playlist_list[$i]['items'];
        }
        if( $playlist['type'] == 'VIDEO' && empty($video_list) )
        {
            $video_list = $playlist_list[$i]['items'];
        }
        if( $playlist['type'] == 'PHOTO' && empty($photo_list) )
        {
            $photo_list = $playlist_list[$i]['items'];
        }
    }
    $playlist_list_json = json_encode($playlist_list);
    $music_list_json = json_encode($music_list);
    $video_list_json = json_encode($video_list);
    $video_nav_show = FALSE;
    if( count($video_list) > 3 )
        $video_nav_show = TRUE;
    
    $photo_list_json = json_encode($photo_list);
    $photo_nav_show = FALSE;
    if( count($photo_list) > 3 )
        $photo_nav_show = TRUE;
    
    $is_logged_in_text = FALSE;
    $is_logged_in_url = FALSE;

    $login_url = FALSE;
    $signup_url = trueSiteUrl() . "/signup.php";
    
    $fan_email = "";
    
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
                    $video_extra = json_decode($file['extra_json'],TRUE);
                    
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

        if( !$template_params['ga_account_id'] )
        {
            $template_params['ga_account_id'] = 'UA-15194524-1';
        }
        
        $template_params_json = json_encode($template_params);
        
        if( $template_type == 'PLAYER_PRINCE' )
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
                include_once 'templates/player_prince.html';
            }
        }
        else if( $template_type == 'PLAYER_DEFAULT_V2' )
        {
            include_once 'templates/default_v2.html';
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
        else if( $template_type == 'PLAYER_SPLASH_VIDEO' )
        {
            include_once 'templates/splash_video.html';
        }
        else if( $template_type == 'PLAYER_SPLASH_FORM_DOWNLOAD' )
        {
            include_once 'templates/splash_form_download.html';
        }
    }

?>