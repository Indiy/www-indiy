<?php

    //$access_key_id = "AKIAIP2VCXXJMBG4K75Q";
    //$secret_access_key = "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";

    $access_key_id = "AKIAIK73DR7TBUD7OJEQ";
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
        $path = "../artists/files/$filename";
    
        $key = "artists/files/$filename";
    
        print "filename: $filename\n";
        
        $file_handle = fopen($path,'rb');
        
        $client->putObject(array(
                                 'Bucket' => 'static2.madd3v.com',
                                 'Key' => $filename,
                                 'Body' => $file_handle,
                                 //'SourceFile' => realpath($path),
                                 ));
        fclose($file_handle);
        break;
    }
    
    
    //$client->createBucket(array('Bucket' => 'test.madd3v.com'));

    $client->putObject(array(
                             'Bucket' => 'static2.madd3v.com',
                             'Key'    => 'data2.txt',
                             'Body'   => 'Hello2!'
                             ));

    print "ListBuckets: \n";
    $result = $client->listBuckets()->get('Buckets');
    foreach ($result['Buckets'] as $bucket) {
        print "here1\n";
        print "{$bucket['Name']} - {$bucket['CreationDate']}\n";
    }
    
    print "ListObjects: \n";
    $iterator = $client->getIterator('ListObjects', array('Bucket' => 'static2.madd3v.com'));
    foreach ($iterator as $object) {
        print "here2\n";
        echo $object['Key'] . "\n";
    }
    
    print "\n\n";
    print "done done\n"

?>