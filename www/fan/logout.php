<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';

    unset($_SESSION['fan_id']);
    
    header("Location: /");

?>