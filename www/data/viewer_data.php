<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include '../includes/functions.php';
    include '../includes/config.php';

    $email = $_REQUEST['email'];
    
    if( $email )
    {
        setcookie('PAGE_VIEWER_EMAIL',$email,time() + 365*24*60*60,'/');

        mysql_insert('mad_newsletter',array( 'email' => $email ));
        echo "{ \"success\": 1 }\n";
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        echo "{ \"failure\": 1 }\n";
    }

?>