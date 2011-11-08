<?php

    session_start();

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('includes/config.php');
    include('includes/functions.php');
    
    $url = "";
    $result = 0;
    
    if( $_REQUEST["username"] != '' && $_REQUEST["password"] != '' ) 
    {
        $user = mysql_real_escape_string($_REQUEST['username']);
        $pass = md5($_REQUEST['password']);	
        $result = mysql_query("SELECT * FROM mydna_musicplayer WHERE (email='$user' || url='$user' || username='$user') AND password='$pass' AND activeStatus='1'");
        if( mysql_num_rows($result) > 0 ) 
        {	
            $row = mf($result);
            $myid = $row['id'];
            $_SESSION['sess_userId'] =	$row['id'];	
            $_SESSION['sess_userName'] = $row['artist'];
            $_SESSION['sess_userUsername'] = $row['username'];
            $_SESSION['sess_userEmail'] =  $row['email'];
            $_SESSION['sess_userType'] = 'ARTIST';
			$_SESSION['sess_userURL'] = $row['url'];
            $inTwoMonths = 60 * 60 * 24 * 60 + time();
            setcookie($cookievar, $myid, $inTwoMonths);
            $_SESSION['me'] = $row['id'];
            $url = trueSiteUrl() . "/manage/artist_management.php?userId=$myid&session_id=". session_id();
            $result = 0;
        }
        else 
        {
            $result = mysql_query("SELECT * FROM myartist_users WHERE email='$user' AND password='$pass' AND activeStatus='1'");
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
                $result = 0;
            }
            else
            {
                $result = 1;
            }
        }
    }
    else
    {
        $result = 2;
    }

    $output = array("result" => $result,"url" => $url);
    print json_encode($output) ."\n";
?>