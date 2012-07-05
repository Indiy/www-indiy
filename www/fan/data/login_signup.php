<?php
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    require_once '../../includes/login_helper.php';
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    $method = $_REQUEST['method'];
    
    if( $method == 'register' )
    {
        do_register();
    }
    else if( $method == 'login' )
    {
        do_login();
    }
    else if( $method == 'send_register_token' )
    {
        do_send_register_token();
    }
    
    function do_login()
    {
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $hash_password = md5($email . $password);
        
        $sql = "SELECT * FROM fans WHERE email='$email' AND password='$hash_password'";
        $fan = mf(mq($sql));
        if( $fan )
        {
            fan_login($fan);
        }
        else
        {
            $output = array("error" => 1);
            print json_encode($output);
        }
    }
    
    function do_register()
    {
        $password = $_REQUEST['password'];
        $register_token = $_REQUEST['register_token'];
        
        $sql = "SELECT * FROM fans WHERE register_token='$register_token'";
        $fan = mf(mq($sql));
        if( $fan )
        {
            $email = $fan['email'];
            $hash_password = md5($email . $password);
        
            $updates = array("password" => $hash_password);
            mysql_update("fans",$updates,'id',$fan['id']);
            
            fan_login($fan);
        }
        else
        {
            $output = array("error" => 1);
            print json_encode($output);
        }
    }
    
    function fan_login($fan)
    {
        $_SESSION['fan_id'] = $fan['id'];
        $expire = time() + 60*24*60*60;
        $cookie_domain = str_replace("http://www.","",trueSiteUrl());
        setcookie("FAN_EMAIL",$fan['email'],$expire,"/",$cookie_domain);
        $output = array("success" => 1,
                        "url" => fan_site_url(),
                        );
        print json_encode($output);
    }
    
    function do_send_register_token()
    {
        $email = $_REQUEST['email'];
        $register_token = random_string(32);
        
        $fan = mf(mq("SELECT * FROM fans WHERE email='$email'"));
        if( $fan )
        {
            $values = array("register_token" => $register_token,
                            );
            mysql_update('fans',$values,'id',$fan['id']);
        }
        else
        {
            $values = array("email" => $email,
                            "register_token" => $register_token,
                            );
            mysql_insert('fans',$values);
        }
        
        $register_link = fan_site_url() . "/register.php?token=$register_token";
        $register_generic_link = fan_site_url() . "/register.php";
        
        $to = $email;
        $subject = "MyArtistDNA Fan Account Registration";
        $message = <<<END
        
Thank you for registering for a fan account at MyArtistDNA.

Click the link below to verify your email address.

$register_link

Or go to $register_generic_link and enter the code:

$register_token

Be Heard. Be Seen. Be Independent.

END;
        $from = "no-reply@myartistdna.com";
        $headers = "From:" . $from;

        mail($to,$subject,$message,$headers);
        echo "{ \"success\": 1 }\n";
    }

?>