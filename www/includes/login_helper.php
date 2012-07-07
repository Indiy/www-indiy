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
    
    $url = trueSiteUrl() . "/manage/artist_management.php?userId=$myid&session_id=" . session_id();
    return $url;
}

function post_signup($row)
{
    $email = $row['email'];
    $username = $row['username'];
    if( $email )
    {
        $to = $email;
        $message = <<<END

Thanks for signing up!

Welcome! We're excited to have you join us and wanted to give you your login info for your records.

Username: $username
        
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

?>
