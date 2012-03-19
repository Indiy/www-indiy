<?php

    $FILE = "/tmp/madtv_history_data.json";

    //Production 
    error_reporting(0);
    $dbhost		=	"localhost";
    $dbusername	=	"madtv_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"madtv_mysql";
    
    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");
    
    $sql = "SELECT * FROM videos ORDER BY `order` ASC, `id` ASC";
    $q = mysql_query($sql);
    $video_list = array();
    while( $row = mysql_fetch_array($q) )
    {
        $logo = $row['logo_file'];
        $poster = $row['poster_file'];
        $video_file = $row['video_file'];
        $artist = $row['artist'];
        $name = $row['name'];
        
        $duration = get_duration($video_file);
        
        $item = array("artist" => $artist,
                      "name" => $name,
                      "track" => "$artist - $name",
                      "logo" => "/media/$logo",
                      "poster" => "/media/$poster",
                      "video_file" => "/media/$video_file",
                      "duration" => $duration
                      );
        $video_list[] = $item;
    }

    $history = [];
    $json = file_get_contents($FILE);
    if( $json )
    {
        print "loading old data\n";
        $data = json_decode($json,TRUE);
        $history = $data;
    }

    for( $i = count($history) ; $i < 3 ; ++i )
    {
        $file = get_new_file();
        $file["start_time"] = 0;
        print "new history track: " . $file["track"] . "\n";
        
        array_unshift($history,$file);
    }

    while(TRUE)
    {
        $file = get_new_file();
        $file["start_time"] = time();

        print "new track: " . $file["track"] . "\n";
        
        array_unshift($history,$file);
        $history = array_slice($history,0,20);
        
        $json = json_encode($history);
        file_put_contents($FILE,$json,LOCK_EX);

        $duration = $file["duration"];
        print "Sleeping %d seconds\n\n");
        sleep($duration);
    }


    function get_next_file();
    {
        global $history;
        global $video_list;
        
        $history_len = count($history);
        $half_video_list = intval(floor(count($video_list)/2));
        $dup_search_len = min($half_video_list,$history_len);
        
        for( $i = 0 ; $i < 100 ; $i++ )
        {
            $index = rand(0,count($video_list));
            $next = $video_list[$index]; 
            
            $found = FALSE;
            for( $j = 0 ; $j < $dup_search_len ; $j++ )
            {
                $h = $history[$j];
                if( $h["track"] == $next["track"] )
                {
                    $found = TRUE;
                    break;
                }
            }
            if( !$found )
            {
                return $next;
            }
        }
        return $next;
    }

    function get_duration($video_file)
    {
        $output = exec("/usr/bin/ffmpeg -i ../$video_file");
        
        preg_match('/Duration: (.*?),/', $output, $matches);
        $duration = $matches[1];
        $duration_array = split(':', $duration);
        $duration = $duration_array[0] * 3600 + $duration_array[1] * 60 + $duration_array[2];
        return intval(floor($duration));
    }



?>
