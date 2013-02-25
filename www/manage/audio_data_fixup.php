<?php
    
    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli')
	{
		header("Location: index.php");
		exit();
	}
    session_write_close();
    set_time_limit(60*60);
    
    echo "<html><body><pre>\n";
    
    print "audio image data\n";
    
    $sql = "SELECT * FROM mydna_musicplayer_audio WHERE extra_json IS NULL OR extra_json LIKE \"\"";
    $image_q = mq($sql);

    $total = 0;
    $good = 0;

    while( $row = mf($image_q) )
    {
        $total++;
        $id = $row['id'];
        $audio = $row['audio'];
        $audio_path = "../artists/files/$audio";
        $length = get_audio_length($audio_path);
        
        if( $length > 0 )
        {
            $good++;

            $extra = array("audio_length" => $length);
            $extra_json = json_encode($extra);
            $values = array("extra_json" => $extra_json);

            mysql_update("mydna_musicplayer_audio",$values,"id",$id);
        }
        else
        {
            print "failed to get len for: $audio_path\n";
        }
    }
    
    print "total: $total, good: $good\n";

    print "\n\n============================================\n\n";

    print "done done\n\n";
    
?>