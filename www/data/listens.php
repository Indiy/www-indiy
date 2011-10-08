<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

	include('../includes/functions.php');	
	include('../includes/config.php');
	
    $image = $_GET['image'];

    $music = mf(mq("select * from `[p]musicplayer_audio` where `image`='{$image}' limit 1"));
    $artistid = $music["artistid"];
    $listens = $music["views"] + 1;
    update("[p]musicplayer_audio","views","{$listens}","id",$music["id"]);


    $total = mf(mq("SELECT SUM(views) FROM `[p]musicplayer_audio` WHERE `artistid`='{$artistid}'"));
    
    $output = array("total_listens" => $total[0],"track_listens" => $listens);
    
    print json_encode($output);

?>