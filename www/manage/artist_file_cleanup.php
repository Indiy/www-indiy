<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';

    error_reporting(E_ALL);

	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli' )
	{
		header("Location: /index.php");
		exit();
	}
    
    echo "<html><body><pre>\n";



    $sql = "SELECT id AS artist_id, logo AS file, NULL AS upload_filename FROM mydna_musicplayer";
    $q = mq($sql);
    
    $dir = "../artists/images";
    $dest_dir = "../artists/files";
    
    while( $item = mf($q) )
    {
        $artist_id = $item['artist_id'];
        $file = $item['file'];
        $upload_filename = $item['upload_filename'];
        
        if( !$file )
        {
            print "No file: file: $file, artist_id: $artist_id, upload_filename: $upload_filename\n";
            continue;
        }

        $path_parts = pathinfo($file);
        $extension = $path_parts['extension'];
        
        $src_file = "$dir/$file";
        if( !file_exists($src_file) )
        {
            print "File not found: $src_file, file: $file, artist_id: $artist_id, update_filename: $upload_filename\n";
            continue;
        }
        $hash = hash_file("md5",$src_file);
        
        $save_filename = "{$artist_id}_$hash.$extension";
        
        $existing_sql = "SELECT * FROM artist_files WHERE filename = '$save_filename' AND artist_id = '$artist_id'";
        //print "existing_sql: $existing_sql\n";
        $existing = mf(mq($existing_sql));
        if( $existing )
        {
            print "Existing file: $file, $save_filename, $upload_filename\n";
            if( $upload_filename )
            {
                if( $existing['upload_filename'] )
                {
                    print "Already has upload filename: " . $existing['upload_filename'] . "\n";
                }
                else
                {
                    print "Adding upload filename: $upload_filename\n";
                    
                    $updates = array("upload_filename" => $upload_filename);
                    mysql_update("artist_files",$updates,"id",$existing['id']);
                }
            }
        }
        else
        {
            $dest_file = "$dest_dir/$save_filename";
            
            copy($src_file,$dest_file);
            
            $values = array("artist_id" => $artist_id,
                            "filename" => $save_filename,
                            "upload_filename" => $filename);
                            
            $ret = mysql_insert("artist_files",$values);
            
            print "New File: $file, $save_filename, ret: "; var_dump($ret); print "\n";
        }
    }
    

?>