<?php

    
    
    if( $_COOKIE['maintenance_cookie'] == 'adivyy129741jblad9y71' )
    {
        do_origonal();
        die();
    }
    
    if( isset($_REQUEST['maintainer']) )
    {
        $maintainer = $_REQUEST['maintainer'];
        if( $maintainer == 'adivyy129741jblad9y71' )
        {
            setcookie('maintenance_cookie','adivyy129741jblad9y71');
            do_origonal();
            die();
        }
    }
    
    include_once "templates/error_maintenance.html";
    
    function do_origonal()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        include ".$request_uri";
    }
?>