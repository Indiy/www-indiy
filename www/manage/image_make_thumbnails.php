<?php
    
    error_reporting(E_ALL);
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
    
    function maybe_convert_and_upload_file($client,$src_image,$prefix,$width,$height,&$extra)
    {
        global $ALT_IMAGE_REV_KEY;
        
        $src_imagex = imagesx($src_image);
        $src_imagey = imagesy($src_image);

        $dst_imagex = $width;
        if( $height )
        {
            $dst_imagey = $height;
        }
        else
        {
            $dst_imagey = round($src_imagey / $src_imagex * $dst_imagex);
        }

        $alt_key = "w{$width}";
        if( $height )
        {
            $alt_key .= "_h{$height}";
        }
        
        $file_path = "/artists/thumbs/{$prefix}_{$alt_key}_{$ALT_IMAGE_REV_KEY}.jpg";

        try
        {
            $ret = $client->headObject(array(
                                             'Bucket' => $GLOBALS['g_aws_static_bucket'],
                                             'Key' => $file_path,
                                             ));
            print "  Image($file_path) already exists, skipping\n";
            
            $extra['alts'][$alt_key] = $file_path;
            
            return;
        }
        catch( Exception $e )
        {
        }
        
        $dst_imagex = $width;
        if( $height )
        {
            $dst_imagey = $height;
        }
        else
        {
            $dst_imagey = round($src_imagey / $src_imagex * $dst_imagex);
        }
        
        print "dst: x,y: $dst_imagex,$dst_imagey\n";
        
        $dst_image = imagecreatetruecolor($dst_imagex, $dst_imagey);
        
        imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_imagex,
                           $dst_imagey, $src_imagex, $src_imagey);
        
        
        ob_start();
        imagejpeg($dst_image,NULL,100);
        $img_data = ob_get_clean();
        
        imagedestroy($dst_image);
        
        $args = array(
                      'Bucket' => $GLOBALS['g_aws_static_bucket'],
                      'Key' => $file_path,
                      'Body' => $img_data,
                      'ACL' => 'public-read',
                      'CacheControl' => 'public, max-age=22896000',
                      'ContentType' => 'image/jpeg',
                      );
        $client->putObject($args);
        
        $extra['alts'][$alt_key] = $file_path;

        print " uploaded: $file_path\n";
    }
    
    function needed_image_sizes($width)
    {
        $needed_widths = array(320,480,640,768,800,960,1024,
                               1080,1280,1440,1536,1600,2048,
                               400,500,600,700,900,1000,1100,1200,1300,
                               1400,1500
                               );
        
        $ret = array();
        
        foreach( $needed_widths as $w )
        {
            if( $w < $width )
                $ret[] = array($w,FALSE);
        }
        
        $ret[] = array(200,FALSE);
        $ret[] = array(65,44);
        $ret[] = array(210,132);
        
        return $ret;
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

                print "filename: $filename ($width,$height)\n";
                
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
                    maybe_convert_and_upload_file($client,$src_image,$prefix,$size[0],$size[1],$extra);
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