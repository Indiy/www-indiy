<?php

    define("PATH_TO_ROOT","../");
    
    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';
    
    require_once '../Login_Twitbook/twitter/twitteroauth.php';
    require_once '../Login_Twitbook/config/twconfig.php';
    
    //print "<html><body><pre>\n";
    
    if( !empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret']) )
    {
        $oauth_token = $_SESSION['oauth_token'];
        $oauth_token_secret = $_SESSION['oauth_token_secret'];
        $oauth_verifier = $_GET['oauth_verifier'];
        
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
        $access_token = $twitteroauth->getAccessToken($oauth_verifier);
        $user_info = $twitteroauth->get('account/verify_credentials');
        
        //print "session: "; var_dump($_SESSION);
        //print "user_info: "; var_dump($user_info);
        
        if( isset($user_info->error) )
        {
            //print "Failed to get user info\n";
            header("Location: /");
            die();
        }
        else
        {
            $uid = $user_info->id;
            
            $fan = mf(mq("SELECT * FROM fans WHERE tw_uid='$uid'"));
            if( $fan )
            {
                //print "already exists\n";
                $url = login_fan_from_row($fan);
                header("Location: $url");
                die();
            }
            
            $screen_name = $user_info->screen_name;
            $oauth_token = $access_token['oauth_token'];
            $oauth_token_secret = $access_token['oauth_token_secret'];

            $extra = array(
                           "twitter_oauth_token" => $oauth_token,
                           "twitter_oauth_token_secret" => $oauth_token_secret
                           );
            
            $extra_json = json_encode($extra);
            
            $values = array("email" => "@$screen_name",
                            "tw_uid" => $uid,
                            "extra_json" => $extra_json,
                            );
            
            //print "values: "; var_dump($values);
            
            mysql_insert('fans',$values);
            
            $fan_id = mysql_insert_id();
            
            $fan = mf(mq("SELECT * FROM fans WHERE id='$fan_id'"));

            post_fan_signup($fan);

            $url = login_fan_from_row($fan);
            header("Location: $url");
            die();
        }
    }
    
    //print "Failed to do twitter login\n";
    header("Location: /");
    die();
    
?>