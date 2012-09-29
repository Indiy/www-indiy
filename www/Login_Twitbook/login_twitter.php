<?php
    
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
            header("Location: /login.php?failed=twitter");
            die();
        }
        else
        {
            $uid = $user_info->id;
        
            $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE oauth_uid_twitter='$uid' OR ( oauth_uid='$uid' AND oauth_provider='twitter' )"));
            if( $artist )
            {
                $artist_url = loginArtistFromRow($artist);
            }
            
            $fan = mf(mq("SELECT * FROM fans WHERE tw_uid='$uid'"));
            if( $fan )
            {
                $fan_url = login_fan_from_row($fan);
            }
            
            if( $artist_url )
            {
                header("Location: $artist_url");
                die();
            }
            
            if( $fan_url )
            {
                header("Location: $fan_url");
                die();
            }
        }
    }

    //print "Failed to do twitter login\n";
    header("Location: /login.php?failed=twitter");
    die();
    
?>