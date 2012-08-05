<?php

    require_once 'includes/functions.php';
    require_once 'includes/config.php';

    $show_fan = FALSE;
    $show_artist = FALSE;
    $show_label = FALSE;
    $show_super_admin = FALSE;
    $artist_id = 0;
    
    $num_logins = 0;

    if( $_SESSION['fan_id'] )
    {
        $show_fan = TRUE;
        $num_logins++;
    }

    if( $_SESSION['sess_userType'] == 'ARTIST' )
    {
        $show_artist = TRUE;
        $artist_id = $_SESSION['sess_userId'];
        $num_logins++;
    }
    else if( $_SESSION['sess_userType'] == 'LABEL' )
    {
        $show_label = TRUE;
        $num_logins++;
    }
    else if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' )
    {
        $show_super_admin = TRUE;
        $num_logins++;
    }

    if( $num_logins == 0 )
    {
        print "Not Logged in.\n";
    }


    print "<html><body>";

    if( $show_fan )
    {
        print "<a href='/fan'>Fan Home Page</a><br/>";
        print "<br/>";
    }
    
    if( $show_artist )
    {
        print "<a href='/manage/artist_management.php?userId=$artist_id'>Artist Home Page</a><br/>";
        print "<br/>";
    }

    if( $show_label )
    {
        print "<a href='/manage/dashboard.php'>Label Home Page</a><br/>";
        print "<br/>";
    }
    if( $show_super_admin )
    {
        print "<a href='/manage/dashboard.php'>Admin Home Page</a><br/>";
        print "<br/>";
    }
    
    print "<a href='/logout.php'>Logout</a><br/>";
    print "<br/>";
    
    print "</body></html>";

?>