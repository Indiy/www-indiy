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
    
    
    function get_data($id)
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer_content WHERE id='$id'"));
        
        array_walk($row,cleanup_row_element);
        
        $image_path = "../artists/files/" . $row['image'];
        if( !empty($row['image']) )
            $row['image_url'] = $image_path;
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        return $row;
    }
    

    function do_POST()
    {
        $artist_id = $_POST["artistid"];
        $tab_id = $_POST["id"];
    
        if( $tab_id != "" ) 
        {
            $row = mf(mq("SELECT * FROM mydna_musicplayer_content WHERE id='$tab_id'"));
            $old_image_file = $row["image"];
        }
        
        $remove_image = $_POST["remove_image"] == 'true';
        
        if( $remove_image )
            $old_image_file = '';
        
        $content_name = my($_POST["name"]);
        $content_video = $_POST["video"];
        $content_body = my($_POST["body"]);
        
        $ret = artist_file_upload($artist_id,$_FILES["logo"],$old_image_file);
        $image_file = $ret['file'];
        
        $tables = "artistid|name|image|video|body";
        $values = "$artist_id|$content_name|$image_file|$content_video|$content_body";
        
        if( $tab_id != "" )
        {
            update("mydna_musicplayer_content",$tables,$values,"id",$tab_id);
        } 
        else 
        {
            insert("mydna_musicplayer_content",$tables,$values);
            $tab_id = mysql_insert_id();
        }
        $postedValues['imageSource'] = $image_file;
        $postedValues['success'] = "1";
        $postedValues['postedValues'] = $_REQUEST;
        
        if( $_POST['ajax'] )
        {
            $postedValues['tab_data'] = get_data($tab_id);
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