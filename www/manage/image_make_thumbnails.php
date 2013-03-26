<?php
    
    error_reporting(E_ALL);
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
    
    
    function image_needs_update($extra)
    {
        if( !isset($extra['image_data'])
           || !isset($extra['image_data']['width'])
           || !isset($extra['image_data']['height'])
           )
        {
            print "  No valid image_data\n";
            return TRUE;
        }
        if( !isset($extra['alts']) )
        {
            print "  No alts\n";
            return TRUE;
        }
        if( count($extra['alts']) < 3 )
        {
            print "  Invalid alt set " . count($extra['alts']) . "\n";
            return TRUE;
        }
        
        return FALSE;
    }
    
    try
    {
        $client = get_s3_client();
        
        $sql = "SELECT * FROM artist_files WHERE type='IMAGE' ORDER BY artist_files.extra_json ASC";
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
            
            print "filename: $filename\n";
            
            if( image_needs_update($extra) )
            {
                $sql = "UPDATE artist_files SET processing = 1 WHERE id='$id' AND processing = 0";
                $ret = mq($sql);
                $row_count = mysql_affected_rows();
                
                if( $ret == TRUE && $row_count > 0 )
                {
                    $url = artist_file_url($filename);

                    $path_parts = pathinfo($filename);
                    $extension = $path_parts['extension'];
                    $prefix = str_replace(".$extension","",$filename);
                    
                    $src_data = file_get_contents($url);
                    $src_image = imagecreatefromstring($src_data);
                    
                    $width = imagesx($src_image);
                    $height = imagesy($src_image);

                    print " size: ($width,$height)\n";
                    
                    if( !isset($extra['image_data']) )
                    {
                        $image_data = array("width" => $width,
                                            "height" => $height,
                                            );
                        $extra['image_data'] = $image_data;
                    }
                    
                    $needed_sizes = needed_image_sizes($width);
                    
                    foreach( $needed_sizes as $i => $size )
                    {
                        image_maybe_convert_and_upload_file($client,$src_image,$prefix,$size[0],$size[1],$extra);
                    }
                    
                    $extra_json = json_encode($extra);
                    
                    $updates = array(
                                     "extra_json" => $extra_json,
                                     "processing" => 0
                                     );
                    mysql_update('artist_files',$updates,'id',$id);
                    print "  updated $id\n";
                }
                else
                {
                    print "  no update needed\n";
                }
            }
            else
            {
                print " no update needed!"
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