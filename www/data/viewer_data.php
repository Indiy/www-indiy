<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once('../includes/functions.php');   
    require_once('../includes/config.php');

    $email = $_REQUEST['email'];
    $artist_id = $_REQUEST['artist_id'];

    if( $email && $artist_id )
    {
        setcookie('PAGE_VIEWER_EMAIL',$email,time() + 365*24*60*60,'/');
    
        $values = array('artistid' => $artist_id,
                        'email' => $email,
                        );
        mysql_insert("mydna_musicplayer_subscribers",$values);

        echo "{ \"success\": 1 }\n";
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        echo "{ \"failure\": 1 }\n";
    }

?>