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
    }

    //print "No user!\n";
    header("Location: /");
    die();
    //die("done done\n\n");

?>