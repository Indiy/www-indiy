<?php
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('../includes/functions.php');   
    include('../includes/config.php');
    
    $artist_id = $_REQUEST['artist_id'];
    $views = 0;
    
    function update_table($table,$id)
    {
        $views = 0;
        $video = mq("UPDATE $table SET views = views + 1 WHERE id='$id'");
        $photo = mf(mq("SELECT views FROM $table WHERE id='$id'"));
        if( $photo )
            $views = $photo['views'];
        
        return $views;
    }
    
    
    if( isset($_REQUEST['song_id']) )
    {
        $song_id = $_REQUEST['song_id'];

        $music = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id='$song_id'"));
        $artist_id = $music["artistid"];
        $views = intval($music["views"]) + 1;
        mysql_update("mydna_musicplayer_audio",array("views" => $views),"id",$music['id']);

    }
    else if( isset($_REQUEST['video_id']) )
    {
        $views = update_table('mydna_musicplayer_video',$_REQUEST['video_id']);
    }
    else if( isset($_REQUEST['photo_id']) )
    {
        $views = update_table('photos',$_REQUEST['photo_id']);
    }
    
    $total = artist_get_total_views($artist_id);
    
    $output = array("total_views" => $total,
                    "element_views" => $views);

    print json_encode($output);

?>