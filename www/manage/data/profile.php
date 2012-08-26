<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    
    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        exit();
    }
    session_write_close();
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
        do_POST();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
    function do_POST()
    {
        $artist_id = $_REQUEST['artistid'];
        
        $row = mf(mq("SELECT id,logo,password,extra_json FROM mydna_musicplayer WHERE id='$artist_id'"));
        $old_logo = $row["logo"];
        
        $extra = json_decode($row['extra_json'],TRUE);
		
		$artist = my($_POST["artist"]);
		$email = $_POST["email"];
		$gender = $_POST["artist_gender"];
		$languages = $_POST["artist_languages"];
		$location = $_POST["artist_location"];
		$music_likes  = $_POST["artist_music_likes"];
		$url  = $_POST["url"];
		$website = $_POST["website"];
		$appid = $_POST["appid"];
        $custom_domain = $_POST["custom_domain"];
        $tags = $_POST["tags"];
        $artist_type = $_POST["artist_type"];
        $logo = artist_upload_file($artist_id,$_FILES["logo"],$old_logo);
		
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
                        "custom_domain" => $custom_domain,
                        "tags" => $tags,
                        "artist_type" => $artist_type,
                        );

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
		
		$postedValues['imageSource'] = "../artists/files/".$artist_logo;
		$postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
        if( $_REQUEST['ajax'] )
        {
            $postedValues['artist_data'] = get_artist_data($artist_id);
            echo json_encode($postedValues);
            exit();
        }
        else
        {
            header("Location: /manage/artist_management.php?userId=$artist_id");
            exit();
        }
    }
?>