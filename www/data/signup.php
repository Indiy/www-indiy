<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);

    if( isset($data['network']) )
    {
        $network = $data['network'];
        if( $network == 'twitter' )
        {
            do_twitter_signup($data);
        }
        else if( $network == 'facebook' )
        {
            do_facebook_signup($data);
        }
        else
        {
            $output = array("error" => "Unknown network.");
            print json_encode($output);
        }
    }
    else
    {
        do_regular_signup($data);
    }
    
    function do_regular_signup($data)
    {
        $name = $data['name'];
        $url = $data['url'];
        $email = $data['email'];
        $password = md5($data['password']);

        $error = FALSE;

        $sql = "SELECT * FROM mydna_musicplayer";
        $sql .= " WHERE url = '$url' ";
        $sql .= " OR email = '$email' ";
        $q = mysql_query($sql) or die("bad sql: '$sql'");
        $row = mf($q);
        if( $row )
        {
            if( $row['url'] == $url )
                $error = "That URL is already taken.";
            else
                $error = "User already exists with that name or email address.";
        }
        else
        {
            $values = array("artist" => $name,
                            "url" => $url,
                            "email" => $email,
                            "password" => $password,
                            );
            if( mysql_insert('mydna_musicplayer',$values) )
            {
                $insert_id = mysql_insert_id();
                $q = mysql_query("SELECT * FROM mydna_musicplayer WHERE id = $insert_id");
                $row = mf($q);
                post_signup($row);
                $url = loginArtistFromRow($row);
            }
            else
            {
                $error = "Database error, please try again.";
            }
        }

        $output = array("error" => $error,"url" => $url);
        print json_encode($output);
        die();
    }
    
    function do_signup_check($data)
    {
        $name = $data['name'];
        $url = $data['url'];
        
        $error = FALSE;
        
        $sql = "SELECT * FROM mydna_musicplayer";
        $sql .= " WHERE url = '$url' ";
        $q = mysql_query($sql) or die("bad sql: '$sql'");
        $row = mf($q);
        if( $row )
        {
            if( $row['url'] == $url )
                $error = "That URL is already taken.";
            else
                $error = "User already exists with that name or email address.";
            
            $output = array("error" => $error);
            print json_encode($output);
            die();
        }
        
        $_SESSION['signup_name'] = $name;
        $_SESSION['signup_url'] = $url;
    }
    
    function do_twitter_signup($data)
    {
        do_signup_check($data);
    }
    function do_facebook_signup($data)
    {
        do_signup_check($data);

        require_once '../Login_Twitbook/facebook/facebook.php';
        require_once '../Login_Twitbook/config/fbconfig.php';
        
        $args = array('appId' => APP_ID,'secret' => APP_SECRET);
        
        $facebook = new Facebook($args);
        
        $args = array(
                      'scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,manage_pages,offline_access',
                      'redirect_uri' => trueSiteUrl() . "/Login_Twitbook/signup-facebook.php",
                      );
        
        $login_url = $facebook->getLoginUrl($args);
        $output = array("error" => $error,"url" => $login_url);
        print json_encode($output);
        die();
        
    }
?>
