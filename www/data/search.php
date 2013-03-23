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
    
        $sql = "SELECT mydna_musicplayer.id, mydna_musicplayer.artist, mydna_musicplayer.url,";
        $sql .= " mydna_musicplayer.logo, artist_files.extra_json AS logo_extra_json";
        $sql .= " FROM mydna_musicplayer";
        $sql .= " JOIN artist_files ON mydna_musicplayer.logo = artist_files.filename";
        $sql .= " WHERE preview_key =  ''";
        
        $q = mysql_query($sql);
        $ret = array();
        while( $row = mysql_fetch_assoc($q) )
        {
            $artist_id = $row['id'];
            
            $image = $row['logo'];
            $image_extra = json_decode($row['logo_extra_json'],TRUE);
            
            if( !$image )
            {
                $image = "/images/search_default.jpg";
            }
            else
            {
                $image = get_image_thumbnail($image,$image_extra,65,44);
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
                $image_extra = json_decode($row['image_extra_json'],TRUE);
                if( !$image )
                {
                    $image = "/images/search_default.jpg";
                }
                else
                {
                    $image = get_image_thumbnail($image,$image_extra,65,44);
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
        $sql = "SELECT mydna_musicplayer_audio.id,mydna_musicplayer_audio.artistid AS artist_id,";
        $sql .= " mydna_musicplayer_audio.name,mydna_musicplayer_audio.image,";
        $sql .= " artist_files.extra_json AS image_extra_json";
        $sql .= " FROM mydna_musicplayer_audio";
        $sql .= " JOIN artist_files ON mydna_musicplayer_audio.image = artist_files.filename";
        $sql .= " JOIN mydna_musicplayer ON mydna_musicplayer_audio.artistid = mydna_musicplayer.id";
        $sql .= " WHERE mydna_musicplayer.preview_key = ''";
        
        return get_media($sql);
    }
    
    function get_videos()
    {
        $sql = "SELECT mydna_musicplayer_video.id,mydna_musicplayer_video.artistid AS artist_id,";
        $sql .= " mydna_musicplayer_video.name,mydna_musicplayer_video.image,";
        $sql .= " artist_files.extra_json AS image_extra_json";
        $sql .= " FROM mydna_musicplayer_video";
        $sql .= " JOIN artist_files ON mydna_musicplayer_video.image = artist_files.filename";
        $sql .= " JOIN mydna_musicplayer ON mydna_musicplayer_video.artistid = mydna_musicplayer.id";
        $sql .= " WHERE mydna_musicplayer.preview_key = ''";
        
        return get_media($sql);
    }

    function get_photos()
    {	
        $sql = "SELECT photos.id,photos.artist_id AS artist_id,";
        $sql .= " photos.name,photos.image,";
        $sql .= " artist_files.extra_json AS image_extra_json";
        $sql .= " FROM photos";
        $sql .= " JOIN artist_files ON photos.image = artist_files.filename";
        $sql .= " JOIN mydna_musicplayer ON photos.artist_id = mydna_musicplayer.id";
        $sql .= " WHERE mydna_musicplayer.preview_key = ''";

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

