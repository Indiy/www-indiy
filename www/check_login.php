<?php

    session_start();

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('includes/config.php');
    include('includes/functions.php');
    
    $url = "";
    $result = 0;
    
    if ($_REQUEST["username"]!='' && $_REQUEST["password"]!='') 
    {
        $user = addslashes($_REQUEST['username']);
        $pass = md5($_REQUEST['password']);	
        $result = mysql_query("select * from mydna_musicplayer where (email='$user' || url='$user' || username='$user') AND password='$pass' AND activeStatus='1'");
        $me_row = mysql_num_rows($result);	
        if ($me_row > 0) {	
            $row = mf($result);
            $myid = $row['id'];				
            //session_regenerate_id();
            $_SESSION['sess_userId'] =	$row['id'];		
            $_SESSION['sess_userName'] = $row['artist'];
            $_SESSION['sess_userUsername'] = $row['username'];
            $_SESSION['sess_userEmail'] =  $row['email'];
            $_SESSION['sess_userType'] = $row['type'];
			$_SESSION['sess_userURL'] = $row['url'];
            
            // Set cookie to expire in two months
            $inTwoMonths = 60 * 60 * 24 * 60 + time();
            // Set the user cookie
            setcookie($cookievar, $myid, $inTwoMonths);
            if( $row['type'] == 2 )
            {
                $url = "http://www.myartistdna.com/manage/dashboard.php?session_id=". session_id();
            }
            else
            {
                $_SESSION['me'] = $row['id'];
                $url = "http://www.myartistdna.com/manage/artist_management.php?userId=$myid&session_id=". session_id();
            }
            $result = 1;
        }
        else 
        {
            $result = 0;
        }
    }
    else
    {
        $result = 2;
    }

    $output = array("result" => $result,"url" => $url);
    print json_encode($output) ."\n";
?>