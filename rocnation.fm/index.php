<?php

    $dbhost		=	"localhost";
    $dbusername	=	"rnfm_user";
    $dbpassword	=	"rnfm_password";
    $dbname		=	"rocnationfm";
    
    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");
    
    $sql = "SELECT * FROM genres ORDER BY `order` ASC";
    $q = mysql_query($sql);
    $genre_list = array();
    while( $row = mysql_fetch_array($q) )
        $genre_list[] = $row;
    
    $genre_list_json = json_encode($genre_list);

    if( isset($_GET['genre']) )
    {
        include_once 'player.html';
    }
    else
    {
    
        include_once 'splash.html';
    }

?>

