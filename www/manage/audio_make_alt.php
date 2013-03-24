<?php
    
    require_once "../includes/config.php";
    require_once "../includes/functions.php";
    
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    $ALT_IMAGE_REV_KEY = "1";
    
    error_reporting(E_ALL);
    
    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli' )
	{
		header("Location: /index.php");
		exit();
	}
    
    $user = get_current_user();
    $fd = fopen("/tmp/audio_make_alts_$user.lock",'w+');
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
    
    function audio_maybe_convert_and_upload_file($client,$src_file,$ogg_file,&$extra)
    {
        $file_path = "/artists/files/$ogg_file";
        
        try
        {
            $ret = $client->headObject(array(
                                             'Bucket' => $GLOBALS['g_aws_static_bucket'],
                                             'Key' => $file_path,
                                             ));
            print "  Audio($file_path) already exists, skipping\n";
            
            $extra['alts']['ogg'] = $file_path;
            
            return;
        }
        catch( Exception $e )
        {
        }
        
        $dst_file = tempnam("/tmp","ogg");
        
        @system("/usr/local/bin/ffmpeg -i $src_file -acodec libvorbis $dst_file",$retval);
        if( $retval == 0 )
        {
            print "  successfully made ogg: $ogg_file\n";
            $args = array(
                          'Bucket' => $GLOBALS['g_aws_static_bucket'],
                          'Key' => $file_path,
                          'SourceFile' => realpath($dst_file),
                          'ACL' => 'public-read',
                          'CacheControl' => 'public, max-age=22896000',
                          'ContentType' => 'audio/ogg',
                          );
            $client->putObject($args);

            $extra['alts']['ogg'] = $file_path;

            unlink($ogg_file);
            print "  uploaded: $file_path\n";
        }
        else
        {
            print "***failed to make ogg: $ogg_file\n";
        }
    }
    
    try
    {
        $client = get_s3_client();
        
        $sql = "SELECT * FROM artist_files WHERE type='AUDIO' ORDER BY artist_files.extra_json ASC";
        $q = mq($sql);
        while( $file = mf($q) )
        {
            $id = $file['id'];
            $filename = $file['filename'];
            $extra_json = $file['extra_json'];
            if( $extra_json && strlen($extra_json) > 0 )
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
            
            $src_data = file_get_contents($url);
            $tmp_file = tempnam("/tmp","mac");
            file_put_contents($tmp_file,$src_data);
            
            $audio_length = get_audio_length($tmp_file);

            print "filename: $filename ($audio_length secs)\n";
            
            $extra['media_length'] = $audio_length;
            
            $ogg_file = "$prefix.ogg";
            
            audio_maybe_convert_and_upload_file($client,$tmp_file,$ogg_file,$extra);

            $extra_json = json_encode($extra);
            mysql_update('artist_files',array("extra_json" => $extra_json),'id',$id);
            
            print "  updated $id\n";
            unlink($tmp_file);
            flush();

            break;
        }
    }
    catch( Exception $e )
    {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    print "\n\n";
    print "done done\n"
    
?>