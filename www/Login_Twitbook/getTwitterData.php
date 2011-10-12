<?php require("twitter/twitteroauth.php");
require 'config/twconfig.php';
require 'config/functions.php';
session_start();

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
			$auto_incremented_id = $_SESSION['me'];
		}
        $userdata = $user->checkUser($uid, 'twitter', $username,$user_info,$music_str,$auto_incremented_id);
		

        if(!empty($userdata)){
            session_start();
			$_SESSION['me'] = $userdata['id'];//added for login
			// Set cookie to expire in two months
			$inTwoMonths = 60 * 60 * 24 * 60 + time();
			// Set the user cookie
			setcookie($cookievar, $userdata['id'], $inTwoMonths);

            $_SESSION['id'] = $userdata['id'];
			$_SESSION['oauth_id'] = $uid;
            $_SESSION['username'] = $userdata['username'];
            $_SESSION['oauth_provider'] = $userdata['oauth_provider'];
            //header("Location: /index.php?p=addartist&id=".$userdata['id']);
			//header("Location: /index.php?p=addartist&id=$uid");
			header("Location: ../manage/dashboard.php");
        }
    }
} else {
    // Something's missing, go back to square 1
    header('Location: login-twitter.php');
}
?>