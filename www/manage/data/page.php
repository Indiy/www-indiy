<?php  
    
    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	
    if( $_SESSION['sess_userId'] == "" )
	{
		header("location: index.php");
		exit();
	}

    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
        do_POST();
    else
        print "Bad method\n";

    exit();

function do_POST()
{
    $artist_id = $_POST['artistid'];

    $upload_audio_filename = NULL;
    $song_id = $_POST['id'];
    if( $song_id ) 
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id='$song_id'"));
        
        $old_logo = $row["image"];
        $old_sound = $row["audio"];
        $old_product_id = $row["product_id"];
        $upload_audio_filename = $row["upload_audio_filename"];
        $old_image_data = $row["image_data"];
    }
    
    $audio_name = my($_POST["name"]);
    $audio_download = $_POST["download"];
    $audio_bgcolor = $_POST["bgcolor"];
    $audio_bgposition = $_POST["bgposition"];
    $audio_bgrepeat = $_POST["bgrepeat"];
    $audio_amazon = $_POST["amazon"];
    $audio_itunes = $_POST["itunes"];
    $mad_store = $_POST["mad_store"] == 'true';
    $remove_image = $_POST["remove_image"] == 'true';
    $remove_song = $_POST["remove_song"] == 'true';
    $bg_style = $_POST["bg_style"];
    $audio_tags = $_POST["tags"];
    $image_data = $old_image_data;
    
    if( $remove_song )
        $old_sound = '';
    if( $remove_image )
        $old_logo = '';
    
    $audio_logo = $old_logo;
    
    if(!empty($_FILES["logo"]["name"])){
        if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
            $image_data = get_image_data($_FILES["logo"]["tmp_name"]);
            if( $image_data != NULL )
            {
                $audio_logo = $artist_id."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
                @move_uploaded_file($_FILES['logo']['tmp_name'], '../artists/images/' . $audio_logo);
            }
            else
            {
                $image_data = $old_image_data;
                $postedValues['image_error'] = "Image format not recognized.";
            }
        }
    }
    
    $upload_sound_error = false;
    if(!empty($_FILES["audio"]["name"]))
    {
        if (is_uploaded_file($_FILES["audio"]["tmp_name"])) 
        {
            $uploaded_name = strtolower($_FILES["audio"]["name"]);
            $ext_parts = explode(".",$uploaded_name);
            $ext = $ext_parts[count($ext_parts) - 1];
            
            $filename = $artist_id."_".strtolower(rand(11111,99999)."_song.");
            $audio_sound = $filename . "mp3";
            $audio_sound_ogg = $filename . "ogg";
            
            $upload_file = $_FILES['audio']['tmp_name'];
            $mp3_file = '../artists/audio/' . $audio_sound;
            $ogg_file = '../artists/audio/' . $audio_sound_ogg;
            if( $ext == "mp3" )
            {
                @move_uploaded_file($upload_file, $mp3_file);
                @system("/usr/local/bin/ffmpeg -i $mp3_file -acodec libvorbis $ogg_file");
                $upload_audio_filename = $_FILES["audio"]["name"];
            }
            else
            {
                @system("/usr/local/bin/ffmpeg -i $upload_file -acodec libmp3lame $mp3_file",$retval);
                if( $retval == 0 )
                {
                    @system("/usr/local/bin/ffmpeg -i $upload_file -acodec libvorbis $ogg_file");
                    $upload_audio_filename = $_FILES["audio"]["name"];
                }
                else
                {
                    $postedValues['upload_error'] = 'Please upload audio files in mp3 format.';
                    $audio_sound = '';
                }
            }
        } else {
            if ($old_sound != $audio_sound) {
                $audio_sound = $old_sound;
            }
        }
    }else{
        $audio_sound = $old_sound;
    }
    if( $mad_store )
    {
        if( isset($old_product_id) && $old_product_id > 0 )
        {
            $product_id = $old_product_id;
        }
        else
        {
            $src = "../artists/images/$audio_logo";
            $dst = "../artists/products/$audio_logo";
            @copy($src,$dst);
            $values = array("artistid" => $artist_id,
                            "name" => $audio_name,
                            "description" => "Single",
                            "image" => $audio_logo,
                            "price" => 0.99,
                            "sku" => MADSONG,
                            );
            mysql_insert('mydna_musicplayer_ecommerce_products',$values);
            $product_id = mysql_insert_id();
        }
    }
    else
    {
        if( isset($old_product_id) )
        {
            $sql = "DELETE FROM mydna_musicplayer_ecommerce_products WHERE id = '$old_product_id'";
            mq($sql);
        }
        $product_id = NULL;
    }
    
    //INSERTING THE DATA
    
    $values = array("artistid" => $artist_id,
                    "name" => $audio_name,
                    "image" => $audio_logo,
                    "bgcolor" => $audio_bgcolor,
                    "bgposition" => $audio_bgposition,
                    "bgrepeat" => $audio_bgrepeat,
                    "audio" => $audio_sound,
                    "download" => $audio_download,
                    "amazon" => $audio_amazon,
                    "itunes" => $audio_itunes,
                    "product_id" => $product_id,
                    "upload_audio_filename" => $upload_audio_filename,
                    "bg_style" => $bg_style,
                    "tags" => $audio_tags,
                    "image_data" => $image_data,
                    );
    
    if( $song_id ) 
    {
        mysql_update('mydna_musicplayer_audio',$values,"id",$song_id);
    } 
    else 
    {
        mysql_insert('mydna_musicplayer_audio',$values);
        $new_song_id = mysql_insert_id();
    }
    
    $successMessage = "<div id='notify'>Success! You are being redirected...</div>";
    
    //showing the post value after the upload //	
    $postedValues['imageSource'] = "../artists/images/".$audio_logo;
    $postedValues['audio_sound'] = "../artists/audio/".$audio_sound;
    $postedValues['success'] = "1";
    
    $postedValues['postedValues'] = $_REQUEST;
    
    require_once 'include/utils.php';
    @create_abbrevs();
    
    if( $new_song_id )
    {
        $song = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id = '$new_song_id'"));
        $short_link = make_short_link($song['abbrev']);
        $update_text = "Check out my new song, $audio_name: $short_link via @myartistdna";
        
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'"));
        if( $artist['fb_setting'] == 'AUTO' )
        {
            send_fb_update($artist,$update_text);
            $postedValues['fb_update'] = TRUE;
        }
        if( $artist['tw_setting'] == 'AUTO' )
        {
            send_tweet($artist,$update_text);
            $postedValues['tw_update'] = TRUE;
        }
    }
    
    
    if( $_POST['ajax'] )
    {
        echo json_encode($postedValues);
        exit();
    }
    else
    {
        header("Location: /manage/artist_management.php?userId=$
    }
    
}

?>