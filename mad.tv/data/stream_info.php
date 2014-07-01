<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    $ret = array();
    $ret['history'] = array();
    $ret['genre_list'] = array();
    
    $sql = "SELECT * FROM genre ORDER BY `order` ASC";
    $q = mq($sql);
    while( $row = mf($q) )
    {
        $name = $row['name'];
        $genre_id = $row[]
        
        $ret['genre_list'][] = $row;

        $file = "/tmp/madtv_history_data_$genre_id.json";
        $json = file_get_contents($file);
        $ret['history'][$genre_id] = json_decode($json);
    }
    
    print json_encode($ret);

?>