<?php

    if( isset($_GET['genre']) )
    {
        include_once 'player.html';
    }
    else
    {
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
            
        $genre_list_json = json_encode($genre_list);
    
        include_once 'splash.html';
    }
?>

