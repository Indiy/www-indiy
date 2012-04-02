<?php
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
    
    if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
    
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
        if( empty($row['upload_video_filename'] )
           $row['upload_video_filename'] = $row['image'];

        $image_path = "../artists/images/" . $row['image'];
        if( !empty($row['image']) )
            $row['image'] = $image_path;
        else
            $row['image'] = "images/photo_video_01.jpg";
       
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
            $old_logo = $row["image"];
            $old_sound = $row["video"];
            $upload_video_filename = $row["upload_video_filename"];
        }
        
        $video_name = my($_POST["name"]);
        $video_tags = $_POST["tags"];
        
        if( $_POST["remove_video_image"] == 'true' )
            $old_logo = '';
        if( $_POST["remove_video"] == 'true' )
            $old_sound = '';
        
        // Upload Image
        if(!empty($_FILES["logo"]["name"]))
        {
            if (is_uploaded_file($_FILES["logo"]["tmp_name"]))
            {
                $video_logo = $artist_id."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
                @move_uploaded_file($_FILES['logo']['tmp_name'], PATH_TO_ROOT . 'artists/images/' . $video_logo);
            } 
            else 
            {
                if ($old_logo != "") 
                {
                    $video_logo = $old_logo;
                }
            }
        }
        else
        {
            $video_logo = $old_logo;
        }
        
        // Upload video
        if(!empty($_FILES["video"]["name"]))
        {
            if (is_uploaded_file($_FILES["video"]["tmp_name"]))
            {
                ignore_user_abort(true);
                set_time_limit(0);
                $tmp_file = $_FILES['video']['tmp_name'];
                $ext = explode(".",$_FILES['video']['name']);
                $upload_ext = strtolower($ext[count($ext)-1]);
                
                $video_sound_mp4 = $artist_id . '_' . strtolower( rand(11111,99999) . '_video.mp4' );
                $dest_file = PATH_TO_ROOT . 'vid/' . $video_sound_mp4;
                $dest_file_ogv = str_replace('.mp4','.ogv',$dest_file);
                
                $args = "-i_qfactor 0.71 -qcomp 0.6 -qmin 10 -qmax 63 -qdiff 4 -trellis 0 -vcodec libx264 -s 640x360 -vb 300k -ab 64k -ar 44100 -threads 4";
                if( $upload_ext == "mp4" )
                {
                    @move_uploaded_file($tmp_file, $dest_file);
                    @chmod($dest_file, 0644);
                    //@system("/usr/bin/qafaststart $dest_file");
                    @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $dest_file_ogv $dest_file");
                    
                    $video_sound = $video_sound_mp4;
                    $upload_video_filename = $_FILES["video"]["name"];
                }
                else if( $upload_ext == "mov" )
                {
                    @system("/usr/local/bin/ffmpeg -i $tmp_file $args $dest_file");
                    @unlink($_FILES['video']['tmp_name']);
                    //@system("/usr/bin/qafaststart $dest_file");
                    @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $dest_file_ogv $dest_file");
                    
                    $video_sound = $video_sound_mp4;
                    $upload_video_filename = $_FILES["video"]["name"];
                }
                else
                {
                    $postedValues['upload_error'] = 'Please upload video files in MP4 or MOV format.';
                    $video_sound = '';
                }
            } 
            else 
            {
                if ($old_sound != "") 
                {
                    $video_sound = $old_sound;
                }
            }
        }
        else
        {
            $video_sound = $old_sound;
        }
        
        
        $tables = "artistid|name|image|video|upload_video_filename|tags";
        $values = "$artist_id|$video_name|$video_logo|$video_sound|$upload_video_filename|$video_tags";
        
        if( $video_id != "") 
        {
            update($database,$tables,$values,"id",$video_id);
        } 
        else 
        {
            insert($database,$tables,$values);
            $video_id = mysql_insert_id();
        }
        
        $postedValues['imageSource'] = $video_logo;
        $postedValues['video_sound'] = $video_sound;
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
