<?php

    ignore_user_abort(true);
    set_time_limit(0);
    
    $genre = $argv[1];
    if( !$genre )
        $genre = 'rock';

    $FILE = "/tmp/rntv_history_data_$genre.json";

    $root_url = "http://www.rocnation.tv";

    //Production 
    $dbhost		=	"localhost";
    $dbusername	=	"rntv_user";
    $dbpassword	=	"rntv_password";
    $dbname		=	"rocnationtv";
    
    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");
    
    $sql = "SELECT * FROM videos WHERE genre = '$genre'";
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
                      "title" => "$artist - $name",
                      "logo" => "$root_url/media/$logo",
                      "poster" => "$root_url/media/$poster",
                      "video_file" => "$root_url/media/$video_file",
                      "duration" => $duration
                      );
        $video_list[] = $item;
    }
    if( count($video_list) == 0 )
        die("No videos for genre: $genre");

    $history = array();
    $json = file_get_contents($FILE);
    if( $json )
    {
        print "loading old data\n";
        $data = json_decode($json,TRUE);
        $history = $data;
    }

    for( $i = count($history) ; $i < 3 ; ++$i )
    {
        $file = get_next_file();
        $file["start_time"] = 0;
        print "new history track: " . $file["title"] . "\n";
        
        array_unshift($history,$file);
    }

    while(TRUE)
    {
        $file = get_next_file();
        $file["start_time"] = time();

        print "new track: " . $file["title"] . "\n";
        
        array_unshift($history,$file);
        $history = array_slice($history,0,20);
        
        $json = json_encode($history);
        file_put_contents($FILE,$json,LOCK_EX);

        $duration = $file["duration"];
        $sleep_duration = $duration - 5;
        print "Sleeping $sleep_duration seconds\n\n";
        sleep($sleep_duration);
    }


    function get_next_file()
    {
        global $history;
        global $video_list;
        
        $history_len = count($history);
        $half_video_list = intval(floor(count($video_list)/2));
        $dup_search_len = min($half_video_list,$history_len);
        
        for( $i = 0 ; $i < 100 ; ++$i )
        {
            $index = mt_rand(0,count($video_list)-1);
            $next = $video_list[$index]; 
            
            $found = FALSE;
            for( $j = 0 ; $j < $dup_search_len ; ++$j )
            {
                $h = $history[$j];
                if( $h["title"] == $next["title"] )
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
        $output = @shell_exec("/usr/bin/ffmpeg -i ~/public_html/media/'$video_file' 2>&1");
        
        preg_match('/Duration: (.*?),/', $output, $matches);
        $duration = $matches[1];
        $duration_array = split(':', $duration);
        $duration = $duration_array[0] * 3600 + $duration_array[1] * 60 + $duration_array[2];
        $duration = intval(floor($duration));
        print "file: $video_file, duration: $duration\n";
        return $duration;
    }



?>
