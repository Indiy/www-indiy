<?php

    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");
    
    if( !isset($_SESSION['fan_id']) )
    {
        $output = array(
                        "logged_in" => FALSE,
                        "error" => "not logged in",
                        );
        send_output($output);
        die();
    }
    
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
        do_GET();
        die();
    }
    elseif( $method == 'POST' )
    {
        do_POST();
    }
    else
    {
        $output = array("error" => "unknown method");
        send_output($output);
        die();
    }

    function do_GET()
    {
        $fan_id = $_SESSION['fan_id'];

        $fan = mf(mq("SELECT * FROM fans WHERE id='$fan_id'"));
        if( $fan )
        {
            $user_info_json = $fan['user_info_json'];
            $user_info = json_decode($user_info_json,TRUE);
            
            $output = array("success" => 1,
                            "user_info" => $user_info,
                            );
            send_output($output);
        }
        else
        {
            $output = array("error" => "fan not found");
            send_output($output);
        }
    }
    function do_POST()
    {
        $fan_id = $_SESSION['fan_id'];
        
        $fan = mf(mq("SELECT * FROM fans WHERE id='$fan_id'"));
        if( $fan )
        {
            $user_info_json = $fan['user_info_json'];
            $user_info = json_decode($user_info_json,TRUE);
            
            unset($_GET['method']);
            unset($_POST['method']);
            
            foreach( $_GET as $k => $v )
            {
                $user_info[$k] = $v;
            }
            foreach( $_POST as $k => $v )
            {
                $user_info[$k] = $v;
            }
            
            $user_info_json = json_encode($user_info);
            $values = array("user_info_json" => $user_info_json);
            
            mysql_update('fans',$values,'id',$fan_id);
            
            $output = array("success" => 1,
                            "user_info" => $user_info,
                            );
            send_output($output);
        }
        else
        {
            $output = array("error" => "fan not found");
            send_output($output);
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