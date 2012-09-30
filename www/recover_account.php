<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    $token = FALSE;
    if( isset($_REQUEST['token']) )
    {
        $token = $_REQUEST['token'];
    }

    include_once 'templates/recover_account.html';

?>