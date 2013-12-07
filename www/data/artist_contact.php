<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    require_once('../includes/functions.php');
    require_once('../includes/config.php');
    
    $name = $_REQUEST["name"];
    $email = $_REQUEST["email"];
    $comments = $_REQUEST["comments"];

    $artist_id = $_REQUEST["artist_id"];
    
    $artist = mf( mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'") );
    $to = $artist['email'];

    $subject = "Contact Form Submission";
    $message = "NAME: $name\n\nEMAIL: $email\n\nMESSAGE: $comments\n\n";
    $from = "no-reply@myartistdna.com";
    $headers = "From:" . $from;

    mail($to,$subject,$message,$headers);
    
    echo "{ success: 1 }\n";
    
?>