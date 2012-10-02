<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	require_once '../includes/login_helper.php';
    
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    if( $_SESSION['sess_userId'] == '' )
	{
		header("Location: /index.php");
		die();
	}

    if( isset($_REQUEST['email']) )
    {
        $email = $_REQUEST['email'];

        $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE email='$email'"));
        
        post_artist_signup($artist);
        
        print "Email sent<br/>";
        print "<br/>";
    }
    

?>

<form>
    Email Address: <input name='email'>
</form>
