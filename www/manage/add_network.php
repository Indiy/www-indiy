<?php

    session_start();
    require_once '../includes/config.php';
    require_once '../includes/functions.php';

    $network = $_REQUEST['network'];

    if( $_REQUEST['artist_id'] )
        $_SESSION['attach_artist_id'] = $_REQUEST['artist_id'];

    if( $network == 'twitter' )
    {
        require_once '../Login_Twitbook/twitter/twitteroauth.php';
        require_once '../Login_Twitbook/config/twconfig.php';
        
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);
        
        $landing_url = 'http://' . $_SERVER['HTTP_HOST'] . '/manage/twitter_landing.php';
        $request_token = $twitteroauth->getRequestToken($landing_url);
        
        // Saving them into the session
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        
        // If everything goes well..
        if( $twitteroauth->http_code == 200 ) 
        {
            // Let's generate the URL and redirect
            $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
            header('Location: ' . $url);
            exit();
        } 
        else 
        {
            // It's a bad idea to kill the script, but we've got to know when there's an error.
            die('Something wrong happened.');
        }
    }
    else
    {
        require_once '../Login_Twitbook/facebook/facebook.php';
        require_once '../Login_Twitbook/config/fbconfig.php';
        
        $facebook = new Facebook(array('appId' => APP_ID,'secret' => APP_SECRET ));
        
        try 
        {
            $uid = $facebook->getUser();
            $user_info = $facebook->api('/me');
        }
        catch (Exception $e) 
        {}
        
        if( !empty($user_info) ) 
        {
            $artist_id = $_SESSION['attach_artist_id'];
        
            $fb_access_token = $facebook->getAccessToken();
            $username = $user_info['username'];
            if( !$username )
                $username = $user_info['name'];
                
            mysql_update('mydna_musicplayer',
                         array("fb_uid" => $uid,
                               "fb_access_token" => $fb_access_token,
                               "facebook" => $username),
                         'id',$artist_id);
            
            $_SESSION['attach_artist_id'] = 0;

            header("Location: /manage/artist_management.php?userId=$artist_id");
            exit();
        } 
        else 
        {
            $args = array('scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access');
            $login_url = $facebook->getLoginUrl($args);
            header("Location: $login_url");
            exit();
        }
    }

?>


