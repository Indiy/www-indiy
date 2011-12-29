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
            $artist_id = $userdata['id'];
            if( $user_info['username'] )
            {
                $username = $user_info['username'];
                $url = "http://graph.facebook.com/$username/picture";
                $artist_logo = $artist_id . "_" . rand(11111,99999) . "_twitter.jpg";
                $file_path = '../artists/images/' . $artist_logo;
                
                $contents = file_get_contents($url);
                if( $contents )
                {
                    file_put_contents($file_path,$contents);
                    mysql_update("mydna_musicplayer",array("logo" => $artist_logo),"id",$artist_id);
                }
            }
        
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
