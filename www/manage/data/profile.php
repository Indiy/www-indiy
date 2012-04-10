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
        
        $row = mf(mq("SELECT id,logo,password FROM mydna_musicplayer WHERE id='$artist_id'"));
        $old_logo = $row["logo"];
		
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
        $user_tags = $_POST["tags"];
		
		if(!empty($_FILES["logo"]["name"]))
        {
			if (is_uploaded_file($_FILES["logo"]["tmp_name"])) 
            {
				$artist_logo = $artistid."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
				@move_uploaded_file($_FILES['logo']['tmp_name'], PATH_TO_ROOT . "artists/images/$artist_logo");
                $logo = $artist_logo;
			} 
            else 
            {
				if ($old_logo != $artist_logo) 
                {
					$logo = $old_logo;
				}
			}
		}
        else
        {
			$logo = $old_logo;
		}
		
		$tables = "artist|email|gender|languages|location|music_likes|url|website|appid|IsArtist|logo|custom_domain|tags";
		$values = "{$artist}|{$email}|{$gender}|{$languages}|{$location}|{$music_likes}|{$url}|{$website}|{$appid}|{$IsArtist}|{$logo}|{$custom_domain}|$user_tags";
		
        update("mydna_musicplayer",$tables,$values,"id",$artist_id);
		
		$postedValues['imageSource'] = "../artists/images/".$artist_logo;
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