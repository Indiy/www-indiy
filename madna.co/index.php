<?php

    /*
    
    //Production 
    error_reporting(0);
    $dbhost		=	"localhost";
    $dbusername	=	"madcom_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"madcom_mysql";
     
    */

    echo "<html><body><pre>\n";
    
    // MADDEV.COM
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
    $dbhost		=	"localhost";
    $dbusername	=	"maddvcom_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"maddvcom_mysql";

    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");

    $url = $_REQUEST['url'];
    echo "url=$url\n";

    $abbrev = $url;
    echo "abbrev=$abbrev\n";

    $sql = "SELECT mydna_musicplayer_audio.id AS songid, mydna_musicplayer.url AS artist_url FROM mydna_musicplayer_audio "
        . "JOIN mydna_musicplayer ON mydna_musicplayer_audio.artistid = mydna_musicplayer.id "
        . "WHERE mydna_musicplayer_audio.abbrev = '$abbrev'";
    $q = mysql_query($sql);
    $row = mysql_fetch_array($q);
    
    var_dump($row);

?>

