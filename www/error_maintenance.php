<?php

    error_reporting(E_ALL);
    
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    if( maintenance_allow_request() )
    {
        $redirect_url = $_SERVER['REDIRECT_URL'];
        
        if( maintenance_endsWith($redirect_url,"php") )
        {
            include_once ".$redirect_url";
        }
        else
        {
            $path = ".$redirect_url";
            
            $mime_type = mime_content_type($path);
            
            if( maintenance_endsWith($redirect_url,"css") )
            {
                $mime_type = "text/css";
            }
            else if( maintenance_endsWith($redirect_url,"js") )
            {
                $mime_type = "application/javascript";
            }
            else if( maintenance_endsWith($redirect_url,"otf") )
            {
                $mime_type = "font/opentype";
            }
            
            header("Content-Type: $mime_type");
            
            $real_path = realpath($path);
            
            $length = filesize($real_path);
            header("Content-Length: $length");
            
            readfile($real_path);
        }
        
        die();
    }
    
    include_once "templates/error_maintenance.html";
    die();
    
    function maintenance_allow_request()
    {
        if( $_COOKIE['maintenance_cookie'] == 'adivyy129741jblad9y71' )
            return TRUE;
        
        if( isset($_REQUEST['maintainer']) )
        {
            if( $_REQUEST['maintainer'] == 'adivyy129741jblad9y71' )
            {
               if( strpos($_SERVER['SERVER_NAME'],"madd3v.com") !== FALSE )
               {
                   setcookie('maintenance_cookie','adivyy129741jblad9y71',0,"/","madd3v.com");
               }
               else if( strpos($_SERVER['SERVER_NAME'],"myartistdna.com") !== FALSE )
               {
                   setcookie('maintenance_cookie','adivyy129741jblad9y71',0,"/","myartistdna.com");
               }
               return TRUE;
            }
        }
        
        $redirect_url = $_SERVER['REDIRECT_URL'];
        
        if( maintenance_endsWith($redirect_url,'error.css') )
            return TRUE;
        if( maintenance_endsWith($redirect_url,'error_mad_logo.jpg') )
            return TRUE;
    }
    
    function maintenance_endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        
        return (substr($haystack, -$length) === $needle);
    }
?>