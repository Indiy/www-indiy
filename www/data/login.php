<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';
    
    session_start();
    if( isset($_REQUEST['network']) )
    {
        $network = $_REQUEST['network'];
        if( $network == 'twitter' )
        {
            do_twitter_login($data);
        }
        else if( $network == 'facebook' )
        {
            do_facebook_login($data);
        }
        else
        {
            $output = array("error" => "Unknown network.");
            send_output($output);
        }
    }
    else
    {
        do_regular_login();
    }
    
    function do_facebook_login()
    {
        require_once '../Login_Twitbook/facebook/facebook.php';
        require_once '../Login_Twitbook/config/fbconfig.php';
        
        $args = array('appId' => APP_ID,'secret' => APP_SECRET);
        
        $facebook = new Facebook($args);
        
        $args = array(
                      'scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access',
                      'redirect_uri' => trueSiteUrl() . "/Login_Twitbook/login_facebook.php",
                      );
        
        $login_url = $facebook->getLoginUrl($args);
        $output = array("error" => FALSE,"url" => $login_url);
        send_output($output);
        die();
    }
    
    function do_twitter_login()
    {
        require_once '../Login_Twitbook/twitter/twitteroauth.php';
        require_once '../Login_Twitbook/config/twconfig.php';
        
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);
        
        $landing_url = trueSiteUrl() . "/Login_Twitbook/login_twitter.php";
        $request_token = $twitteroauth->getRequestToken($landing_url);
        
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        if( $twitteroauth->http_code == 200 )
        {
            $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
            $output = array("error" => FALSE,"url" => $url);
            send_output($output);
            die();
        }
        else
        {
            $output = array("error" => "Failed to talk to Twitter!","url" => NULL);
            send_output($output);
            die();
        }
    }
    
    function do_regular_login()
    {
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        
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
                $output['url'] = $url;
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