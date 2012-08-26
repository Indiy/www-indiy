<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';

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

        $path_parts = pathinfo($file);
        $extension = $path_parts['extension'];
        
        $src_file = "$dir/$file";
        $hash = hash_file("md5",$src_file);
        
        $save_filename = "{$artist_id}_$hash.$extension";
        
        $existing = mf(mq("SELECT * FROM artist_files WHERE filename = '$save_filename' AND artist_id = '$artist_id'"));
        if( $extisting )
        {
            print "Existing file: $file, $upload_filename\n";
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
                            
            mysql_insert("artist_files",$values);
            
            print "New File: $file, $save_filename, \n";
        }
    }
    

?>