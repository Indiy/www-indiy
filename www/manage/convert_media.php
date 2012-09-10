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

    function file_error($id,$msg)
    {
        $values = array("error" => $msg);
        mysql_update("artist_files",$values,"id",$id);
    }


    $file_q = mq("SELECT * FROM artist_files WHERE type='AUDIO' AND error IS NULL");
    
    while( $file = mf($file_q) )
    {
        $id = $file['id'];
        $filename = $file['filename'];
    
        $path_parts = pathinfo($filename);
        $extension = strtolower($path_parts['extension']);
        
        if( $extension != 'mp3' )
        {
            $src_file = "../artists/files/$filename";
            $mp3_file = str_replace(".$extension",".mp3",$src_file);
            @system("/usr/local/bin/ffmpeg -i $src_file -acodec libmp3lame $mp3_file",$retval);
            if( $retval == 0 )
            {
                print "updated file to mp3: $id, file: $filename\n";
                $filename = $mp3_file;
                $values = array("filename" => $mp3_file);
                mysql_update("artist_files",$values,"id",$id);
            }
            else
            {
                file_error($id,"Please upload audio files in mp3 format.");
            }
        }
    }
    
    print "\ndone done\n\n";

?>