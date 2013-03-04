<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    
    session_start();
    session_write_close();
    
    $fan_site_url = fan_site_url();
    
    include_once 'templates/fan_login.html';

?>