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

    function do_table($sql,$dir,$table = FALSE,$column = FALSE)
    {
        print "\n\n";
        print "============================================\n";
        print "sql: $sql\n";
        print "\n";
    
        $q = mq($sql);
        
        $dest_dir = "../artists/files";
        
        while( $item = mf($q) )
        {
            $id = $item['id'];
            $artist_id = $item['artist_id'];
            $file = $item['file'];
            $upload_filename = $item['upload_filename'];
            
            if( !$file )
            {
                print "No file: file: $file, artist_id: $artist_id, upload_filename: $upload_filename\n";
                continue;
            }

            $path_parts = pathinfo($file);
            $extension = strtolower($path_parts['extension']);
            
            $src_file = "$dir/$file";
            if( !file_exists($src_file) )
            {
                print "File not found: $src_file, file: $file, artist_id: $artist_id, update_filename: $upload_filename\n";
                continue;
            }
            $hash = hash_file("md5",$src_file);
            $type = get_file_type($src_file);
            
            $save_filename = "{$prefix}{$artist_id}_$hash.$extension";
            
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
                                "upload_filename" => $upload_filename,
                                "type" => $type);
                                
                $ret = mysql_insert("artist_files",$values);
                
                print "New File: $file, $save_filename, type: $type, ret: ";
                var_dump($ret);
            }
            copy_alternative($src_file,"{$prefix}{$artist_id}_$hash","ogg");
            copy_alternative($src_file,"{$prefix}{$artist_id}_$hash","ogv");
            
            if( $table && $column )
            {
                $updates = array($column => $save_filename);
                mysql_update($table,$updates,'id',$id);
            }
        }
    }
    
    function copy_alternative($src_file,$dst_file_prefix,$extension)
    {
        $dest_dir = "../artists/files";
    
        $path_parts = pathinfo($src_file);
        $src_ext = $path_parts['extension'];
        $alt_file = str_replace(".$src_ext",".$extension",$src_file);
        $dst_file = "$dest_dir/$dst_file_prefix.$extension";
        if( file_exists($alt_file) && !file_exists($dst_file) )
        {
            copy($alt_file,$dst_file);
            print "Copied alt file: $alt_file\n";
        }
    }
    
    /*
    $dir = "../artists/images";
    $sql = "SELECT id AS id, id AS artist_id, logo AS file, NULL AS upload_filename FROM mydna_musicplayer";
    do_table($sql,$dir,"mydna_musicplayer","logo");
    
    $dir = "../artists/images";
    $sql = "SELECT id, artistid AS artist_id, image AS file, NULL AS upload_filename FROM mydna_musicplayer_audio";
    do_table($sql,$dir,'mydna_musicplayer_audio','image');

    $dir = "../artists/audio";
    $sql = "SELECT id, artistid AS artist_id, audio AS file, upload_audio_filename AS upload_filename FROM mydna_musicplayer_audio";
    do_table($sql,$dir,'mydna_musicplayer_audio','audio');
    */
    
    
    $dir = "../artists/products";
    $sql = "SELECT id, artistid AS artist_id, image AS file, NULL AS upload_filename FROM mydna_musicplayer_ecommerce_products";
    do_table($sql,$dir,'mydna_musicplayer_ecommerce_products','image');

    /*

    $dir = "../artists/images";
    $sql = "SELECT id, artistid AS artist_id, image AS file, NULL AS upload_filename FROM mydna_musicplayer_video";
    do_table($sql,$dir);

    $dir = "../vid";
    $sql = "SELECT id, artistid AS artist_id, video AS file, upload_video_filename AS upload_filename FROM mydna_musicplayer_video";
    do_table($sql,$dir);
     
    $dir = "../artists/photo";
    $sql = "SELECT id, artist_id AS artist_id, image AS file, NULL AS upload_filename FROM photos";
    do_table($sql,$dir);

    $dir = "../artists/digital_downloads";
    $sql = "";
    $sql .= "SELECT product_files.id AS id, mydna_musicplayer_ecommerce_products.artistid AS artist_id ";
    $sql .= ", product_files.filename AS file ";
    $sql .= ", product_files.upload_filename AS upload_filename ";
    $sql .= "FROM product_files ";
    $sql .= "JOIN mydna_musicplayer_ecommerce_products ON product_files.product_id = mydna_musicplayer_ecommerce_products.id";
    do_table($sql,$dir);
     

    $dir = "../artists/images";
    $sql = "SELECT id, artistid AS artist_id, image AS file, NULL AS upload_filename FROM mydna_musicplayer_content";
    do_table($sql,$dir);
    
    */
    print "done done\n\n";

?>