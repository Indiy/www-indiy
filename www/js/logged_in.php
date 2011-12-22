<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    include('../includes/config.php');
    include('../includes/functions.php');   

    if( $_SESSION['sess_userName'] )
    {
        $name = '"' . $_SESSION['sess_userName'] . '"';
    }
    else
    {
        $name = "false";
    }

?>

var g_userName = <?=$name;=?>;
