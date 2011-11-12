<?php

    /*
    
    //Production 
    error_reporting(0);
    $dbhost		=	"localhost";
    $dbusername	=	"madcom_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"madcom_mysql";
     
    */

    //echo "<html><body><pre>\n";
    
    // MADDEV.COM
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
    $dbhost		=	"localhost";
    $dbusername	=	"maddvcom_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"maddvcom_mysql";

    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");


    $q = mysql_query("SELECT * FROM mydna_musicplayer_config WHERE 1 LIMIT 1");
    $row = mysql_fetch_array($q);
    $true_site_url = $row['url'];

    $url = $_REQUEST['url'];
    //echo "url=$url\n";

    $abbrev = $url;
    //echo "abbrev=$abbrev\n";

    $sql = "SELECT mydna_musicplayer_audio.id AS song_id, mydna_musicplayer.url AS artist_url FROM mydna_musicplayer_audio "
        . "JOIN mydna_musicplayer ON mydna_musicplayer_audio.artistid = mydna_musicplayer.id "
        . "WHERE mydna_musicplayer_audio.abbrev = '$abbrev'";
    $q = mysql_query($sql);
    $row = mysql_fetch_array($q);
    $artist_url = $row['artist_url'];
    $song_id = $row['song_id'];
    
    $redirect_url = str_replace('www.',$artist_url . '.',$true_site_url);
    $redirect_url .= "/?song_id=" . $song_id;
    
    //echo "redirect_url = '$redirect_url'\n";

    header("Location: $redirect_url");

    echo "<html>\n";
    echo "<head>\n";
    echo "<title>...</title>\n";
    echo "<meta http-equiv=\"refresh\" content=\"1; url=$redirect_url\">\n";
    echo "<meta http-equiv=\"pragma\" content=\"no-cache\">\n";
    echo "<meta http-equiv=\"expires\" content=\"-1\">\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<a href=\"$redirect_url\">Click here to continue.</a>\n";
    echo "</body>\n";
    echo "</html>\n";
    
?>

