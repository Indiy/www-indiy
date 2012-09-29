<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    session_write_close();
    
    if( isset($_SESSION['sess_userId']) )
    {
        header("Location: /manage/");
        die();
    }
    else if( isset($_SESSION['fan_id']) )
    {
        header("Location: /fan/");
        die();
    }
    
    $login_failed = FALSE;
    if( $_REQUEST['failed'] )
    {
        $network = $_REQUEST['failed'];
        $login_failed = "No MyArtistDNA account found associated with your $network account.";
    }

    include_once 'templates/login.html';
?>