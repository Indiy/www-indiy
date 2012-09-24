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
        $artist_url = FALSE;
        $fan_url = FALSE;
    
        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE fb_uid='$uid' OR ( oauth_uid='$uid' AND oauth_provider='facebook' )"));
        if( $artist )
        {
            $artist_url = loginArtistFromRow($artist);
        }
        
        $fan = mf(mq("SELECT * FROM fans WHERE fb_uid='$uid'"));
        if( $fan )
        {
            $fan_url = login_fan_from_row($fan);
        }
        
        if( $artist_url )
        {
            header("Location: $artist_url");
            die();
        }
        
        if( $fan_url )
        {
            header("Location: $fan_url");
            die();
        }
    }

    //print "No user!\n";
    header("Location: /login_failed.php");
    die();
    //die("done done\n\n");

?>