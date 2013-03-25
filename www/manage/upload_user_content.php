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
                                                 'Bucket' => $GLOBAL['g_aws_static_bucket'],
                                                 'Key' => $key,
                                                 ));
                print " $key already exists, skipping\n";
                
                $extra['alts']['alt_key'] = $file_path;
                
                return;
            }
            catch( Exception $e )
            {
                $args = array(
                              'Bucket' => $GLOBAL['g_aws_static_bucket'],
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
                print "******file missing: $filename, $path, $realpath\n";
            }
        }
    }

    $i = 0;

    try
    {
        $client = get_s3_client();
        
        $sql = "SELECT * FROM artist_files ORDER BY RAND()";
        $q = mq($sql);
        while( $file = mf($q) )
        {
            $filename = $file['filename'];

            maybe_upload_file($client,$filename,FALSE);
            maybe_upload_file($client,$filename,'.ogv');
            maybe_upload_file($client,$filename,'.ogg');
            $i += 1;
            
            flush();
        }
    }
    catch( Exception $e )
    {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    print "\n\n";
    print "did $i items\n";
    print "done done\n"

?>