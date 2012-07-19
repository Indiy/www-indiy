<?php 
    
    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    
    require_once '../Login_Twitbook/twitter/twitteroauth.php';
    require_once '../Login_Twitbook/config/twconfig.php';
    
    
    if(!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) 
    {
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
        $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
        $user_info = $twitteroauth->get('account/verify_credentials');
        
        if( isset($user_info->error) )
        {
            die("Failed to get user info");
        } 
        else
        {
            $uid = $user_info->id;
            $screen_name = $user_info->screen_name;
            $oauth_token = $access_token['oauth_token'];
            $oauth_token_secret = $access_token['oauth_token_secret'];
            
            $artist_id = $_SESSION['attach_artist_id'];
            
            mysql_update('mydna_musicplayer',
                         array("oauth_uid_twitter" => $uid,
                               "oauth_token" => $oauth_token,
                               "oauth_secret" => $oauth_token_secret,
                               "twitter" => $screen_name),
                         'id',$artist_id);
            
            $_SESSION['attach_artist_id'] = 0;

            header("Location: /manage/artist_management.php?userId=$artist_id");
            exit();
        }
    } 
    else 
    {
        die("Failed to do twitter login");
    }
    
?>
