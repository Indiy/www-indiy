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
        break;
    }
    
    print "total: $total, good: $good\n";

    print "\n\n============================================\n\n";

    print "done done\n\n";
    
    
    function get_audio_length($file)
    {
        $cmd = "/usr/bin/ffprobe $file";
        $lines = array();
        exec($cmd,&$lines);
        
        $output = implode("\n",$lines);
        
        print "cmd: "; var_dump($cmd); print "\n";
        print "output: "; var_dump($output); print "\n";
        
        $matches = array();
        $ret = preg_match("/Duration: ([^::]*):([^:]*):([^,]*),/",$output,&$matches);

        print "ret: "; var_dump($ret); print "\n";
        print "matches: "; var_dump($matches); print "\n";
        
        if( $ret === 1 )
        {
            if( count($matches) > 3 )
            {
                $hours = floatval($matches[1]);
                $minutes = floatval($matches[2]);
                $seconds = floatval($matches[3]);
                
                $seconds += $hours * 60 * 60 + $minutes * 60;
                
                return $seconds;
            }
        }
        return 0;
    }
    
?>

