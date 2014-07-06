<?php

    require_once "../includes/config.php";
    error_reporting(E_ALL);

    $sql = "SELECT * FROM videos";
    $q = mq($sql);
    $video_list = array();
    while( $row = mf($q) )
    {
        $video_list[] = $row;
    }

    foreach( $video_list as $video )
    {
        $file = $video["video_file"];
        $path_mp4 = "..$file";
        print "file: $file, path: $path_mp4\n";
        
        $path_ogv = str_replace(".mp4",".ogv",$path_mp4);
        if( file_exists($path_ogv) )
        {
            print "Has OGV!\n";
        }
        else
        {
            print "Encoding file...";
            @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $path_ogv $path_mp4");
            print "Done\n";
        }
        print "---------------------\n";
    }




?>