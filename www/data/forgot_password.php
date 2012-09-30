<?php

    define("PATH_TO_ROOT","../");

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    $method = $_REQUEST['method'];

    if( $method == "send_code" )
    {
        do_send_code();
        die();
    }
    else if( $method == "update_password" )
    {
        do_update_password();
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        print "Unknown request\n";
        var_dump($_REQUEST);
        die();
    }

    function send_reset_email($email,$register_token)
    {
        $link = trueSiteUrl() . "/recover_account.php?token=$register_token";
        $generic_link = trueSiteUrl() . "/recover_account.php";
    
        $to = $email;
        $subject = "Retrieve your MyArtistDNA Account!";

        ob_start();
        include PATH_TO_ROOT . "templates/email_forgot_password.html";
        $message = ob_get_contents();
        ob_end_clean();

        $from = "no-reply@myartistdna.com";

        $headers = "From: $from\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        
        mail($to,$subject,$message,$headers);
    }

    function do_send_code()
    {
        $error = "Email address not found.";
        $sent_email = FALSE;
        
        $email = $_REQUEST['email'];
        if( strlen($email) > 0 )
        {
            $register_token = random_string(32);

            $sql = "SELECT * FROM mydna_musicplayer WHERE email = '$email' LIMIT 1";
            $artist = mf(mq($sql));
            if( $artist )
            {
                $artist_id = $artist['id'];
            
                $values = array("register_token" => $register_token);
                mysql_update('mydna_musicplayer',$values,'id',$artist_id);

                $email = $artist['email'];
                
                if( !$sent_email )
                {
                    $sent_email = TRUE;
                    send_reset_email($email,$register_token);
                    $error = FALSE;
                }
            }
            
            $sql = "SELECT * FROM fans WHERE email='$email' LIMIT 1";
            $fan = mf(mq($sql));
            if( $fan )
            {
                $fan_id = $fan['id'];
                
                $values = array("register_token" => $register_token);
                mysql_update('fans',$values,'id',$fan_id);
                
                $email = $fan['email'];
                
                if( !$sent_email )
                {
                    $sent_email = TRUE;
                    send_reset_email($email,$register_token);
                    $error = FALSE;
                }
            }
        }

        if( $error )
        {
            $output = array("error" => $error);
            print json_encode($output);
            die();
        }
        else
        {
            $output = array("success" => 1);
            print json_encode($output);
            die();
        }
    }
    
    function do_update_password()
    {
        $error = "Invalid token.";
        $url = FALSE;
    
        $token = $_REQUEST['token'];
        $password = $_REQUEST['password'];
        
        $sql = "SELECT * FROM mydna_musicplayer WHERE register_token='$token' LIMIT 1";
        $artist = mf(mq($sql));
        if( $artist )
        {
            $artist_id = $artist['id'];
            
            $values = array("password" => $password,
                            "register_token" => "",
                            );
            mysql_update('mydna_musicplayer',$values,'id',$artist_id);
            $url = loginArtistFromRow($artist);
            $error = FALSE;
        }
        $sql = "SELECT * FROM fans WHERE register_token='$token' LIMIT 1";
        $fan = mf(mq($sql));
        if( $fan )
        {
            $fan_id = $fan['id'];
            $email = $fan['email'];
            
            $hash_password = md5($email . $password);
            
            $values = array("password" => $hash_password,
                            "register_token" => "",
                            );
            mysql_update('fans',$values,'id',$fan_id);
            $fan_url = login_fan_from_row($fan);
            if( !$url )
            {
                $url = $fan_url;
            }
            $error = FALSE;
        }
        
        if( $error )
        {
            $output = array("error" => $error);
            print json_encode($output);
            die();
        }
        else
        {
            $output = array("success" => 1,"url" => $url);
            print json_encode($output);
            die();
        }
    }


?>