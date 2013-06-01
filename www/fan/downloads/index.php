<?php

    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';

    session_start();
    session_write_close();

    $fan_id = $_SESSION['fan_id'];
    if( !$fan_id )
    {
        header("Location: /fan/");
        die();
    }
    
    $file_id = $_REQUEST['id'];
    $as_attachment = $_REQUEST['attachment'];
    
    $sql = "SELECT * FROM fan_files ";
    $sql .= " JOIN product_files ON fan_files.product_file_id = product_files.id ";
    $sql .= " WHERE fan_files.fan_id='$fan_id' AND fan_files.id='$file_id' ";
    
    $file = mf(mq($sql));
    
    if( !$file )
    {
        header("HTTP/1.0 404 Not Found");
        include_once "error404.php";
        die();
    }
    
    $mime_type = mime_content_type($file['upload_filename']);
    
    $upload_filename = $file['upload_filename'];
    
    header("Content-Type: $mime_type");
    
    $filename = $file['filename'];
    $file_url = artist_file_url($filename);
    
    //$real_path = realpath($path);

    //header("X-Sendfile: $real_path");
    
    //$length = filesize($real_path);
    //header("Content-Length: $length");
    
    if( $as_attachment )
        header("Content-Disposition: attachment; filename=\"$upload_filename\"");
    
    readfile($file_url);
    
    die();

?>