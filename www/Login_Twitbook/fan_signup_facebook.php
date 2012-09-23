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
        $fan = mf(mq("SELECT * FROM fans WHERE fb_uid='$uid'"));
        if( $fan )
        {
            $url = login_fan_from_row($fan);
            header("Location: $url");
            die();
        }

        $fan_id = FALSE;
        $email = $user['email'];

        $fan = mf(mq("SELECT * FROM fans WHERE email = '$email'"));
        if( $fan )
        {
            $fan_id = $fan['id'];
        }
    
        $fb_access_token = $facebook->getAccessToken();
        $extra = array("facebook_access_token" => $fb_access_token);
        $extra_json = json_encode($extra);
        
        $values = array("email" => $email,
                        "fb_uid" => $uid,
                        "extra_json" => $extra_json,
                        );
        
        if( $fan_id )
        {
            mysql_update('fans',$values,'id',$fan_id);
        }
        else
        {
            mysql_insert('fans',$values);
            $fan_id = mysql_insert_id();
        }

        $fan = mf(mq("SELECT * FROM fans WHERE id='$fan_id'"));
        $url = login_fan_from_row($fan);
        header("Location: $url");
        die();
    }

    //print "No user!\n";
    header("Location: /");
    die();
    //die("done done\n\n");

?>