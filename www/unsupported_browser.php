<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    $is_windows = FALSE;
    if( strpos($_SERVER['HTTP_USER_AGENT'],"Windows") !== FALSE )
    {
        $is_windows = TRUE;
    }
    
    $is_msie = FALSE;
    $is_very_old_msie = FALSE;
    if( preg_match('/MSIE ([0-9]*)/',$uas,$matches) === 1 )
    {
        $is_msie = TRUE;
        $ie_major = intval($matches[1]);

        if( $ie_major < 8 )
        {
            $is_very_old_msie = TRUE;
        }
    }
    
    $return_url = FALSE;
    if( isset($_GET['return_url']) )
    {
        $return_url = $_GET['return_url'];
    }
    else
    {
        if( __FILE__ != "unsupported_browser.php" )
        {
            if( $_SERVER["HTTPS"] == "on" )
            {
                $return_url .= "https://";
            }
            else
            {
                $return_url .= "http://";
            }
            $return_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
    }
    
    include_once "templates/unsupported_browser.html";

?>