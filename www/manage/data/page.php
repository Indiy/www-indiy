<?php  

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
    
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}

    $method = $_SERVER['REQUEST_METHOD'];
    if( isset($_REQUEST['method']) )
        $method = strtoupper($_REQUEST['method']);

    if( $method == 'POST' )
        do_POST();
    else if( $method == 'ORDER' )
        do_ORDER();
    else
        print "Bad method\n";

    exit();
    
function do_ORDER()
{
    $array = $_REQUEST['arrayorder'];
    $count = 1;
    foreach( $array as $id )
    {
        $values = array("order" => $count);
        mysql_update('mydna_musicplayer_audio',$values,"id",$id);
        ++$count;
    }
    
    $ret = array("success" => 1);
    echo json_encode($ret);
    exit();
}

function get_page_data($page_id)
{
    $row = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id='$page_id'"));
    
    $row['short_link'] = make_short_link($row['abbrev']);
    array_walk($row,cleanup_row_element);
    $row['download'] = $row['download'] == "0" ? FALSE : TRUE;
    $row['product_id'] = $row['product_id'] > 0 ? intval($row['product_id']) : FALSE;
    if( !empty($row['image']) )
        $row['image_url'] = artist_file_url($row['image']);
    else
        $row['image_url'] = "images/photo_video_01.jpg";
    return $row;
}

function do_POST()
{
    $artist_id = $_POST['artistid'];

    $upload_audio_filename = NULL;
    $song_id = $_POST['id'];
    $extra = array();
    if( $song_id )
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id='$song_id'"));
        
        $old_logo = $row["image"];
        $old_sound = $row["audio"];
        $old_product_id = $row["product_id"];
        $upload_audio_filename = $row["upload_audio_filename"];
        $old_image_data = $row["image_data"];
        $extra = array();
        if( $row['extra_json'] )
        {
            $extra_json = $row['extra_json'];
            $extra = json_decode($extra_json,TRUE);
        }
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
    $image_data = get_image_data(PATH_TO_ROOT . "artists/files/$audio_logo");

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
                            "sku" => "MADSONG",
                            "type" => "DIGITAL",
                            );
            mysql_insert('mydna_musicplayer_ecommerce_products',$values);
            $product_id = mysql_insert_id();
        }
        
        mq("DELETE FROM product_files WHERE artist_id='$artist_id' AND product_id='$product_id'");
        
        $file = mf(mq("SELECT * FROM artist_files WHERE artist_id='$artist_id' AND filename='$audio_sound'"));
        if( $file )
        {
            $filename = $file['filename'];
            $upload_filename = $file['upload_filename'];
            
            $values = array("product_id" => $product_id,
                            "filename" => $filename,
                            "upload_filename" => $upload_filename,
                            );
            mysql_insert("product_files",$values);
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
    
    if( $audio_sound )
    {
        $audio_path = "../../artists/files/$audio_sound";
        $extra['audio_length'] = get_audio_length($audio_path);
    }
    
    $extra_json = json_encode($extra);
    
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
                    "extra_json" => $extra_json,
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