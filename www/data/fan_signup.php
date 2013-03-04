<?php
    
    define("PATH_TO_ROOT","../");
    
    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    session_start();
    
    $method = $_REQUEST['method'];
    
    if( $method == 'change_password' )
    {
        do_change_password();
    }
    else if( $method == 'signup' )
    {
        do_signup();
    }
    else if( $method == 'set_password' )
    {
        do_set_password();
    }
    
    function do_set_password()
    {
        $fan_id = $_SESSION['fan_id'];
        
        $password = $_REQUEST['password'];
        
        $fan = mf(mq("SELECT * FROM fans WHERE id='$fan_id'"));
        if( $fan )
        {
            if( !$fan['password'] )
            {
                $email = $fan['email'];
            
                $hash_password = md5($email . $password);
                
                $updates = array("password" => $hash_password);
                mysql_update("fans",$updates,'id',$fan_id);
                
                $output = array("success" => 1);
                print json_encode($output);
                die();
            }
            else
            {
                $output = array("error" => "Account already has a password.");
                print json_encode($output);
                die();
            }
        }
        else
        {
            $output = array("error" => "Fan account not found.");
            print json_encode($output);
            die();
        }
    }
    
    function do_change_password()
    {
        $fan_id = $_SESSION['fan_id'];
        $old_pass = $_REQUEST['old_password'];
        $new_pass = $_REQUEST['new_password'];
        
        if( !$fan_id )
        {
            $output = array("error" => 1,"detail" => "Fan not logged in.");
            print json_encode($output);
            die();
        }
        
        $sql = "SELECT * FROM fans WHERE id='$fan_id'";
        $fan = mf(mq($sql));
        if( $fan )
        {
            $email = $fan['email'];
            $hash_password = md5($email . $old_pass);
            if( $fan['password'] == $hash_password )
            {
                $hash_password = md5($email . $new_pass);
            
                $updates = array("password" => $hash_password);
                mysql_update("fans",$updates,'id',$fan_id);
            
                $output = array("success" => 1);
                print json_encode($output);
                die();
            }
            else
            {
                $output = array("error" => 2,"detail" => "Incorrect old password.");
                print json_encode($output);
                die();
            }
        }
        else
        {
            $output = array("error" => 3,"detail" => "Fan account not found.");
            print json_encode($output);
            die();
        }
    }
    
    function do_signup()
    {
        if( isset($_REQUEST['network']) )
        {
            $network = $_REQUEST['network'];
            if( $network == 'twitter' )
            {
                do_twitter_signup();
            }
            else if( $network == 'facebook' )
            {
                do_facebook_signup();
            }
        }
        else
        {
            do_email_signup();
        }
    }
    function do_email_signup()
    {
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        
        if( $email && $password )
        {
            $hash_password = md5($email . $password);
            
            $fan = mf(mq("SELECT * FROM fans WHERE email = '$email' AND password='$hash_password'"));
            if( $fan )
            {
                $url = login_fan_from_row($fan);
                $output = array("success" => 1,"url" => $url);
                print json_encode($output);
                die();
            }
            
            $fan_id = FALSE;
            $fan = mf(mq("SELECT * FROM fans WHERE email = '$email'"));
            if( $fan )
            {
                if( $fan['password'] )
                {
                    $output = array("error" => "Fan account already exists with that email address.");
                    print json_encode($output);
                    die();
                }
                $fan_id = $fan['id'];
                $values = array("password" => $hash_password);
                mysql_update('fans',$values,'id',$fan_id);
                post_fan_signup($fan);
            }
            else
            {
                $values = array("email" => $email,
                                "password" => $hash_password,
                                );
                mysql_insert('fans',$values);
                $fan_id = mysql_insert_id();
                $fan = mf(mq("SELECT * FROM fans WHERE id='$fan_id'"));
                post_fan_signup($fan);
            }

            if( $fan )
            {
                $url = login_fan_from_row($fan);
                $output = array("success" => 1,"url" => $url);
                print json_encode($output);
                die();
            }
            
            $output = array("error" => "Failed to create fan account.");
            print json_encode($output);
            die();
        }
        
        $output = array("error" => "Need a valid email address and password to create an account.");
        print json_encode($output);
        die();
    }
    function do_facebook_signup()
    {
        require_once '../Login_Twitbook/facebook/facebook.php';
        require_once '../Login_Twitbook/config/fbconfig.php';
        
        $args = array('appId' => APP_ID,'secret' => APP_SECRET);
        
        $facebook = new Facebook($args);
        
        $args = array(
                      'scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access',
                      'redirect_uri' => trueSiteUrl() . "/Login_Twitbook/fan_signup_facebook.php",
                      );
        
        $login_url = $facebook->getLoginUrl($args);
        $output = array("error" => FALSE,"url" => $login_url);
        print json_encode($output);
        die();
    }
    function do_twitter_signup()
    {
        require_once '../Login_Twitbook/twitter/twitteroauth.php';
        require_once '../Login_Twitbook/config/twconfig.php';
        
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);
        
        $landing_url = trueSiteUrl() . "/Login_Twitbook/fan_signup_twitter.php";
        $request_token = $twitteroauth->getRequestToken($landing_url);
        
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        
        if( $twitteroauth->http_code == 200 )
        {
            $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
            $output = array("error" => FALSE,"url" => $url);
            print json_encode($output);
            die();
        }
        else
        {
            $output = array("error" => "Failed to talk to Twitter!","url" => NULL);
            print json_encode($output);
            die();
        }
    }

?>
