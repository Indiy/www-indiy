<?php

    $FILE = 

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    $dbhost		=	"localhost";
    $dbusername	=	"madtv_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"madtv_mysql";
    
    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");
    
    $sql = "SELECT DISTINCT genre FROM videos";
    $q = mysql_query($sql);
    $genre_list = array();
    while( $row = mysql_fetch_array($q) )
        $genre_list[] = $row['genre'];

    
    $genre = $_GET['genre'];
    if( !$genre )
        $genre = 'rock';

    $file = "/tmp/madtv_history_data_$genre.json";
    $json = file_get_contents($file);

    $ret = array();
    $ret['history'] = json_decode($json);
    $ret['genre_list'] = $genre_list;
    
    print json_encode($ret);

?>

