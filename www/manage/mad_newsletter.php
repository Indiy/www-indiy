<?php

    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once '../includes/config.php';
    require_once '../includes/functions.php';	
	
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == '' )
	{
		header("Location: /index.php");
		exit();
	}
    
    if( $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
    {
		header("Location: /index.php");
		exit();
    }
    
    $filename = 'mad_newsletter.xls';
    
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    echo "email\r\n";
    echo "\r\n";

    $sql = "SELECT * FROM mad_newsletter";
    $q = mq($sql);
    while( $row = mf($q) )
    {
        $email = $row['email'];
        echo "$email\r\n";
    }
    echo "\r\n";
?>

