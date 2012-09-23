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
                                   ));
    
    try
    {
        $uid = $facebook->getUser();
        $user = $facebook->api('/me');
    }
    catch( Exception $e )
    {}
    
    print "<html><body><pre>\n\n";
    
    print "user: "; var_dump($user);
    print "session: "; var_dump($_SESSION);
    
    
    if (!empty($user))
    {
        $fb_name = $user['name'];
        $fb_access_token = $facebook->getAccessToken();
        $email = $user['email'];

        $name = $_SESSION['signup_name'];
        if( !$name )
            $name = $fb_name;

        $url = $_SESSION['signup_url'];
        
        $values = array("artist" => $name,
                        "url" => $url,
                        "facebook" => $fb_name,
                        "fb_uid" => $uid,
                        "fb_access_token" => $fb_access_token,
                        "oauth_provider" => 'facebook',
                        "oauth_uid" => $uid,
                        "email" => $email,
                        );
        
        print "values: "; var_dump($values);
        
        mysql_insert('mydna_musicplayer',$values);
        
        $artist_id = mysql_insert_id();
        
        $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
        $url = loginArtistFromRow($artist_data);
        //header("Location: $url");
    }
    else
    {
        print "No user!\n";
        //header("Location: /");
    }
    die("done done\n\n");

?>