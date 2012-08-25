<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('../includes/functions.php');   
    include('../includes/config.php');
    
    function update_table($table,$id)
    {
        mq("UPDATE $table SET loves = loves + 1 WHERE id='$id'");
    }
    
    if( isset($_REQUEST['music_id']) )
    {
        update_table('mydna_musicplayer_audio',$_REQUEST['music_id']);
    }
    else if( isset($_REQUEST['video_id']) )
    {
        update_table('mydna_musicplayer_video',$_REQUEST['video_id']);
    }
    else if( isset($_REQUEST['photo_id']) )
    {
        update_table('photos',$_REQUEST['photo_id']);
    }

    $output = array("success" => 1);
    print json_encode($output);

?>