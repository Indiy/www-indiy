<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';
    
    require_once 'facebook/facebook.php';
    require_once 'config/fbconfig.php';
    require_once 'config/functions.php';
    
    
    $args = array('appId' => APP_ID,'secret' => APP_SECRET);
    $facebook = new Facebook($args);

    try
    {
        $uid = $facebook->getUser();
        $user = $facebook->api('/me');
    }
    catch( Exception $e )
    {}
    
    //print "<html><body><pre>\n\n";
    
    //print "user: "; var_dump($user);
    //print "session: "; var_dump($_SESSION);
    
    if( !empty($user) )
    {
        $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE fb_uid='$uid' OR ( oauth_uid='$uid' AND oauth_provider='facebook' )"));
        if( $artist_data )
        {
            $url = loginArtistFromRow($artist_data);
            header("Location: $url");
            die();
        }
    
        $fb_name = $user['name'];
        $fb_access_token = $facebook->getAccessToken();
        $email = $user['email'];

        $name = $_SESSION['signup_name'];
        if( !$name )
            $name = $fb_name;

        $url = $_SESSION['signup_url'];
        
        $preview_key = random_string(8);

        $values = array("artist" => $name,
                        "url" => $url,
                        "facebook" => $fb_name,
                        "fb_uid" => $uid,
                        "fb_access_token" => $fb_access_token,
                        "oauth_provider" => 'facebook',
                        "oauth_uid" => $uid,
                        "email" => $email,
                        "preview_key" => $preview_key,
                        );
        
        //print "values: "; var_dump($values);
        
        mysql_insert('mydna_musicplayer',$values);
        
        $artist_id = mysql_insert_id();
        
        $artist_data = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
        $url = loginArtistFromRow($artist_data);
        header("Location: $url");
        die();
    }

    //print "No user!\n";
    header("Location: /");
    die();
    //die("done done\n\n");

?>