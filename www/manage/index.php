<?php

	require_once '../includes/config.php';
	include_once '../includes/functions.php';	

	if( $_SESSION['sess_userId'] == '' )
	{
		header("Location: /index.php");
		exit();
	}
    else
    {
        if( $_SESSION['sess_userType'] == 'ARTIST' )
        {
            $artist_id = $_SESSION['sess_userId'];
            header("Location: /manage/artist_management.php?userId=$artist_id");
        }
        else
        {
            header("Location: /manage/dashboard.php");
        }
    }

?>

