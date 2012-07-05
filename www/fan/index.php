<?php
    
    $browser = get_browser(null,TRUE);
    if( $browser['browser'] == 'IE' && $browser['majorver'] < 8 )
    {
        include('unsupported_browser.php');
        die();
    }
    
    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    
    $fan_site_url = fan_site_url();

    $artist_url = '';
    $http_host = $_SERVER["HTTP_HOST"];
    if( "http://" . $http_host != trueSiteUrl() )
    {
        header("Location: $fan_site_url");
        die();
    }
    
    if( !isset($_SESSION['fan_id']) )
    {
        
        header("Location: $fan_site_url/login.php");
        die();
    }
    
    $fan_id = $_SESSION['fan_id'];
    
    $fan_files = array();
    $fan_files_html = "";
    
    $sql = "SELECT * FROM fan_files ";
    $sql .= " JOIN product_files ON fan_files.product_file_id = product_files.id ";
    $sql .= " WHERE fan_id='$fan_id' ";
    $files_q = mq($sql);
    while( $file = mf($files_q) )
    {
        $file_id = $file['id'];
        $file_name = $file['upload_filename'];
        $product_file_id = $file['product_file_id'];
        
    
        $item = array("id" => $file_id,
                      "name" => $file_name,
                      );
        $fan_files[] = $item;
        
        $url = "/fan/downloads/$file_name?id=$file_id";
        
        $html = "";
        $html .= "<div class='file'>";
        $html .= " <div class='name'>$file_name</div>";
        $html .= " <div class='download'><a href='$url'>download</a></div>";
        $html .= "</div>";
        $fan_files_html .= $html;
    }
    
    $fan_files_json = json_encode($file_files);
    
    include_once 'templates/fan_index.html';
?>