<?php
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    include('../includes/functions.php');   
    include('../includes/config.php');
    
    session_write_close();
    
    $allowed_artists = array();
    
    function get_artists()
    {
        global $allowed_artists;
    
        $sql = "SELECT id,artist,url,logo FROM mydna_musicplayer WHERE preview_key = ''";
        $q = mysql_query($sql);
        $ret = array();
        while( $row = mysql_fetch_assoc($q) )
        {
            $artist_id = $row['id'];
            
            $image = $row['logo'];
            
            if( !$image )
            {
                $image = "/images/NoPhoto.jpg";
            }
            else
            {
                $image = "/timthumb.php?src=/artists/files/$image&w=65&h=44&zc=0&q=100";
            }
        
            $ret[] = array("id" => $row['id'],
                           "artist" => $row['artist'],
                           "url" => $row['url'],
                           "logo" => $image,
                           );
            $allowed_artists[$artist_id] = TRUE;
        }
        return $ret;
    }

    function get_media($sql)
    {
        global $allowed_artists;
    
        $q = mysql_query($sql);
        $ret = array();
        while( $row = mysql_fetch_assoc($q) )
        {
            $artist_id = $row['artist_id'];
            if( isset($allowed_artists[$artist_id]) )
            {
                $image = $row['image'];
                if( !$image )
                {
                    $image = "/images/NoPhoto.jpg";
                }
                else
                {
                    $image = "/timthumb.php?src=/artists/files/$image&w=65&h=44&zc=0&q=100";
                }
            
                $ret[] = array("id" => $row['id'],
                               "artist_id" => $row['artist_id'],
                               "name" => $row['name'],
                               "image" => $image,
                               );
            }
        }
        return $ret;
    }
    
    function get_songs()
    {	
        $sql = "SELECT id,artistid AS artist_id,name,image FROM mydna_musicplayer_audio";
        return get_media($sql);
    }
    
    function get_videos()
    {	
        $sql = "SELECT id,artistid AS artist_id,name,image FROM mydna_musicplayer_video";
        return get_media($sql);
    }

    function get_photos()
    {	
        $sql = "SELECT id,artist_id,name,image FROM photos";
        return get_media($sql);
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

