<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    $dbhost		=	"localhost";
    $dbusername	=	"fm_app_user";
    $dbpassword	=	"fm_app_password";
    $dbname		=	"fm_app";

    $fm_app_site = "leemazin";

    $FILE = "/tmp/fm_app_genre_data_$fm_app_site.json";

    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");

    $sql = "SELECT * FROM genres WHERE site=\"$fm_app_site\" ORDER BY `order` ASC";
    $q = mysql_query($sql);
    $genre_list = array();
    while( $row = mysql_fetch_array($q) )
        $genre_list[] = $row;

    $json = file_get_contents($FILE);
    
    $ret = array();
    $ret['genre_data'] = json_decode($json);
    $ret['genre_list'] = $genre_list;
    print json_encode($ret);

?>

