<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        exit();
    }
    
    if( $_REQUEST['add_user'] )
    {
        do_add_user();
    }
    else if( $_REQUEST['add_label'] )
    {
        do_add_label();
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        print "Unknown request\n";
        var_dump($_REQUEST);
    }
    
    function do_add_user()
    {
        if( $_SESSION['sess_userType'] != 'SUPER_ADMIN'
            && $_SESSION['sess_userType'] != 'LABEL' ) 
        {
            header("HTTP/1.0 403 Forbidden");
            exit();
        }
    
        $artist = $_REQUEST['artist'];
        $url = $_REQUEST['url'];
        $email = $_REQUEST['email'];
        $password = md5($_REQUEST['password']);
        $label_id = "NULL";
        if( $_SESSION['sess_userType'] == 'LABEL' )
            $label_id = $_SESSION['sess_userId'];
        
        $tables = "artist|url|email|password|label_id";
		$values = "{$artist}|{$url}|{$email}|{$password}|{$label_id}";
        insert('mydna_musicplayer',$tables,$values);
        
        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
    }
    
    function do_add_label()
    {
        if( $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
        {
            header("HTTP/1.0 403 Forbidden");
            exit();
        }
        
        $name = $_REQUEST['name'];
        $email = $_REQUEST['email'];
        $password = md5($_REQUEST['password']);
        
        $tables = "name|email|password";
		$values = "{$name}|{$email}|{$password}";
        insert('myartist_users',$tables,$values);
        
        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
    }

?>