<?php

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
    
    $sql = "SELECT * FROM genres ORDER BY `order` ASC";
    $q = mysql_query($sql);
    $genre_list = array();
    while( $row = mysql_fetch_array($q) )
        $genre_list[] = $row['genre'];


    $ret = array();
    $ret['history'] = array();
    $ret['genre_list'] = $genre_list;

    foreach( $genre_list as $genre )
    {
        $file = "/tmp/madtv_history_data_$genre.json";
        $json = file_get_contents($file);
        $ret['history'][$genre] = json_decode($json);
    }
    
    print json_encode($ret);

?>

