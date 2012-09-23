<?php
    
    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';
    
    require_once '../Login_Twitbook/twitter/twitteroauth.php';
    require_once '../Login_Twitbook/config/twconfig.php';
    
    print "<html><body><pre>\n";
    
    if( !empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret']) )
    {
        $oauth_token = $_SESSION['oauth_token'];
        $oauth_token_secret = $_SESSION['oauth_token_secret'];
        $oauth_verifier = $_GET['oauth_verifier'];
    
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
        $access_token = $twitteroauth->getAccessToken($oauth_verifier);
        $user_info = $twitteroauth->get('account/verify_credentials');

        print "session: "; var_dump($_SESSION);
        print "user_info: "; var_dump($user_info);
        
        if( isset($user_info->error) )
        {
            print "Failed to get user info\n";
            //header("Location: /");
            die();
        }
        else
        {
            $uid = $user_info->id;
        
            $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE oauth_uid_twitter='$uid' OR ( oauth_uid='$uid' AND oauth_provider='twitter' )"));
            if( $artist_data )
            {
                $url = loginArtistFromRow($artist_data);
                header("Location: $url");
                die();
            }
        
            $screen_name = $user_info->screen_name;
            $oauth_token = $access_token['oauth_token'];
            $oauth_token_secret = $access_token['oauth_token_secret'];
            
            $name = $_SESSION['signup_name'];
            $url = $_SESSION['signup_url'];
            
            $values = array("artist" => $name,
                            "url" => $url,
                            "twitter" => $screen_name,
                            "oauth_uid_twitter" => $uid,
                            "oauth_token" => $oauth_token,
                            "oauth_secret" => $oauth_token_secret,
                            "oauth_provider" => 'twitter',
                            "oauth_uid" => $uid,
                            );
            
            print "values: "; var_dump($values);
            
            mysql_insert('mydna_musicplayer',$values);
            
            $artist_id = mysql_insert_id();
            
            $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
            $url = loginArtistFromRow($artist_data);
            //header("Location: $url");
            die();
        }
    }

    print "Failed to do twitter login\n";
    //header("Location: /");
    
?>