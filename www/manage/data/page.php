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
        do_POST();
    else
        print "Bad method\n";

    exit();

function get_page_data($page_id)
{
    $row = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id='$page_id'"));
    
    $row['short_link'] = make_short_link($row['abbrev']);
    array_walk($row,cleanup_row_element);
    $row['download'] = $row['download'] == "0" ? FALSE : TRUE;
    $row['product_id'] = $row['product_id'] > 0 ? intval($row['product_id']) : FALSE;
    $image_path = "../artists/files/" . $row['image'];
    if( !empty($row['image']) )
        $row['image'] = $image_path;
    else
        $row['image'] = "images/photo_video_01.jpg";
    return $row;
}

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
    
    $audio_logo = $_POST['image_drop'];

    $audio_sound = $_POST['song_drop'];
    
    if( $mad_store )
    {
        if( isset($old_product_id) && $old_product_id > 0 )
        {
            $product_id = $old_product_id;
        }
        else
        {
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
        $song_id = mysql_insert_id();
    }
    
    $successMessage = "<div id='notify'>Success! You are being redirected...</div>";
    
    //showing the post value after the upload //	
    $postedValues['imageSource'] = $audio_logo;
    $postedValues['audio_sound'] = $audio_sound;
    $postedValues['success'] = "1";
    
    $postedValues['postedValues'] = $_REQUEST;
    
    require_once '../include/utils.php';
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
        $page_data = get_page_data($song_id);
        $postedValues['page_data'] = $page_data;
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