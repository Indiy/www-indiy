<?php 

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';

    require_once 'facebook/facebook.php';
    require_once 'config/fbconfig.php';
    require_once 'config/functions.php';

    $facebook = new Facebook(array(
                'appId' => APP_ID,
                'secret' => APP_SECRET,
//            'cookie' => true
            ));

    try 
    {
        $uid = $facebook->getUser();
        $user = $facebook->api('/me');
    } 
    catch (Exception $e) 
    {}

    if (!empty($user)) 
    {
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
    } 
    else 
    {
        $login_url = $facebook->getLoginUrl(array(
			'scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access'));
        header("Location: " . $login_url);
    }
    
?>
