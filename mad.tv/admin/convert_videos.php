<?php

    require_once "../includes/config.php";
    error_reporting(E_ALL);

    $sql = "SELECT * FROM video";
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
            $src_file = $path_mp4;
            $dst_file = $path_ogv;
            @system("/usr/local/bin/ffmpeg -y -i $src_file -f ogg -vcodec libtheora -q:a 8 -q:v 8 -acodec libvorbis $dst_file");
            print "Done\n";
        }
        print "---------------------\n";
    }




?>