<?php

    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
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
        
        if( endsWith($request_uri,"php") )
        {
            include_once ".$request_uri";
        }
        else
        {
            $path = ".$request_uri";

            $mime_type = mime_content_type($path);
            header("Content-Type: $mime_type");
            
            $real_path = realpath($path);
            
            $length = filesize($real_path);
            header("Content-Length: $length");
            
            readfile($real_path);
        }
    }
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        
        return (substr($haystack, -$length) === $needle);
    }
?>