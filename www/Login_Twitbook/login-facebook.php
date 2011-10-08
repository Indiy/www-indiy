<?php
session_start();
require 'facebook/facebook.php';
require 'config/fbconfig.php';
require 'config/functions.php';
$facebook = new Facebook(array(
            'appId' => APP_ID,
            'secret' => APP_SECRET,
            'cookie' => true
        ));

$session = $facebook->getSession();

//print_r($session);

if (!empty($session)) {
    # Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
    try {
        $uid = $facebook->getUser();
        $user = $facebook->api('/me');
    } catch (Exception $e) {

    }
	############# End userdata ############

	############# Getting the user's movies like ############
	try {
        $uid = $facebook->getUser();
        $user_movies = $facebook->api('/me/movies');
    } catch (Exception $e) {

    }	
	if (!empty($user_movies)) {
		if(count($user_movies["data"]) > 0) :
			$movies_str = "";
			foreach($user_movies["data"] as $movies_arr)
			{
				$movies_str .= $movies_arr['name'].", ";	
			}
		endif;
	}
	############# End movie ############

	############# Getting the user's music like ############
	try {
        $uid = $facebook->getUser();
        $user_music = $facebook->api('/me/music');
    } catch (Exception $e) {

    }	

	if (!empty($user_music)) {
		if(count($user_music["data"]) > 0) :
			$music_str = "";
			foreach($user_music["data"] as $music_arr)
			{
				$music_str .= $music_arr['name'].", ";	
			}
		endif;
	}
	############# End music ############
	
	############# Getting the user's television like ############
	try {
        $uid = $facebook->getUser();
        $user_television = $facebook->api('/me/television');
    } catch (Exception $e) {

    }	
	if (!empty($user_television)) {
		if(count($user_television["data"]) > 0) :
			$television_str = "";
			foreach($user_television["data"] as $television_arr)
			{
				$television_str .= $television_arr['name'].", ";	
			}
		endif;
	}	
	############# End television ############

    if (!empty($user)) {
        # User info ok? Let's print it (Here we will be adding the login and registering routines)  

        $username = $user['name'];
		$user_info = $user;
		$auto_incremented_id = '';
		//Calling the DB USER object
        $user = new User();
			if(isset($_SESSION['me'])){
			$auto_incremented_id = $_SESSION['me'];
		}
        $userdata = $user->checkUser($uid, 'facebook', $username,$user_info,$music_str,$auto_incremented_id);

        if(!empty($userdata)){
            session_start();
			/*
            $_SESSION['id'] = $userdata['id'];
			$_SESSION['oauth_id'] = $uid;

            $_SESSION['username'] = $userdata['userName'];
            $_SESSION['oauth_provider'] = $userdata['oauth_provider'];*/
			$_SESSION['me'] = $userdata['id'];
            //header("Location: /index.php?p=addartist&id=".$userdata['id']);
			header("Location: /index.php?p=addartist");
        }
    } else {
        # For testing purposes, if there was an error, let's kill the script
        $facebook->setSession(null);
        $login_url = $facebook->getLoginUrl(array(
			'req_perms' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access'));
        @header("Location: " . $login_url);
    }
} else {
    # There's no active session, let's generate one
    $login_url = $facebook->getLoginUrl(array(
			'req_perms' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access'));
    @header("Location: " . $login_url);
}
?>
