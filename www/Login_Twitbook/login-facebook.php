<?php 

    session_start();

    require_once 'facebook/facebook.php';
    require_once 'config/fbconfig.php';
    require_once 'config/functions.php';
    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';

    $facebook = new Facebook(array(
                'appId' => APP_ID,
                'secret' => APP_SECRET,
//            'cookie' => true
            ));

    # Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
    try {
        $uid = $facebook->getUser();
        $user = $facebook->api('/me');
    } catch (Exception $e) {

    }
	############# End userdata ############


    if (!empty($user)) {
        # User info ok? Let's print it (Here we will be adding the login and registering routines)  

        $_SESSION['fb_access_token'] = $facebook->getAccessToken();
        $username = $user['name'];
		$user_info = $user;
		$auto_incremented_id = '';
		//Calling the DB USER object
        $user = new User();
        $userdata = $user->checkUser($uid, 'facebook', $username,$user_info,$music_str,$auto_incremented_id);
        if(!empty($userdata))
        {
            $url = loginArtistFromRow($userdata);
			header("Location: $url");
        }
    } else {
        # For testing purposes, if there was an error, let's kill the script
        //$facebook->setSession(null);
        $login_url = $facebook->getLoginUrl(array(
			'scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access'));
        @header("Location: " . $login_url);
    }
    
?>
