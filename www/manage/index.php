<?php

	require_once '../includes/config.php';
	require_once '../includes/functions.php';	

    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == '' )
	{
		header("Location: /login.php");
		die();
	}
    else
    {
        if( $_SESSION['sess_userType'] == 'ARTIST' )
        {
            $artist_id = $_SESSION['sess_userId'];
            header("Location: /manage/artist_management.php?userId=$artist_id");
            die();
        }
        else
        {
            header("Location: /manage/dashboard.php");
            die();
        }
    }

?>

