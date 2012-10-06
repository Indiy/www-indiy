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
            if( strpos($_SERVER['SERVER_NAME'],"madd3v.com") !== FALSE )
            {
                setcookie('maintenance_cookie','adivyy129741jblad9y71',0,"/","madd3v.com");
                
            }
            else if( strpos($_SERVER['SERVER_NAME'],"myartistdna.com") !== FALSE )
            {
                setcookie('maintenance_cookie','adivyy129741jblad9y71',0,"/","myartistdna.com");
            }
        
            do_origonal();
            die();
        }
    }
    
    include_once "templates/error_maintenance.html";
    
    function do_origonal()
    {
        $redirect_url = $_SERVER['REDIRECT_URL'];
        
        if( endsWith($redirect_url,"php") )
        {
            include_once ".$redirect_url";
        }
        else
        {
            $path = ".$redirect_url";

            $mime_type = mime_content_type($path);
            
            if( endsWith($redirect_url,"css") )
            {
                $mime_type = "text/css";
            }
            else if( endsWith($redirect_url,"js") )
            {
                $mime_type = "application/javascript";
            }
            else if( endsWith($redirect_url,"otf") )
            {
                $mime_type = "font/opentype";
            }
            
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