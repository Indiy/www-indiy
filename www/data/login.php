<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    
    if( $username != '' && $password != '' )
    {
        $fan_url = fan_login($username,$password);
        $artist_url = artist_login($username,$password);
        $admin_url = admin_login($username,$password);
        
        $output = array(
                        "fan_url" => $fan_url,
                        "artist_url" => $artist_url,
                        "admin_url" => $admin_url,
                        );

        $num_logins = 0;
        $url = "";

        if( $fan_url )
        {
            $num_logins++;
            $url = $fan_url;
        }
        if( $artist_url )
        {
            $num_logins++;
            $url = $artist_url;
        }
        if( $admin_url )
        {
            $num_logins++;
            $url = $admin_url;
        }
        
        if( $num_logins == 0 )
        {
            $output['error'] = 1;
        }
        else
        {
            $output['success'] = 1;
            if( num_logins > 1 )
            {
                $output['url'] = trueSiteUrl() . "/role_choice.php";
            }
            else
            {
                $output['url'] = $url;
            }
        }
        send_output($output);
    }
    else
    {
        $output = array(
                        "error" => 1,
                        "detail" => "need username and password",
                        );
        send_output($output);
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