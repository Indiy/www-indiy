<?php

	require_once '../includes/config.php';
	include_once '../includes/functions.php';	

	if($_SESSION['sess_userId'] == '')
	{
		header("Location: /index.php");
		exit();
	}
    else
    {
        header("Location: dashboard.php");
    }

?>

