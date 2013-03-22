<?php
    
    $access_key_id = "AKIAIP2VCXXJMBG4K75Q";
    $secret_access_key = "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";
    
    require_once "../includes/config.php";
    require_once "../includes/functions.php";
    
    require_once "../../includes/aws.phar";
    
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
    $fd = fopen("/tmp/make_thumbnails_$user.lock",'w+');
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
    
    use Aws\S3\S3Client;
    
    function maybe_convert_and_upload_file($client,$src_image,$prefix,$width,$height,&$alts);
    {
        $src_imagex = imagesx($src_image);
        $src_imagey = imagesy($src_image);

        if( $src_imagex <= $width )
        {
            print " skip resize of image: $prefix ($src_imagex <= $width)";
            return;
        }
        
        $dst_key = "/test/$prefix_w$width";
        if( $height )
        {
            $dst_key .= "_h$height";
        }
        $dst_key .= ".jpg";
        
        try
        {
        
            $ret = $client->headObject(array(
                                             'Bucket' => 'static2.madd3v.com',
                                             'Key' => $dst_key,
                                             ));
            
            var_dump($ret);
            
        
        }
        catch( Exception $e )
        {
            print "Exception: $e\n";
        }
            /*
        if(  )
        {
            
            $args = array(
                          'Bucket' => 'static2.madd3v.com',
                          'Key' => $key,
                          'SourceFile' => $realpath,
                          'ACL' => 'public-read',
                          'CacheControl' => 'public, max-age=22896000'
                          );
            $client->putObject($args);
            print " uploaded: $filename\n";
        }
        */
    }
    
    try
    {
        
        $args = array(
                      'key' => $access_key_id,
                      'secret' => $secret_access_key,
                      );
        
        $client = S3Client::factory($args);
        
        //print "client: "; print_r($client); print "\n";
        
        
        
        $sql = "SELECT * FROM artist_files WHERE type='IMAGE'";
        $q = mq($sql);
        while( $file = mf($q) )
        {
            $filename = $file['filename'];
            
            $url = artist_file_url($filename);

            $path_parts = pathinfo($filename);
            $extension = $path_parts['extension'];
            $prefix = str_replace(".$extension","",$filename);
            
            $src_data = file_get_contents($url);
            $src_image = imagefromstring($src_data);
            
            $alts = array();
            
            maybe_convert_and_upload_file($client,$src_image,$prefix,200,FALSE,$alts);
            //maybe_convert_and_upload_file($client,$prefix,210,132,$alts);
            //maybe_convert_and_upload_file($client,$prefix,65,45,$alts);
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