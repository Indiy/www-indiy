<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    session_write_close();
    
    $login_failed = FALSE;
    if( $_REQUEST['failed'] )
    {
        $network = $_REQUEST['failed'];
        $login_failed = "No account found associated with your $network account";
    }

    include_once 'templates/login.html';
?>