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
    
    function starts_with($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }
    
    function parse_css($src_file,$html_dir,$web_path)
    {
        global $file_map;
        
        $dst_file = tempnam("/tmp","css");
        
        $contents = file_get_contents($src_file);
        
        $arr = array();
        preg_match_all('/url\([\'\"]?([^\'\"\)]*)[\'\"]?\)/', $contents, $arr, PREG_PATTERN_ORDER);
        
        foreach($arr[0] as $i => $item)
        {
            print "    css item: $item\n";
        
            $url_query = $arr[1][$i];

            $url_arr = array();
            preg_match('/[^\?\#]*/',$url_query,$url_arr);
            $rel_url = $url_arr[0];
            
            $end_url = substr($url_query,strlen($rel_url));
            
            if( starts_with($rel_url,'/') )
            {
                $url = $rel_url;
            }
            else
            {
                $path = "$html_dir$web_path/$rel_url";
                $abs_path = realpath($path);
                $url = str_replace($html_dir,'',$abs_path);
            }
            
            
            print "    url: $url\n";
            if( isset($file_map[$url]) )
            {
                $new_url = $file_map[$url];
                $new_item = "url($new_url$end_url)";
                print "    new_item: $new_item\n";
                
                $contents = str_replace($item,$new_item,$contents);
            }
            else
            {
                print "***didnt $url in file_map\n";
            }
        }
        
        file_put_contents($dst_file,$contents);
        
        return $dst_file;
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
            if( starts_with($src_file,'.') )
            {
                print "  skip dot file: $item\n";
                continue;
            }
            
            $web_file = "$web_path/$item";

            $path_parts = pathinfo($src_file);
            $extension = $path_parts['extension'];
            
            if( $extension == 'css' )
            {
                $src_file = parse_css($src_file,$html_dir,$web_path);
            }

            $hash = hash_file("md5",$src_file);
            $key_file = str_replace(".$extension","_$hash.$extension",$item);
            
            
            $key = "$web_path/$key_file";
            print "  key: $key\n";
            
            try
            {
                $ret = $client->headObject(array(
                                                 'Bucket' => $GLOBALS['g_aws_static_bucket'],
                                                 'Key' => $key,
                                                 ));
                print "    $key already exists, skipping\n";
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
                print "    uploaded: $src_file\n";
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
    
    do_static_dir($html_dir,"/images");
    do_static_dir($html_dir,"/fonts");
    do_static_dir($html_dir,"/css");

    print "file_map: \n";
    var_dump($file_map);
    
    print "\n\n";
    print "done done\n";
?>