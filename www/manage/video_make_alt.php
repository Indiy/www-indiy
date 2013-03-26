<?php
    
    require_once "../includes/config.php";
    require_once "../includes/functions.php";
    
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    error_reporting(E_ALL);
    
    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli' )
	{
		header("Location: /index.php");
		exit();
	}
    
    $user = get_current_user();
    $fd = fopen("/tmp/video_make_alts_$user.lock",'w+');
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
    
    echo "<html><body>\n";
    echo str_repeat(" ",1024);
    echo "<pre>\n";
    
    function video_maybe_convert_and_upload_file($client,$src_file,$ogv_file,&$extra)
    {
        $file_path = "/artists/files/$ogv_file";
        
        try
        {
            $ret = $client->headObject(array(
                                             'Bucket' => $GLOBALS['g_aws_static_bucket'],
                                             'Key' => $file_path,
                                             ));
            print "  Video($file_path) already exists, skipping\n";
            
            $extra['alts']['ogv'] = $file_path;
            
            return;
        }
        catch( Exception $e )
        {
        }
        
        $dst_file = tempnam("/tmp","ogv");
        
        @system("/usr/local/bin/ffmpeg -i $src_file -f ogg -vcodec libtheora -qscale 8 -acodec libvorbis $dst_file",$retval);
        if( $retval == 0 )
        {
            print "  successfully made ogv: $ogv_file\n";
            $args = array(
                          'Bucket' => $GLOBALS['g_aws_static_bucket'],
                          'Key' => $file_path,
                          'SourceFile' => realpath($dst_file),
                          'ACL' => 'public-read',
                          'CacheControl' => 'public, max-age=22896000',
                          'ContentType' => 'video/ogg',
                          );
            $client->putObject($args);
            
            $extra['alts']['ogv'] = $file_path;
            
            print "  uploaded: $file_path\n";
        }
        else
        {
            print "***failed to make ogv: $ogv_file\n";
        }
        unlink($dst_file);
    }
    
    function video_needs_update($extra)
    {
        if( !isset($extra['media_length']) )
        {
            print "  No media length\n";
            return TRUE;
        }
        if( !isset($extra['alts']['ogv']) )
        {
            print "  No ogv\n";
            return TRUE;
        }
        if( $extra['media_length'] < 1.0 )
        {
            print "  Invalid media length: " . $extra['media_length'] . "\n";
            return TRUE;
        }
        
        return FALSE;
    }
    
    try
    {
        $client = get_s3_client();
        
        $sql = "SELECT * FROM artist_files WHERE type='VIDEO' ORDER BY artist_files.extra_json ASC";
        $q = mq($sql);
        while( $file = mf($q) )
        {
            $id = $file['id'];
            $filename = $file['filename'];
            $extra_json = $file['extra_json'];
            if( strlen($extra_json) > 0 )
            {
                $extra = json_decode($extra_json,TRUE);
            }
            else
            {
                $extra = array();
            }
            
            $url = artist_file_url($filename);
            
            $path_parts = pathinfo($filename);
            $extension = $path_parts['extension'];
            $prefix = str_replace(".$extension","",$filename);
            
            print "filename: $filename, id: $id\n";
            flush();
            if( video_needs_update($extra) )
            {
                
                $sql = "UPDATE artist_files SET processing = 1 WHERE id='$id' AND processing = 0";
                $ret = mq($sql);
                $row_count = mysql_affected_rows();
                
                if( $ret == TRUE && $row_count > 0 )
                {
                    try
                    {
                        print "  got lock...\n";
                        
                        $tmp_file = tempnam("/tmp","mav");
                        
                        download_url_to_file($url,$tmp_file);
                        print "  downloaded url: $url\n";
                        
                        $media_length = get_audio_length($tmp_file);
                        print "  media_length: $media_length\n";
                        
                        $extra['media_length'] = $media_length;
                        
                        $ogv_file = "$prefix.ogv";
                        
                        video_maybe_convert_and_upload_file($client,$tmp_file,$ogv_file,$extra);
                    }
                    catch( Exception $e )
                    {
                        echo '  Inner Caught exception: ',  $e->getMessage(), "\n";
                    }
                    
                    $extra_json = json_encode($extra);
                    $updates = array(
                                     "extra_json" => $extra_json,
                                     "processing" => 0
                                     );
                    mysql_update('artist_files',$updates,'id',$id);
                    
                    print "  updated $id\n";
                    unlink($tmp_file);
                }
                else
                {
                    print "  failed to get lock, skipping...\n";
                }
            }
            else
            {
                print "  no update needed\n";
            }
            
            print "\n";
            flush();
        }
    }
    catch( Exception $e )
    {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    print "\n\n";
    print "done done\n"
    
?>