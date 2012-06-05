<?php
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    include('../includes/functions.php');   
    include('../includes/config.php');
    
    session_write_close();
    
    function get_artists()
    {
        $sql = "SELECT id,artist,url,logo FROM mydna_musicplayer";
        $q = mysql_query($sql);
        $ret = array();
        while( $row = mysql_fetch_assoc($q) )
        {
            $ret[] = array("id" => $row['id'],
                           "artist" => $row['artist'],
                           "url" => $row['url'],
                           "logo" => $row['logo'],
                           );
        }
        return $ret;
    }
    
    function get_songs()
    {	
        $sql = "SELECT id,artistid,name,image FROM mydna_musicplayer_audio";
        $q = mysql_query($sql);
        ret = array();
        while( $row = mysql_fetch_assoc($q) )
        {
            $ret[] = array("id" => $row['id'],
                           "artist_id" => $row['artistid'],
                           "name" => $row['name'],
                           "image" => $row['image'],
                           );
        }
        return $ret;
    }
    
    function get_videos()
    {	
        $sql = "SELECT id,artistid,name,image FROM mydna_musicplayer_video";
        $q = mysql_query($sql);
        ret = array();
        while( $row = mysql_fetch_assoc($q) )
        {
            $ret[] = array("id" => $row['id'],
                           "artist_id" => $row['artistid'],
                           "name" => $row['name'],
                           "image" => $row['image'],
                           );
        }
        return $ret;
    }

    function get_photos()
    {	
        $sql = "SELECT id,artist_id,name,image FROM photos";
        $q = mysql_query($sql);
        ret = array();
        while( $row = mysql_fetch_assoc($q) )
        {
            $ret[] = array("id" => $row['id'],
                           "artist_id" => $row['artist_id'],
                           "name" => $row['name'],
                           "image" => $row['image'],
                           );
        }
        return $ret;
    }
    
    
    $artists = get_artists();
    $songs = get_songs();
    $videos = get_videos();
    $photos = get_photos();
    
    $ret = array("artists" => $artists,
                 "songs" => $songs,
                 "videos" => $videos,
                 "photos" => $photos,
                 );
                 
    echo json_encode($ret);
    
?>

