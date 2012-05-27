<?php
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('../includes/functions.php');   
    include('../includes/config.php');
    
    $artist_id = 0;
    $views = 0;
    
    if( isset($_GET['song_id']) )
    {
        $song_id = $_GET['song_id'];

        $music = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id='$song_id'"));
        $artist_id = $music["artistid"];
        $views = intval($music["views"]) + 1;
        update("mydna_musicplayer_audio",array("views" => $views),"id",$music['id']);

    }
    else if( isset($_GET['video_id') )
    {
        $video_id = $_GET['video_id'];

        $video = mf(mq("SELECT * FROM mydna_musicplayer_video WHERE id='$video_id'"));
        $artist_id = $video["artistid"];
        $views = intval($video["views"]) + 1;
        update("mydna_musicplayer_video",array("views" => $views),"id",$video['id']);
    }
    
   $music_sum = mf(mq("SELECT SUM(views) FROM mydna_musicplayer_audio WHERE `artistid`='$artist_id'"));
   $video_sum = mf(mq("SELECT SUM(views) FROM mydna_musicplayer_video WHERE `artistid`='$artist_id'"));
   
   $total = intval($music_sum[0]);
   $total += intval($video_sum[0]);
   
   $output = array("total_views" => $total,
                   "element_views" => $views);

   print json_encode($output);

?>