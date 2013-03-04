<?php

    require_once "public_html/includes/config.php";
    require_once "public_html/includes/functions.php";
    
    $ID3v2 = "/usr/local/bin/id3v2";
    
    $streams = array();
    
    $sql = "SELECT * FROM fm_streams ORDER BY `order` ASC, id ASC";
    $q = mq($sql);
    while( $s = mf($q) )
    {
        $streams[] = $s;
    }
    
    foreach( $streams as $i => $stream )
    {
        $id = $stream['id'];
        $streams[$i]['songs'] = array();
        
        $sql = "";
        $sql .= "SELECT fm_songs.*,";
        $sql .= " audio_file.filename AS audio_filename, audio_file.upload_filename AS audio_upload_filename,";
        $sql .= " image_file.filename AS image_filename, image_file.upload_filename AS image_upload_filename";
        $sql .= " FROM fm_songs";
        $sql .= " JOIN artist_files AS audio_file ON fm_songs.audio_file_id = audio_file.id";
        $sql .= " JOIN artist_files AS image_file ON fm_songs.image_file_id = image_file.id";
        $sql .= " WHERE fm_stream_id='$id'";
        
        $q = mq($sql);
        while( $song = mf($q) )
        {
            $streams[$i]['songs'][] = $song;
        }
    }
    

    
    foreach( $streams as $i => $stream )
    {
        $stream_id = $stream['id'];
        $songs = $stream['songs'];

        $file_list = array();
        
        $stream_name = "{$stream_prefix}{$stream_id}";
        $playlist = "ices/$stream_name.playlist.txt";
        
        foreach( $songs as $song )
        {
            $song_id = $song['id'];
            
            $audio_filename = $song['audio_filename'];
            $scrubber_text = $song['scrubber_text'];
            
            $parts = explode(' - ',$scrubber_text,1);
            $artist = "";
            $track = "";
            if( count($parts) > 1 )
            {
                $artist = $parts[0];
                $track = $parts[1];
            }
            else
            {
                $track = $scrubber_text;
            }
            
            $src = "public_html/artists/files/$audio_filename";
            $dst = "ices/mp3/song_{$stream_id}_{$song_id}.mp3";
            
            copy($src,$dst);
            
            $cmd = "$ID3v2 -D $dst";
            system($cmd);
            $cmd = "$ID3v2 -1 -a \"$artist\" -t \"$track\" $dst";
            system($cmd);
            
            $file_list[] = realpath($dst);
        }

        $playlist_text = implode("\n",$file_list);
        
        file_put_contents($playlist,$playlist_text,LOCK_EX);
    }

?>