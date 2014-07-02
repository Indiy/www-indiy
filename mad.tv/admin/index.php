<?php

//echo "<html><body><pre>\n";

$sql = "SELECT * FROM videos ORDER BY `order` ASC, `id` ASC";
$q = mq($sql);
$video_list = array();
while( $row = mf($q) )
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

$video_list_json = json_encode($video_list);


?>

<html>
<body>
    <h1>Video List</h1>
    
    <?
        
        for( $video_list as $k => $video )
        {
            echo "Name: " . $video['name'] . "<br/>\n";
            echo "Artist: " . $video['name'] . "<br/>\n";
            echo "Logo: " . $video['name'] . "<br/>\n";
            echo "Name: " . $video['name'] . "<br/>\n";
            echo "Name: " . $video['name'] . "<br/>\n";
        }
    
    ?>
</body>
</html>