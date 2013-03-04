<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    require_once '../../includes/login_helper.php';
    
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        exit();
    }
    
    if( isset($_REQUEST['method']) )
        $method = strtoupper($_REQUEST['method']);
    else
        $method = $_SERVER['REQUEST_METHOD'];
    
    
    if( $method == 'POST' )
    {
        do_POST();
    }
    else if( $method == 'UPDATE_PUBLISH' )
    {
        do_UPDATE_PUBLISH();
    }
    else if( $method == 'CLEAR_FIRST_INSTRUCTIONS' )
    {
        do_CLEAR_FIRST_INSTRUCTIONS();
    }
    else if( $method == 'SET_EMAIL_ADDRESS' )
    {
        do_SET_EMAIL_ADDRESS();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
    function do_POST()
    {
        $artist_id = $_REQUEST['artistid'];
        
        $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
        $old_logo = $artist_data["logo"];
        
        $extra = json_decode($artist_data['extra_json'],TRUE);
		
		$artist = my($_POST["artist"]);
		$email = $_POST["email"];
		$gender = $_POST["artist_gender"];
		$languages = $_POST["artist_languages"];
		$location = $_POST["artist_location"];
		$music_likes  = $_POST["artist_music_likes"];
		$url  = $_POST["url"];
		$website = $_POST["website"];
		$appid = $_POST["appid"];
        $tags = $_POST["tags"];
        $artist_type = $_POST["artist_type"];
        
        $logo = $_POST['image_drop'];
		
        $values = array("artist" => $artist,
                        "email" => $email,
                        "gender" => $gender,
                        "languages" => $languages,
                        "location" => $location,
                        "music_likes" => $music_likes,
                        "url" => $url,
                        "website" => $website,
                        "appid" => $appid,
                        "IsArtist" => $IsArtist,
                        "logo" => $logo,
                        "tags" => $tags,
                        "artist_type" => $artist_type,
                        );
        if( isset($_POST["custom_domain"]) )
        {
            $custom_domain = $_POST["custom_domain"];
            if( strlen($custom_domain) > 0 )
            {
                $values['custom_domain'] = $custom_domain;
            }
            else
            {
                mq("UPDATE mydna_musicplayer SET custom_domain = NULL WHERE id='$artist_id'");
            }
        }

        $extra_modified = FALSE;
        if( isset($_POST["start_media_type"]) )
        {
            $extra['start_media_type'] = $_POST["start_media_type"];
            $extra_modified = TRUE;
        }
        
        if( $extra_modified )
        {
            $extra_json = json_encode($extra);
            $values['extra_json'] = $extra_json;
        }
		
        mysql_update("mydna_musicplayer",$values,"id",$artist_id);
		
        $artist_page_url = str_replace("http://www.","http://$url.",trueSiteUrl());
        if( strlen($artist_data['preview_key']) > 0 )
        {
            $preview_key = $artist_data['preview_key'];
            $artist_page_url .= "/?preview_key=$preview_key";
        }
        
		$postedValues['imageSource'] = "../artists/files/".$artist_logo;
		$postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
        if( $_REQUEST['ajax'] )
        {
            $postedValues['artist_data'] = get_artist_data($artist_id);
            $postedValues['artist_page_url'] = $artist_page_url;
            echo json_encode($postedValues);
            exit();
        }
        else
        {
            header("Location: /manage/artist_management.php?userId=$artist_id");
            exit();
        }
    }
    function do_UPDATE_PUBLISH()
    {
        $artist_id = $_REQUEST['artist_id'];
        $do_publish = $_REQUEST['do_publish'] == 'true';
        
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
        $artist_url = $artist['url'];
        $url = str_replace("http://www.","http://$artist_url.",trueSiteUrl());
        if( $do_publish )
        {
            $values = array("preview_key" => "");
        }
        else
        {
            $preview_key = random_string(8);
            $values = array("preview_key" => $preview_key);
            $url .= "/?preview_key=$preview_key";
        }
        mysql_update("mydna_musicplayer",$values,"id",$artist_id);
        
        $ret = array("success" => 1,
                     "url" => $url,
                     "request" => $_REQUEST,
                     );
        
        echo json_encode($ret);
        die();
    }
    function do_CLEAR_FIRST_INSTRUCTIONS()
    {
        $artist_id = $_REQUEST['artist_id'];
        
        $values = array("shown_first_instructions" => 1);
        
        mysql_update('mydna_musicplayer',$values,'id',$artist_id);
        
        $ret = array("success" => 1);
        echo json_encode($ret);
        die();
    }
    function do_SET_EMAIL_ADDRESS()
    {
        $artist_id = $_REQUEST['artist_id'];
        
        $email = $_REQUEST['email'];
        
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE email='$email'"));
        if( $artist )
        {
            $ret = array("error" => "Email already used for another account.  Please use a unique email for each account.");
            echo json_encode($ret);
            die();
        }
        
        $values = array("email" => $email);
        
        mysql_update('mydna_musicplayer',$values,'id',$artist_id);
        
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
        post_artist_signup($artist);
        
        $ret = array("success" => 1);
        echo json_encode($ret);
        die();
    }
?>