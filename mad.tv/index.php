<?php

    require_once "includes/config.php";

    if( isset($_GET['genre_id']) )
    {
        include_once 'player.html';
    }
    else
    {
        $sql = "SELECT * FROM genre ORDER BY `order` ASC";
        $q = mq($sql);
        $genre_list = array();
        while( $row = mf($q) )
        {
            $genre_list[] = $row;
        }
        $genre_list_json = json_encode($genre_list);
    
        include_once 'splash.html';
    }
?>

