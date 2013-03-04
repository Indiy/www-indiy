<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';	
	
    session_start();
    session_write_close();

    if( $_SESSION['sess_userId'] == '' )
	{
		header("Location: /index.php");
		exit();
	}
    
    $artist_id = $_REQUEST['artist_id'];
    $filename = 'newsletter.xls';
    
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    echo "name\temail\r\n";
    echo "\r\n";

    $sql = "SELECT * FROM mydna_musicplayer_subscribers WHERE artistid = '$artist_id'";
    $q = mq($sql);
    while( $row = mf($q) )
    {
        $name = $row['name'];
        $email = $row['email'];
        echo "$name\t$email\r\n";
    }
    echo "\r\n";

?>

