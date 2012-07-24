<?php
    require_once '../includes/config.php';
    require_once '../includes/functions.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    if( !isset($_SESSION['fan_id']) )
    {
        $ret = array("logged_in" => FALSE,
                     "error" => "not logged in"
                     );
        print json_encode($ret);
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
        $ret = array("love_list" => $love_list,
                     "success" => TRUE,
                     );
        print json_encode($ret);
        die();
    }
    elseif( $method == 'POST' )
    {
        if( isset($data['love_list']) )
        {
            update_love_list($data['love_list']);
            $love_list =  get_love_list();
            $ret = array("love_list" => $love_list,
                         "success" => TRUE,
                         );
            print json_encode($ret);
            die();
        }
        else
        {
            $ret = array("logged_in" => TRUE,
                         "error" => "no love list"
                         );
            print json_encode($ret);
            die();
        }
    }
    elseif( $method == 'DELETE' )
    {
        $ret = array("logged_in" => TRUE,
                     "error" => "not implemented"
                     );
        print json_encode($ret);
        die();
    }

    function get_love_list()
    {
        global $fan_id;
        
        $love_list = [];
        $love_q = mf("SELECT * FROM fan_loves WHERE fan_id = '$fan_id'");
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
        
            $values = array("fan_id" => $fan_id,
                            "music_id" => $music_id,
                            "photo_id" => $photo_id,
                            "video_id" => $video_id,
                            );
                            
            mysql_insert('fan_loves',$values);
        }
    }
    
    $output = array("error" => $error,"url" => $url);
    print json_encode($output);

?>