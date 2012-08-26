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
        $row = mf(mq("SELECT * FROM mydna_musicplayer_video WHERE id='$id'"));
       
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
        $video_id = $_POST["id"];
        
        $upload_video_filename = NULL;
        if( $video_id != "" ) 
        {
            $row = mf(mq("SELECT `id`,`image`,`video` FROM mydna_musicplayer_video WHERE `id`='$video_id'"));
            $old_image_file = $row["image"];
            $old_video_file = $row["video"];
            $upload_video_filename = $row["upload_video_filename"];
            $old_image_data = $row['image_data'];
        }
        
        $video_name = my($_POST["name"]);
        $video_tags = $_POST["tags"];
        $error = NULL;
        
        if( $_POST["remove_video_image"] == 'true' )
            $old_logo = '';
        if( $_POST["remove_video"] == 'true' )
            $old_sound = '';
            
        
        $ret = artist_file_upload($artist_id,$_FILES["logo"],$old_image_file);
        $image_file = $ret['file'];
        if( isset($ret['image_data']) )
            $image_data = $ret['image_data'];
        else
            $image_data = $old_image_data;
        
        $ret = artist_file_upload($artist_id,$_FILES["video"],$old_video_file);
        $video_file = $ret['file'];
        if( isset($ret['upload_error']) )
            $postedValues['upload_error'] = $ret['upload_error'];
        

        $values = array("artistid" => $artist_id,
                        "name" => $video_name,
                        "image" => $image_file,
                        "video" => $video_file,
                        "upload_video_filename" => $upload_video_filename,
                        "tags" => $tags,
                        "error" => $error,
                        "image_data" => $image_data,
                        );
        
        if( $video_id != "") 
        {
            mysql_update('mydna_musicplayer_video',$values,"id",$video_id);
        } 
        else 
        {
            mysql_insert('mydna_musicplayer_video',$values);
            $video_id = mysql_insert_id();
        }
        
        $postedValues['imageSource'] = $video_logo;
        $postedValues['video_sound'] = $video_file;
        $postedValues['success'] = "1";
        $postedValues['postedValues'] = $_REQUEST;
        
        if( $_POST['ajax'] )
        {
            $postedValues['video_data'] = get_data($video_id);
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
