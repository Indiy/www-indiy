<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
    require_once 'include/utils.php';
    
	if( $_SESSION['sess_userId'] == '')
	{
		header("Location: index.php");
		exit();
	}
    
    echo "<html><body><pre>\n";
    
    create_abbrevs();

?>
