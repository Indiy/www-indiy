<?php

    error_reporting(0);
    $dbhost		=	"localhost";
    $dbusername	=	"rntv_user";
    $dbpassword	=	"rntv_password";
    $dbname		=	"rocnationtv";

    //echo "<html><body><pre>\n";

    /*
     // MADDEV.COM
     error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
     $dbhost		=	"localhost";
     $dbusername	=	"maddvcom_user";
     $dbpassword	=	"MyartistDNA!";
     $dbname		=	"maddvcom_mysql";
     */

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
        $item = array("artist" => $row['artist'],
                      "name" => $row['name'],
                      "logo" => "/media/$logo",
                      "poster" => "/media/$poster",
                      "video_file" => "/media/$video_file",
                      );
        $video_list[] = $item;
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
            @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o \"$path_ogv\" \"$path_mp4\"");
            print "Done\n";
        }
        print "---------------------\n";
    }




?>