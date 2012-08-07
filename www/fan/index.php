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
    
    $sql = "SELECT * FROM fans WHERE id='$fan_id'";
    $fan = mf(mq($sql));
    
    $fan_email = $fan['email'];
    
    $fan_files = array();
    $fan_files_html = "";
    
    $sql = "SELECT fan_files.id AS id, product_files.upload_filename AS upload_filename ";
    $sql .= " FROM fan_files ";
    $sql .= " JOIN product_files ON fan_files.product_file_id = product_files.id ";
    $sql .= " WHERE fan_files.fan_id='$fan_id' ";
    $files_q = mq($sql);
    while( $file = mf($files_q) )
    {
        $file_id = $file['id'];
        $file_name = $file['upload_filename'];
    
        $item = array("id" => $file_id,
                      "name" => $file_name,
                      );
        $fan_files[] = $item;
        
        $encoded_file_name = urlencode($file_name);
        
        $url = "/fan/downloads/$encoded_file_name?id=$file_id&attachment=true";
        
        $html = "";
        $html .= "<div class='file'>";
        $html .= " <div class='name'>$file_name</div>";
        $html .= " <div class='download'><a href='$url'>download</a></div>";
        $html .= "</div>";
        $fan_files_html .= $html;
    }
    
    $fan_files_json = json_encode($file_files);
    
    $order_list = array();
    $order_list_html = "";
    
    $sql = "SELECT orders.id AS id ";
    $sql .= " ,mydna_musicplayer.artist AS artist_name ";
    $sql .= " ,mydna_musicplayer.logo AS artist_logo ";
    $sql .= " ,orders.order_date AS order_date ";
    $sql .= " FROM orders ";
    $sql .= " JOIN mydna_musicplayer ON orders.artist_id = mydna_musicplayer.id ";
    $sql .= " WHERE customer_email='$fan_email' ";
    $sql .= " ORDER BY orders.order_date DESC";
    $orders_q = mq($sql);
    while( $order = mf($orders_q) )
    {
        $order_id = $order['id'];
        $artist_name = $order['artist_name'];
        $artist_logo = $order['artist_logo'];
        $order_date = $order['order_date'];
        
        $artist_logo_url = "/artists/$artist_logo";
        
        $html = "";
        $html .= "<div class='order_item'>";
        $html .= " <div class='logo'><img src='$artist_logo_url'/></div>";
        $html .= " <div class='description'>";
        $html .= "  <div class='artist'>$artist_name</div>";
        $html .= "  <div class='detail'>Order placed: $order_date</div>";
        $html .= " </div>";
        $html .= " <div class='status'>";
        $html .= "  <div class='status_button'>";
        $html .= "   <div class='icon'></div>";
        $html .= "   <div class='label'>Status</div>";
        $html .= "  </div>";
        $html .= " </div>";
        $html .= "</div>";
        $order_list_html .= $html;
    }
        
    include_once 'templates/fan_index.html';
?>