<?php

function loginArtistFromRow($row)
{
    $expire = time() + 60*24*60*60;
    $cookie_domain = str_replace("http://www.","",trueSiteUrl());
    setcookie("LOGIN_EMAIL",$row['email'],$expire,"/",$cookie_domain);

    $myid = $row['id'];
    $_SESSION['sess_userId'] =	$myid;		
    $_SESSION['sess_userName'] = $row['artist'];
    $_SESSION['sess_userUsername'] = $row['username'];
    $_SESSION['sess_userEmail'] =  $row['email'];
    $_SESSION['sess_userType'] = 'ARTIST';
    $_SESSION['sess_userURL'] = $row['url'];
    
    $url = trueSiteUrl() . "/manage/artist_management.php?userId=$myid";
    return $url;
}

function login_fan_from_row($row)
{
    $_SESSION['fan_id'] = $row['id'];
    $expire = time() + 60*24*60*60;
    $cookie_domain = str_replace("http://www.","",trueSiteUrl());
    setcookie("FAN_EMAIL",$row['email'],$expire,"/",$cookie_domain);

    return fan_site_url();
}

function post_signup($row)
{
    $email = $row['email'];
    if( $email )
    {
        $to = $email;
        $message = <<<END

Thanks for signing up!

Welcome! We're excited to have you join us and wanted to give you your login info for your records.

Login Email Address: $email
        
Enjoy your membership, and if you have any quesitons, email us at support@myartistdna.com

Thank You,
The MyArtistDNA Team

Be Heard. Be Seen. Be Independent.

END;
        $subject = "Welcome to MyArtistDNA";
        $from = "no-reply@myartistdna.com";
        $headers = "From:" . $from;
        
        mail($to,$subject,$message,$headers);
    }
}

function artist_login($username,$password)
{
    $user = mysql_real_escape_string($username);
    $pass = md5($password);
    $result = mysql_query("SELECT * FROM mydna_musicplayer WHERE (email='$user' || url='$user' || username='$user') AND password='$pass' AND activeStatus='1'");
    if( mysql_num_rows($result) > 0 )
    {
        $row = mf($result);
        $url = loginArtistFromRow($row);
        return $url;
    }
    return FALSE;
}

function admin_login($username,$password)
{
    $user = mysql_real_escape_string($username);
    $pass = md5($password);
    $result = mysql_query("SELECT * FROM myartist_users WHERE (email='$user' ||  username='$user') AND password='$pass'");
    if( mysql_num_rows($result) > 0 )
    {
        $row = mf($result);
        $_SESSION['sess_userId'] =	$row['id'];
        $_SESSION['sess_userName'] = $row['name'];
        $_SESSION['sess_userUsername'] = $row['name'];
        $_SESSION['sess_userEmail'] =  $row['email'];
        $_SESSION['sess_userType'] = $row['usertype'];
        $_SESSION['sess_userURL'] = $row['name'];
        $url = trueSiteUrl() . "/manage/dashboard.php?session_id=". session_id();
        return $url;
    }
    return FALSE;
}

function fan_login($username,$password)
{
    $email = $username;
    $hash_password = md5($email . $password);
    
    $sql = "SELECT * FROM fans WHERE email='$email' AND password='$hash_password'";
    $fan = mf(mq($sql));
    if( $fan )
    {
        $url = login_fan_from_row($fan);
        return $url;
    }
    return FALSE;
}

?>
