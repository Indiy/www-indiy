<?php

    session_start();

    $network = $_REQUEST['network'];

    if( $network == 'twitter' )
    {
        require_once '../Login_Twitbook/twitter/twitteroauth.php';
        require_once '../Login_Twitbook/config/twconfig.php';
        
        $_SESSION['attach_artist_id'] = $_REQUEST['artist_id'];
        
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
        } 
        else 
        {
            // It's a bad idea to kill the script, but we've got to know when there's an error.
            die('Something wrong happened.');
        }
    }

?>


