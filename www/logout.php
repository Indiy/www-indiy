<?php
    
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    require_once 'includes/login_helper.php';
    
    session_destroy();
    header("Location: /index.php");
    exit();
    
?>