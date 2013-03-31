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
    
    function make_s3_bucket($s3_client,$url,&$extra)
    {
        if( isset($extra['aws']['s3_bucket']))
        {
            $s3_bucket = $extra['aws']['s3_bucket'];
            
            if( $s3_client->doesBucketExist($s3_bucket) )
            {
                $web_config = $s3_client->getBucketWebsite(array('Bucket' => $s3_bucket));
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
            $extra['aws']['s3_bucket'] = $s3_bucket;
            
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
    function make_cloudfront_distro($cf_client,$artist,&$extra)
    {
        if( isset($extra['aws']['cloudfront']['id']))
        {
            try
            {
                $cf_id = $extra['aws']['cloudfront']['id'];
                
                $cf_data = $cf_client->getDistribution(array('Id' => $cf_id));
                
                print "  Got Cloud Front Distro: $cf_id\n";
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
            $custom_domain = str_replace("www.","",$custom_domain);
            $aliases[] = $custom_domain;
            $aliases[] = "www.$custom_domain";
        }
        
        $s3_domain_name = "$s3_bucket.s3.amazonaws.com";
        
        print "  s3_domain_name: $s3_domain_name\n";
        
        $origin_id = "S3-$s3_domain_name";
        $origin = array(
                        'Id' => $origin_id,
                        'DomainName' => $s3_domain_name,
                        'S3OriginConfig' => array('OriginAccessIdentity' => ''),
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
                      'Aliases' => array('Quantity' => count($aliases),'Items' => $aliases),
                      'DefaultRootObject' => 'index.html',
                      'Origins' => array('Quantity' => count($origins),'Items' => $origins),
                      'DefaultCacheBehavior' => $default_cache_behavior,
                      'CacheBehaviors' => array('Quantity' => 0),
                      'Comment' => "artist_id: $artist_id",
                      'Logging' => $logging,
                      'Enabled' => TRUE,
                      );
        
        print "  cf args: ";
        var_dump($args);
        print "\n";
        
        $ret = $cf_client->createDistribution($args);

        $extra['aws']['cloudfront']['id'] = $ret['Id'];
        $extra['aws']['cloudfront']['domain_name'] = $ret['DomainName'];
        return TRUE;
    }

    function publish_artist_pages($s3_client,$artist,$extra)
    {
        $artist_url = $artist['url'];
    
        $url = "http://" . staging_host() .  "/player.php?url=$artist_url";
        $body = file_get_contents($url);
        
        $s3_bucket = $extra['aws']['s3_bucket'];
        $key = '/index.html';
        
        write_s3_html_file($s3_client,$s3_bucket,$key,$body);
    }
    
    function write_s3_html_file($s3_client,$s3_bucket,$key,$body)
    {
        $headers = array(
                         'X-UA-Compatible' => 'chrome=1',
                         );
    
        $args = array(
                      'Bucket' => $s3_bucket,
                      'Key' => $key,
                      'Body' => $body,
                      'ACL' => 'public-read',
                      'CacheControl' => 'public, max-age=300',
                      'ContentType' => 'text/html',
                      'Metadata' => $headers,
                      );
        $s3_client->putObject($args);
    }
    
    function get_r53_zone_id($r53_client,$domain)
    {
        $marker = FALSE;
    
        while( TRUE )
        {
            $args = array();
            
            if( $marker )
            {
                $args['Marker'] = $marker;
            }
     
            $ret = $r53_client->listHostedZones($args);
            
            foreach( $ret['HostedZones'] as $zone )
            {
                if( $zone['Name'] == $domain )
                    return $zone['Id'];
            }
            
            if( $ret['IsTruncated'] )
            {
                $marker = $ret['NextMarker'];
            }
            else
            {
                break;
            }
        }
        return FALSE;
    }
    function get_r53_record_set($r53_client,$zone_id,$domain,$host)
    {
        $args = array(
                      'HostedZoneId' => $zone_id,
                      'StartRecordName' => $host,
                      'MaxItems' => 100,
                      );
        
        
        $ret = $r53_client->listResourceRecordSets($args);
        
        foreach( $ret['ResourceRecordSets'] as $record )
        {
            print "        name: " . $record['Name'] . "\n";
        
            if( $record['Name'] == $host )
            {
                return $record;
            }
        }

        return FALSE;
    }
    
    function update_route53_record($r53_client,$domain,$host,$record_type,$record_value)
    {
        print "  Update R53 record: $domain $host $record_type $record_value\n";
    
        $zone_id = get_r53_zone_id($r53_client,$domain);
        
        if( !$zone_id )
        {
            print "  zone ($domain) not found!\n";
            return TRUE;
        }
        
        $rrs = get_r53_record_set($r53_client,$zone_id,$domain,$host);
        
        $changes = array();
        if( $rrs )
        {
            if( $rrs['Type'] == $record_type
               && $rrs['ResourceRecords'][0]['Value'] == $record_value
               )
            {
                print "    record already exists!\ns";
                return TRUE;
            }
            
            $change = array(
                            'Action' => 'DELETE',
                            'ResourceRecordSet' => $rrs,
                            );
            $changes[] = $change;
        }
        
        $rrs = array(
                     'Name' => $host,
                     'Type' => $record_type,
                     'TTL' => 300,
                     'ResourceRecords' => array(array('Value' => $record_value)),
                     );
        
        $change = array(
                        'Action' => 'CREATE',
                        'ResourceRecordSet' => $rrs,
                        );
        
        $changes[] = $change;
        
        $args = array(
                      'HostedZoneId' => $zone_id,
                      'ChangeBatch' => array('Changes' => $changes),
                      );
        $r53_client->changeResourceRecordSets($args);
        print "    record added!\n";
        return TRUE;
    }
    
    function update_route53($r53_client,$artist,$extra)
    {
        $cf_domain_name = $extra['aws']['cloudfront']['domain_name'];
    
        $artist_url = $artist['url'];

        $domain = str_replace("http://","",trueSiteUrl());
        $host = $artist_url;
        
        update_route53_record($r53_client,$domain,$host,'CNAME',$cf_domain_name);
        
        $custom_domain = $artist['custom_domain'];
        
        if( $custom_domain )
        {
            $custom_domain = str_replace("www.","",$custom_domain);
            
            update_route53_record($r53_client,$custom_domain,'www','CNAME',$cf_domain_name);
            update_route53_record($r53_client,$custom_domain,'','A',root_redirect_ip());
        }
        
    }

    $i = 0;
    try
    {
        $s3_client = get_s3_client();
        $cf_client = get_cf_client();
        $r53_client = get_r53_client();
        
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
                    
                    publish_artist_pages($s3_client,$artist,$extra);
                    
                    update_route53($r53_client,$artist,$extra);
                    
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
            print "\n\n";
            
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