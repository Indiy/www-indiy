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
    $user = get_current_user();
    $fd = fopen("/tmp/convert_media_$user.lock",'rw');
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

    function file_error($id,$artist_id,$msg)
    {
        $values = array("error" => $msg);
        mysql_update("artist_files",$values,"id",$id);
        
        $artist = mf(mq("SELECT email FROM mydna_musicplayer WHERE id='$artist_id'"));
        
        if( $artist['email'] )
        {
            $to = $artist['email'];
            $subject = "MAD uploaded media conversion failed";
            
            $message = <<<END
One of your media files failed upload conversion.  Please login to your account
to try your upload again.

Be Heard. Be Seen. Be Independent.

END;
            $from = "no-reply@myartistdna.com";
            $headers = "From:" . $from;
            
            mail($to,$subject,$message,$headers);
            
            print "Emailed $to regarding file convertion failure.\n";
        }
    }


    $audio_q = mq("SELECT * FROM artist_files WHERE type='AUDIO' AND error IS NULL");
    
    while( $file = mf($audio_q) )
    {
        $id = $file['id'];
        $filename = $file['filename'];
        $artist_id = $file['artist_id'];
    
        $path_parts = pathinfo($filename);
        $extension = strtolower($path_parts['extension']);
        
        if( $extension != 'mp3' )
        {
            $src_file = "../artists/files/$filename";
            $mp3_file = str_replace(".$extension",".mp3",$filename);
            $dst_file = "../artists/files/$mp3_file";
            if( !file_exists($dst_file) )
            {
                @system("/usr/local/bin/ffmpeg -i $src_file -acodec libmp3lame $dst_file",$retval);
                if( $retval == 0 )
                {
                    print "converted file: $filename to $mp3_file\n";
                }
                else
                {
                    print "error with file id: $id, filename: $filename, src_file: $src_file, dst_file: $dst_file\n";
                    file_error($id,$artist_id,"Please upload audio files in mp3 format.");
                    continue;
                }
            }
            else
            {
                print "file ($mp3_file) already exists, using it.\n";
            }
            print "updated file to mp3: $id, file: $filename, new_filename: $mp3_file\n";
            mq("UPDATE mydna_musicplayer_audio SET audio='$mp3_file' WHERE artistid='$artist_id' AND audio='$filename'");
            $filename = $mp3_file;
            $values = array("filename" => $mp3_file);
            mysql_update("artist_files",$values,"id",$id);
        }
        else
        {
            //print "ignore id: $id, filename: $filename\n";
        }
        $path_parts = pathinfo($filename);
        $extension = strtolower($path_parts['extension']);
        
        $src_file = "../artists/files/$filename";
        $ogg_file = str_replace(".$extension",".ogg",$filename);
        $dst_file = "../artists/files/$ogg_file";
        if( file_exists($dst_file) )
        {
            print "id: $id, has ogg, dst_file: $dst_file, done!\n";
        }
        else
        {
            @system("/usr/local/bin/ffmpeg -i $src_file -acodec libvorbis $dst_file",$retval);
            if( $retval == 0 )
            {
                print "successfully made ogg: $ogg_file, id: $id\n";
            }
            else
            {
                print "failed to make ogg: $ogg_file, id: $id\n";
                file_error($id,$artist_id,"Your audio file failed conversion, please try your upload again.");
                continue;
            }
        }
    }
    
    $video_q = mq("SELECT * FROM artist_files WHERE type='VIDEO' AND error IS NULL");
    
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
                
                @system("/usr/local/bin/ffmpeg -i $src_file $args $dst_file");
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
            $values = array("filename" => $mp3_file);
            mysql_update("artist_files",$values,"id",$id);
        }
        else
        {
            //print "ignore id: $id, filename: $filename\n";
        }
        $path_parts = pathinfo($filename);
        $extension = strtolower($path_parts['extension']);
        
        $src_file = "../artists/files/$filename";
        $ogv_file = str_replace(".$extension",".ogv",$filename);
        $dst_file = "../artists/files/$ogv_file";
        if( file_exists($dst_file) )
        {
            print "id: $id, has ogv, dst_file: $dst_file, done!\n";
        }
        else
        {
            @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $dst_file $src_file",$retval);
            if( $retval == 0 )
            {
                print "successfully made ogv: $ogv_file, id: $id\n";
            }
            else
            {
                print "failed to make ogv: $ogv_file, id: $id\n";
                file_error($id,$artist_id,"Your video file failed conversion, please try your upload again.");
                continue;
            }
        }
    }
    
    fclose($fd);
    print "\ndone done\n\n";

?>