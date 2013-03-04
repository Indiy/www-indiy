<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    
    session_start();
    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        exit();
    }
    session_write_close();
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
        do_POST();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
    
    function do_POST()
    {
        $friends = $_REQUEST['friends'];
        
        $postedValues = array();
        
        $sql = "SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'";
        $artist = mf(mq($sql));
        
        $artist_name = $artist['artist'];
        
        $to = $friends;
        $message = $data['message'];
        $subject = 'Someone has invited you to MyArtistDNA';
        
        $message = <<<END
$artist_name has invited you to MyArtistDNA.

Be Heard. Be Seen. Be Independent.

END;
        $from = "no-reply@myartistdna.com";
        $headers = "From:" . $from;
        
        mail($to,$subject,$message,$headers);
        
        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
    }
    
?>