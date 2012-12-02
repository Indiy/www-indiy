<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';

    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    error_reporting(E_ALL);
    
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli' )
	{
		header("Location: /index.php");
		exit();
	}
    session_write_close();
    
    $user = get_current_user();
    $fd = fopen("/tmp/convert_media_$user.lock",'w+');
    if( !$fd )
    {
        print "failed to open file\n";
        die();
    }
    if( !flock($fd,LOCK_EX | LOCK_NB) )
    {
        fclose($fd);
        print "Failed to get lock, done!\n";
        die();
    }

    ignore_user_abort(TRUE);
    set_time_limit(60*60);
    
    echo "<html><body><pre>\n";
    
    
    $artist_id = $_GET['artist_id'];
    
    if( !$artist_id )
    {
        die("Enter artist_id on url.");
    }
    
    
    $video_q = mq("SELECT * FROM artist_files WHERE type='VIDEO' AND artist_id='$artist_id'");
    
    while( $file = mf($video_q) )
    {
        $id = $file['id'];
        $filename = $file['filename'];
        $artist_id = $file['artist_id'];
        
        $path_parts = pathinfo($filename);
        $extension = strtolower($path_parts['extension']);
        
        if( $extension != 'mp4' )
        {
            $src_file = "../artists/files/$filename";
            $mp4_file = str_replace(".$extension",".mp4",$filename);
            $dst_file = "../artists/files/$mp4_file";
            if( !file_exists($dst_file) )
            {
                $args = "-i_qfactor 0.71 -qcomp 0.6 -qmin 10 -qmax 63 -qdiff 4 -trellis 0 -vcodec libx264 -s 640x360 -vb 300k -ab 64k -ar 44100 -threads 4";
                
                @system("/usr/local/bin/ffmpeg -i $src_file $args $dst_file",$retval);
                if( $retval == 0 )
                {
                    print "converted file: $filename to $mp4_file\n";
                }
                else
                {
                    print "error with file id: $id, filename: $filename, src_file: $src_file, dst_file: $dst_file\n";
                    file_error($id,$artist_id,"Please upload video files in mp4 format.");
                    continue;
                }
            }
            else
            {
                print "file ($mp4_file) already exists, using it.\n";
            }
            print "updated file to mp4: $id, file: $filename, new_filename: $mp4_file\n";
            mq("UPDATE mydna_musicplayer_video SET video='$mp4_file' WHERE artistid='$artist_id' AND video='$filename'");
            $filename = $mp4_file;
            $values = array("filename" => $filename);
            mysql_update("artist_files",$values,"id",$id);
        }
        else
        {
            //print "ignore id: $id, filename: $filename\n";
        }
        $path_parts = pathinfo($filename);
        $extension = strtolower($path_parts['extension']);
        
        $src_file = "../artists/files/$filename";
        $ret_height = exec("/usr/bin/mediainfo --Output=Video\;%Height% $src_file");
        $video_height = intval($ret_height);
        $ret_aspect = exec("/usr/bin/mediainfo --Output=Video\;%AspectRatio% $src_file");
        $aspect_ratio = floatval($ret_aspect);
        
        print "FILE: $filename, starting res: $video_height, aspect: $aspect_ratio\n";
        
        maybe_resize_media($src_file,$video_height,$aspect_ratio,1080,720,"2000k","192k");
        maybe_resize_media($src_file,$video_height,$aspect_ratio,720,480,"1500k","192k");
        maybe_resize_media($src_file,$video_height,$aspect_ratio,480,360,"800k","128k");
        maybe_resize_media($src_file,$video_height,$aspect_ratio,360,240,"400k","96k");
        maybe_resize_media($src_file,$video_height,$aspect_ratio,240,0,"300k","96k");
        
        print "FILE DONE!\n";
    }
    
    fclose($fd);
    print "\ndone done\n\n";
    
    
    function maybe_resize_media($src_file,$height,$aspect_ratio,$target_height,$inhibit_height,$vb,$ab)
    {
        if( $height <= $inhibit_height)
        {
            print "  SKIP RES: height: $height lower than inhibit height: $inhibit_height\n";
            return;
        }
        
        $target_width = floor($target_height * $aspect_ratio);
        
        $h_w = "{$target_height}x{$target_width}";
        
        $dst_file = str_replace("_{$target_height}p.mp4",".mp4",$src_file);
        
        if( !file_exists($dst_file) )
        {
            $args = "-i_qfactor 0.71 -qcomp 0.6 -qmin 10 -qmax 63 -qdiff 4 -trellis 0 -vcodec libx264 -s $h_w -vb $vb -ab $ab -ar 44100 -threads 4";
            
            @system("/usr/local/bin/ffmpeg -i $src_file $args $dst_file",$retval);
            if( $retval == 0 )
            {
                print "  SUCCESS: converted file to {$target_height}p: $src_file => $dst_file\n";
            }
            else
            {
                print "  ERROR! converted file to {$target_height}p: $src_file => $dst_file\n\n";
            }
        }
        else
        {
            print "  SKIP: already have file for {$target_height}p: $src_file => $dst_file\n";
        }
        
    }

?>