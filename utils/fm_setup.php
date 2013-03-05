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
        $name = $stream['name'];

        $file_list = array();
        
        $stream_name = "MyArtistDNA.FM $name";
        $stream_mount = "{$stream_prefix}{$stream_id}";
        $playlist = "ices/$stream_mount.playlist.txt";
        $conf_file = "ices/$stream_mount.ices.conf";
        
        foreach( $songs as $song )
        {
            $song_id = $song['id'];
            
            $audio_filename = $song['audio_filename'];
            $scrubber_text = $song['scrubber_text'];
            
            $parts = explode(' - ',$scrubber_text,2);
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
        $playlist_text .= "\n";
        
        file_put_contents($playlist,$playlist_text,LOCK_EX);
        write_conf($conf_file,$stream_mount,$stream_name,$playlist);
    }

    
    function write_conf($file,$stream_mount,$stream_name,$playlist)
    {
        $contents = <<<END
<?xml version="1.0"?>
<ices:Configuration xmlns:ices="http://www.icecast.org/projects/ices">
  <Playlist>
    <File>$playlist</File>
    <Randomize>1</Randomize>
    <Type>builtin</Type>
    <Module>ices</Module>
  </Playlist>

  <Execution>
    <Background>0</Background>
    <Verbose>0</Verbose>
    <BaseDirectory>/tmp</BaseDirectory>
  </Execution>

  <Stream>
    <Server>
      <Hostname>localhost</Hostname>
      <Port>$fm_port</Port>
      <Password>source12345</Password>
      <Protocol>http</Protocol>
    </Server>

    <Mountpoint>/$stream_mount</Mountpoint>
    <Name>$stream_name</Name>
    <Genre>rock</Genre>
    <Description>$stream_name</Description>
    <URL>http://www.myartistdna.fm/</URL>
    <Public>0</Public>
    <Bitrate>128</Bitrate>
    <Reencode>0</Reencode>
    <Channels>2</Channels>
  </Stream>
</ices:Configuration>

END;

        file_put_contents($file,$contents,LOCK_EX);
    }


?>