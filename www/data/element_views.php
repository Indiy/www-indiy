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
        mq("UPDATE $table SET views = views + 1 WHERE id='$id'");
        $photo = mf(mq("SELECT views FROM $table WHERE id='$id'"));
        if( $photo )
            $views = $photo['views'];
        
        return $views;
    }
    
    if( isset($_REQUEST['type']) && isset($_REQUEST['id']) )
    {
        $type = $_REQUEST['type'];
        $id = $_REQUEST['id'];
        if( $type == 'media' )
        {
            $views = 0;
            mq("UPDATE playlist_items SET views = views + 1 WHERE playlist_item_id = '$id'");
            $item = mf(mq("SELECT views FROM playlist_items WHERE playlist_item_id = '$id'"));
            if( $item )
                $views = $item['views'];
            
        }
        else if( $type == 'tab' )
        {
            $views = update_table('mydna_musicplayer_content',$id);
        }
    }
    if( isset($_REQUEST['song_id']) )
    {
        $views = update_table('mydna_musicplayer_audio',$_REQUEST['song_id']);
    }
    else if( isset($_REQUEST['video_id']) )
    {
        $views = update_table('mydna_musicplayer_video',$_REQUEST['video_id']);
    }
    else if( isset($_REQUEST['photo_id']) )
    {
        $views = update_table('photos',$_REQUEST['photo_id']);
    }
    else if( isset($_REQUEST['tab_id']) )
    {
        $views = update_table('mydna_musicplayer_content',$_REQUEST['tab_id']);
    }
    
    $total = artist_get_total_views($artist_id);
    
    $output = array("total_views" => $total,
                    "element_views" => $views);

    $json = json_encode($output);
    if( isset($_REQUEST['callback']) )
    {
        $callback = $_REQUEST['callback'];
        echo "$callback($json);";
    }
    else
    {
        echo $json;
    }

?>