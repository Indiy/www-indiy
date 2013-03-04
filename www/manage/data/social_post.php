<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once PATH_TO_ROOT . 'includes/config.php';
    require_once PATH_TO_ROOT . 'includes/functions.php';

    require_once PATH_TO_ROOT . 'Login_Twitbook/twitter/twitteroauth.php';
    require_once PATH_TO_ROOT . 'Login_Twitbook/config/twconfig.php';
    
    require_once PATH_TO_ROOT . 'Login_Twitbook/facebook/facebook.php';
    require_once PATH_TO_ROOT . 'Login_Twitbook/config/fbconfig.php';
    
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        exit();
    }
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
        do_POST();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
    
    function do_POST()
    {
        $artist_id = $_REQUEST['artist_id'];
        $update_text = $_REQUEST['update_text'];
        $network = $_REQUEST['network'];
        
        $postedValues = array();
        
        $sql = "SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'";
        $artist = mf(mq($sql));
        if( $network == 'twitter' )
        {
            $oauth_token = $artist['oauth_token'];
            $oauth_secret = $artist['oauth_secret'];
            
            $postedValues['twitter_args'] = YOUR_CONSUMER_KEY .','. YOUR_CONSUMER_SECRET .','. $oauth_token .','. $oauth_secret;
            
            $connection = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $oauth_token, $oauth_secret);
            $content = $connection->get('account/verify_credentials');
            $result = $connection->post('statuses/update', array('status' => $update_text));
            $postedValues['twitter_content'] = $content;
            $postedValues['twitter_result'] = $result;
        }
        else if( $network == 'facebook' )
        {
            $fb_access_token = $artist['fb_access_token'];
            $postedValues['fb_a_t'] = $fb_access_token;
            try
            {
                $facebook = new Facebook(array('appId' => APP_ID,'secret' => APP_SECRET));
                $facebook->setAccessToken($fb_access_token);
                $result = $facebook->api('/me/feed','POST',array('message'=>$update_text));
                $postedValues['fb_result'] = $result;
            }
            catch(Exception $e)
            {
                $postedValues['fb_exception'] = $e;
            }
        }
        else
        {
            header("HTTP/1.0 500 Server Error");
            exit();
        }
        
        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
    }
?>

