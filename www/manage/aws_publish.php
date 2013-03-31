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
    $fd = fopen("/tmp/aws_publish_$user.lock",'w+');
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
                                                 'Bucket' => $GLOBALS['g_aws_static_bucket'],
                                                 'Key' => $key,
                                                 ));
                print " $key already exists, skipping\n";
                
                $extra['alts']['alt_key'] = $file_path;
                
                return;
            }
            catch( Exception $e )
            {
                $args = array(
                              'Bucket' => $GLOBALS['g_aws_static_bucket'],
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
    
    function make_s3_bucket($s3_client,$url,&$extra)
    {
        if( isset($extra['aws']['s3_bucket']))
        {
            $s3_bucket = $extra['aws']['s3_bucket'];
            
            if( $s3_client->doesBucketExist($s3_bucket) )
            {
                $web_config = $s3_client->getBucketWebsite(array('Bucket' => $s3_bucket));
                print "  web_config: ";
                var_dump($web_config);
                print "\n";
                $suffix = $web_config['IndexDocument']['Suffix'];
                print "  Got suffix for web config: $suffix\n";
                print "  s3 bucket: $s3_bucket\n";
                return TRUE;
            }
        }
        
        $suffix = str_replace("http://www.","",trueSiteUrl());
        $s3_bucket = FALSE;
        foreach( array("","2","3","4","5","6","7","8") as $bucket_ver )
        {
            try
            {
                $s3_bucket = "$url$bucket_ver.$suffix";
            
                $s3_client->createBucket(array('Bucket' => $s3_bucket));
                break;
            }
            catch( Exception $e )
            {
                $s3_bucket = FALSE;
            }
        }
        
        if( $s3_bucket )
        {
            $extra['s3_bucket'] = $s3_bucket;
            
            $args = array(
                          'Bucket' => $s3_bucket,
                          'IndexDocument' => array('Suffix' => "index.html"),
                          );
            $s3_client->putBucketWebsite($args);
            
            print "  s3 bucket: $s3_bucket\n";
            
            return TRUE;
        }
        print "  failed to make s3 bucket!\n";

        return FALSE;
    }
    function make_cloudfront_distro($cf_client,$artist,$extra)
    {
        if( isset($extra['aws']['cloudfront']['id']))
        {
            try
            {
                $cf_id = $extra['aws']['cloudfront']['id'];
                
                $cf_data = $cf_client->getDistribution(array('Id' => $cf_id));
                
                print "Cloud Front Data:";
                var_dump($cf_data);
                print "\n";
                
                return TRUE;
            }
            catch( Exception $e )
            {
                
            }
        }
        
        $artist_id = $artist['id'];
        $s3_bucket = $extra['aws']['s3_bucket'];
        $url = $artist['url'];
        $custom_domain = $artist['custom_domain'];
        
        $full_url = str_replace("http://www.","$url.",trueSiteUrl());
        $aliases = array($full_url);
        
        if( $custom_domain )
        {
            $aliases[] = $custom_domain;
            $aliases[] = "www.$custom_domain";
        }
        
        $s3_domain_name = "$s3_bucket.s3.amazonaws.com";
        $origin_id = "S3-$s3_domain_name";
        $origin = array(
                        'Id' => $origin_id,
                        'DomainName' => $s3_domain_name,
                        );
        
        $origins = array($origin);
        
        $default_cache_behavior = array(
                                        'TargetOriginId' => $origin_id,
                                        'ForwardedValues' => array('QueryString' => FALSE),
                                        'TrustedSigners' => array('Enabled' => FALSE,'Quantity' => 0),
                                        'ViewerProtocolPolicy' => 'allow-all',
                                        'MinTTL' => 0,
                                        );
        
        $logging =  array(
                          'Enabled' => FALSE,
                          'Bucket' => $s3_domain_name,
                          'Prefix' => 'logs/',
                          );
        
        $args = array(
                      'CallerReference' => "cf_" . time(),
                      'Aliases' => array('Quantity' => count($aliases),'Aliases' => $aliases),
                      'DefaultRootObject' => 'index.html',
                      'Origins' => array('Quantity' => count($origins),'Origins' => $origins),
                      'DefaultCacheBehavior' => $default_cache_behavior,
                      'CacheBehaviors' => array('Quantity' => 0),
                      'Comment' => "artist_id: $artist_id",
                      'Logging' => $logging,
                      'Enabled' => TRUE,
                      );
        
        $ret = $cf_client->createDistribution($args);

        print " createDistribution: ";
        var_dump($ret);
        print "\n";
        
        $extra['aws']['cloudfront']['id'] = $ret['Id'];
        $extra['aws']['cloudfront']['domain_name'] = $ret['DomainName'];
        return TRUE;
    }
    
    $i = 0;
    
    try
    {
        $s3_client = get_s3_client();
        $cf_client = get_cf_client();
        
        $sql = "SELECT * FROM mydna_musicplayer WHERE url != '' ORDER BY id ASC";
        $q = mq($sql);
        while( $artist = mf($q) )
        {
            try
            {
                $id = $artist['id'];
                $artist_name = $artist['artist'];
                $url = $artist['url'];
                $extra_json = $artist['extra_json'];
                $extra = json_decode($extra_json,TRUE);
                
                print "artist: $artist_name, url: $url\n";
                
                //print "  extra:";
                //var_dump($extra);
                //print "\n";
                
                if( isset($extra['aws']) && $extra['aws']['cloudfront_enable'] )
                {
                    if( !make_s3_bucket($s3_client,$url,$extra) )
                    {
                        print "  Failed to create s3 bucket\n";
                        continue;
                    }
                    if( !make_cloudfront_distro($cf_client,$artist,$extra) )
                    {
                        print "  Failed to create CloudFront distro\n";
                        continue;
                    }
                    
                    $extra_json = json_encode($extra);
                    
                    $updates = array('extra_json' => $extra_json);
                    mysql_update('mydna_musicplayer',$updates,'id',$id);
                    
                    print "  Updated: $id\n";
                }
                else
                {
                    print "  Cloud Front not enabled, skip!\n";
                }
            }
            catch( Exception $e )
            {
               echo "  Caught exception: ",  $e->getMessage(), "\n"; 
            }
            
            flush();
        }
    }
    catch( Exception $e )
    {
        echo "Caught exception: ",  $e->getMessage(), "\n";
    }
    print "\n\n";
    print "did $i items\n";
    print "done done\n"
    
?>