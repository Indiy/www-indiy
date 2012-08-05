<?php

    require_once 'includes/functions.php';
    require_once 'includes/config.php';

    $show_fan = FALSE;
    if( $_SESSION['fan_id'] )
    {
        $show_fan = TRUE;
    }
    $show_artist = FALSE;
    $artist
    if( $_SESSION['sess_userType'] == 'ARTIST' )
    {
        $show_artist = TRUE;
        
    }
    

    print "<html><body><pre>";
    print "Role Choice placeholder";

?>