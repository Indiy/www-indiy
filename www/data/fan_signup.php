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
    else if( $method == 'send_register_token' )
    {
        do_send_register_token();
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
            
            $url = login_fan_from_row($fan);
            $output = array(
                            "url" => $url,
                            "success" => 1
                            );
            print json_encode($output);
        }
        else
        {
            $output = array("error" => 1);
            print json_encode($output);
        }
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
