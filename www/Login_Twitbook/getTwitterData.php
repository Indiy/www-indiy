<?php 

require("twitter/twitteroauth.php");
require 'config/twconfig.php';
require 'config/functions.php';
session_start();

require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
    // We've got everything we need
    $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

// Let's request the access token
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);

// Save it in a session var
    $_SESSION['access_token'] = $access_token;

// Let's get the user's info
    $user_info = $twitteroauth->get('account/verify_credentials');

    if (isset($user_info->error)) {
        // Something's wrong, go back to square 1  
        header('Location: login-twitter.php');
    } else {
        $uid = $user_info->id;
        $username = $user_info->name;


        $user = new User();
		$auto_incremented_id = '';
		$music_str = '';
		if(isset($_SESSION['me'])){
			//$auto_incremented_id = $_SESSION['me'];
		}
        $userdata = $user->checkUser($uid, 'twitter', $username,$user_info,$music_str,$auto_incremented_id);
		

        if(!empty($userdata)){
            session_start();
			$_SESSION['me'] = $userdata['id'];//added for login
			// Set cookie to expire in two months
			$inTwoMonths = 60 * 60 * 24 * 60 + time();
			// Set the user cookie
			setcookie($cookievar, $userdata['id'], $inTwoMonths);

            $myid = $userdata['id'];
			$_SESSION['me'] = $userdata['id'];
			$_SESSION['sess_userId'] =	$userdata['id'];		
			$_SESSION['sess_userName'] = $userdata['artist'];
			$_SESSION['sess_userUsername'] = $userdata['userName'];
            $_SESSION['sess_userEmail'] =  $userdata['email'];
            $_SESSION['sess_userType'] = $userdata['type'];
			$_SESSION['sess_userURL'] = $userdata['url'];
			header("Location: " . trueSiteUrl() . "/manage/artist_management.php?userId=$myid&session_id=". session_id());
            /*
            print "<html><body><pre>\n";
            print_r($user_info);
            print_r($userdata);
            print_r($_SESSION);
            */
        }
    }
} else {
    // Something's missing, go back to square 1
    header('Location: login-twitter.php');
}
?>