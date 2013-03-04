<?php

    require_once "public_html/includes/config.php";
    
    $streams = array();
    
    $sql = "SELECT * FROM fm_streams WHERE artist_id='$artist_id' ORDER BY `order` ASC, id ASC";
    $q = mq($sql);
    while( $s = mf($q) )
    {
        $streams[] = $s;
    }
    
    foreach( $streams as $i => $stream )
    {
        $id = $stream['id'];
        $streams[$i]['songs'] = array();
        $q = mq("SELECT * FROM fm_songs WHERE fm_stream_id='$id'");
        while( $song = mf($q) )
        {
            $streams[$i]['songs'][] = $song;
        }
    }
    
    

?>