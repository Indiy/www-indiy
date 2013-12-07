<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once('../includes/functions.php');   
    require_once('../includes/config.php');
    
    $song_id = $_GET['song_id'];

    $music = mf(mq("SELECT * FROM `[p]musicplayer_audio` WHERE id='$song_id' limit 1"));
    $artistid = $music["artistid"];
    $listens = $music["views"] + 1;
    update("[p]musicplayer_audio","views","{$listens}","id",$music["id"]);

    $total = mf(mq("SELECT SUM(views) FROM `[p]musicplayer_audio` WHERE `artistid`='{$artistid}'"));

    $output = array("total_listens" => intval($total[0]),"track_listens" => $listens);
    print json_encode($output);
?>
