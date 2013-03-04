<?php

    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");
    
    session_start();
    session_write_close();
    if( !isset($_SESSION['fan_id']) )
    {
        $output = array(
                        "logged_in" => FALSE,
                        "error" => "not logged in",
                        );
        send_output($output);
        die();
    }
    $fan_id = $_SESSION['fan_id'];
    
    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);
    
    if( isset($_REQUEST['method']) )
    {
        $method = strtoupper($_REQUEST['method']);
    }
    else
    {
        $method = $_SERVER['REQUEST_METHOD'];
    }
    
    if( $method == 'GET' )
    {
        $love_list =  get_love_list();
        $output = array("love_list" => $love_list,
                        "success" => 1,
                        );
        send_output($output);
        die();
    }
    elseif( $method == 'POST' )
    {
        if( isset($data['love_list']) )
        {
            update_love_list($data['love_list']);
            $love_list =  get_love_list();
            $output = array(
                            "love_list" => $love_list,
                            "success" => 1,
                            );
            send_output($output);
            die();
        }
        else
        {
            $output = array(
                            "logged_in" => 1,
                            "error" => "no love list",
                            "detail" => $data,
                            );
            send_output($output);
            die();
        }
    }
    elseif( $method == 'DELETE' )
    {
        $output = array(
                        "logged_in" => 1,
                        "error" => "not implemented",
                        );
        send_output($output);
        die();
    }
    else {
        $output = array("error" => "unknown method");
        send_output($output);
        die();
    }

    function get_love_list()
    {
        global $fan_id;
        
        $love_list = array();
        $love_q = mq("SELECT * FROM fan_loves WHERE fan_id = '$fan_id'");
        while( $item = mf($love_q) )
        {
            $love_list[] = $item;
        }
        return $love_list;
    }
    
    function update_love_list($love_list)
    {
        global $fan_id;
        
        foreach( $love_list as $item )
        {
            $music_id = $item['music_id'];
            $photo_id = $item['photo_id'];
            $video_id = $item['video_id'];
            
            $values = array(
                            "fan_id" => $fan_id,
                            );
        
            if( $music_id )
            {
                $values['music_id'] = $music_id;
                $music = mf(mq("SELECT artistid FROM mydna_musicplayer_audio WHERE id='$music_id'"));
                $values['artist_id'] = $music['artistid'];
            }
            if( $photo_id )
            {
                $values['photo_id'] = $photo_id;
                $music = mf(mq("SELECT artist_id FROM photos WHERE id='$photo_id'"));
                $values['artist_id'] = $music['artist_id'];
            }
            if( $video_id )
            {
                $values['video_id'] = $video_id;
                $music = mf(mq("SELECT artistid FROM mydna_musicplayer_video WHERE id='$video_id'"));
                $values['artist_id'] = $music['artistid'];
            }
            mysql_insert('fan_loves',$values);
        }
    }
    
    function send_output($output)
    {
        $json = json_encode($output);
        if( isset($_REQUEST['callback']) )
        {
            $callback = $_REQUEST['callback'];
            echo "$callback($json);";
        }
        else
        {
            echo $json;
        }
    }
    

?>