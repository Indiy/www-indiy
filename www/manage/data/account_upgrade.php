<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        exit();
    }
    
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
        $artist_id = $_REQUEST['artist_id'];
        $email = $_REQUEST['email'];
        
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id' LIMIT 1"));
        $artist_name = $artist['artist'];
        $artist_url = $artist['url'];
    
        $to = "info@myartistdna.com";
        $subject = "Upgrade for $artist_name";
        $message = <<<END
        
Account upgrade request:

----------------------------------

Artist: $artist_name
URL: $artist_url
Email: $email

----------------------------------

Be Heard. Be Seen. Be Independent.
        
END;

        $from = "no-reply@myartistdna.com";
        $headers = "From:" . $from;
        
        mail($to,$subject,$message,$headers);
        
        echo "{ \"success\": 1 }\n";
    
    }
    
?>