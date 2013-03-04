<?php
    
    $browser = get_browser(null,TRUE);
    if( $browser['browser'] == 'IE' && $browser['majorver'] < 8 )
    {
        include_once 'unsupported_browser.php';
        die();
    }

    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    session_start();
    session_write_close();
    
    $artist_url = get_artist_url_for_page();
    
    if( $artist_url )
    {
        include_once 'player.php';
    }
    else
    {
        if( $_SESSION['sess_userId'] > 0 )
        {
            $user_id =  $_SESSION['sess_userId'];
            if( $_SESSION['sess_userType'] == 'ARTIST' )
            {
                header("Location: /manage/artist_management.php?userId=$user_id");
                die();
            }
            else if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' )
            {
                header("Location: /manage/dashboard.php");
                die();
            }
            else if( $_SESSION['sess_userType'] == 'LABEL' )
            {
                header("Location: /manage/dashboard.php");
                die();
            }
        }
        include_once 'home.php';
        die();
    }

?>