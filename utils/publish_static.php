<?php

    $base_web_dir = $argv[1];
    
    if( !$base_web_dir || strlen($base_web_dir) == 0 )
    {
        print "Usage: php {$argv[0]} <base web dir>\n";
        print "\n";
        die();
    }
    
    $base_web_dir = realpath($base_web_dir);
    print "Base Dir: $base_web_dir\n";
    
    require_once "$base_web_dir/server_config.php";
    require_once "$base_web_dir/includes/aws.phar";
    
    
    $html_dir = $base_web_dir . "/html";

    $file_map = array();
    
    function get_content_type($extension)
    {
        if( $extension == 'eot')
        {
            return "application/vnd.ms-fontobject";
        }
        else if( $extension == 'ttf' )
        {
            return "font/ttf";
        }
        else if( $extension == 'otf' )
        {
            return "font/opentype";
        }
        else if( $extension == 'woff' )
        {
            return "font/x-woff";
        }
        else if( $extension == 'svg' )
        {
            return "image/svg+xml";
        }
        else if( $extension == 'css' )
        {
            return "text/css";
        }
        return FALSE;
    }
    
    function do_static_dir($html_dir,$web_path)
    {
        global $file_map;
        global $client;
        
        $dir = "$html_dir$web_path";
        
        print "doing static dir: $dir\n";
        
        $dir_items = scandir($dir);
        
        foreach( $dir_items as $item )
        {
            $src_file = "$dir/$item";
        
            if( !is_file($src_file) )
            {
                print "  skip non-file: $item\n";
                continue;
            }
            
            $web_file = "$web_path/$item";

            $hash = hash_file("md5",$src_file);
            
            $path_parts = pathinfo($src_file);
            $extension = $path_parts['extension'];
            $key_file = str_replace(".$extension","_$hash.$extension",$item);
            
            $key = "$web_path/$key_file";
            print "  key: $key\n";
            
            try
            {
                $ret = $client->headObject(array(
                                                 'Bucket' => $GLOBALS['g_aws_static_bucket'],
                                                 'Key' => $key,
                                                 ));
                print "  $key already exists, skipping\n";
            }
            catch( Exception $e )
            {
                $args = array(
                              'Bucket' => $GLOBALS['g_aws_static_bucket'],
                              'Key' => $key,
                              'SourceFile' => $src_file,
                              'ACL' => 'public-read',
                              'CacheControl' => 'public, max-age=300',
                              );
                $content_type = get_content_type($extension);
                if( $content_type )
                {
                    $args['ContentType'] = $content_type;
                }
                
                $client->putObject($args);
                print "  uploaded: $src_file\n";
            }
            $file_map[$web_file] = $key;
        }
        
        print "done static dir\n";
    }
    
    
    $args = array(
                  'key' => $GLOBALS['g_access_key_id'],
                  'secret' => $GLOBALS['g_secret_access_key'],
                  );
    
    $client = Aws\S3\S3Client::factory($args);
    
    do_static_dir($html_dir,"/css");

    print "file_map: \n";
    var_dump($file_map);
    
    print "\n\n";
    print "done done\n";
?>