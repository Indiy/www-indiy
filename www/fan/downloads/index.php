<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';

    $fan_id = $_SESSION['fan_id'];
    session_write_close();
    
    $file_id = $_REQUEST['id'];
    
    $sql = "SELECT * FROM fan_files ";
    $sql .= " JOIN product_files ON fan_files.product_file_id = product_files.id ";
    $sql .= " WHERE fan_files.fan_id='$fan_id' AND fan_files.id='$file_id' ";
    
    $file = mf(mq($sql);
    
    if( !$file )
    {
        header("HTTP/1.0 404 Not Found");
        die();
    }
    
    $mime_type = mime_content_type($file['upload_filename']);
    
    header("Content-Type: $mime_type");
    
    $filename = $file['filename'];
    $path = "../../artists/digital_downloads/$filename";
    
    $real_path = realpath($path);

    header("X-Sendfile: $real_path");
    die();

?>