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
    $fd = fopen("/tmp/upload_user_content_$user.lock",'w+');
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

    function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) == $needle;
    }
    
    function maybe_upload_file($client,$filename,$alt_ext)
    {
        if( $alt_ext )
        {
            $path_parts = pathinfo($filename);
            $extension = strtolower($path_parts['extension']);
            
            $filename = str_replace(".$extension",$alt_ext,$filename);
        }


        $path = "../artists/files/$filename";
        $key = "artists/files/$filename";
        
        $realpath = realpath($path);
        
        if( file_exists($realpath ) )
        {
            try
            {
                $ret = $client->headObject(array(
                                                 'Bucket' => 'static2.madd3v.com',
                                                 'Key' => $key,
                                                 ));
                print " $key already exists, skipping\n";
                
                $extra['alts']['alt_key'] = $file_path;
                
                return;
            }
            catch( Exception $e )
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
        }
        else
        {
            if( !$alt_ext )
            {
                print "******file missing: $realpath\n";
            }
        }
    }

    try
    {
        
        $args = array(
                      'key' => $access_key_id,
                      'secret' => $secret_access_key,
                      );
        
        $client = S3Client::factory($args);
        
        //print "client: "; print_r($client); print "\n";
        
        
        
        $sql = "SELECT * FROM artist_files";
        $q = mq($sql);
        while( $file = mf($q) )
        {
            $filename = $file['filename'];

            maybe_upload_file($client,$filename,FALSE);
            maybe_upload_file($client,$filename,'.ogv');
            maybe_upload_file($client,$filename,'.ogg');
        }
    }
    catch( Exception $e )
    {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    print "\n\n";
    print "done done\n"

?>