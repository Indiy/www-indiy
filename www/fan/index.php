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
    
    $sql = "SELECT fan_files.id AS id";
    $sql .= " ,product_files.upload_filename AS upload_filename ";
    $sql .= " ,mydna_musicplayer_ecommerce_products.image AS product_image ";
    $sql .= " ,mydna_musicplayer_ecommerce_products.name AS product_name ";
    $sql .= " ,mydna_musicplayer_ecommerce_products.description AS product_decription ";
    $sql .= " FROM fan_files ";
    $sql .= " JOIN product_files ON fan_files.product_file_id = product_files.id ";
    $sql .= " JOIN mydna_musicplayer_ecommerce_products ON product_files.product_id = mydna_musicplayer_ecommerce_products.id ";
    $sql .= " WHERE fan_files.fan_id='$fan_id' ";
    $files_q = mq($sql);
    
    $i = 0;
    while( $file = mf($files_q) )
    {
        $file_id = $file['id'];
        $file_name = $file['upload_filename'];
        $product_name = $file['product_name'];
        $product_image = $file['product_image'];
        $product_description = $file['product_description'];

        $product_image_url = "/artists/products/$product_image";
    
        $item = array("id" => $file_id,
                      "name" => $file_name,
                      );
        $fan_files[] = $item;
        
        $encoded_file_name = urlencode($file_name);
        
        $odd = "";
        if( $i % 2 == 1 )
            $odd = " odd";
        
        $url = "/fan/downloads/$encoded_file_name?id=$file_id&attachment=true";
        
        $html = "";
        $html .= "<div class='file$odd'>";
        $html .= " <div class='image'><img src='$product_image_url'/></div>";
        $html .= " <div class='product_file'>";
        $html .= "  <div class='product_name'>$product_name</div>";
        $html .= "  <div class='file_name'>$file_name</div>";
        $html .= " </div>";
        $html .= " <div class='action'>";
        $html .= "  <a href='$url'>";
        $html .= "   <div class='download_button'>";
        $html .= "    <div class='icon'></div>";
        $html .= "    <div class='label'>Download</div>";
        $html .= "   </div>";
        $html .= "  </a>";
        $html .= " </div>";
        $html .= "</div>";
        $fan_files_html .= $html;
        
        $i++;
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
    
    $i = 0;
    while( $order = mf($orders_q) )
    {
        $order_id = $order['id'];
        $artist_name = $order['artist_name'];
        $artist_logo = $order['artist_logo'];
        $order_date = $order['order_date'];
        
        $artist_logo_url = "/artists/images/$artist_logo";
        
        $odd = "";
        if( $i % 2 == 1 )
            $odd = " odd";
        
        $html = "";
        $html .= "<div class='order_item$odd'>";
        $html .= " <div class='logo'><img src='$artist_logo_url'/></div>";
        $html .= " <div class='description'>";
        $html .= "  <div class='artist'>$artist_name</div>";
        $html .= "  <div class='detail'>Order placed: $order_date</div>";
        $html .= " </div>";
        $html .= " <div class='action'>";
        $html .= "  <a href='/order_status.php?order_id=$order_id'>";
        $html .= "   <div class='status_button'>";
        $html .= "    <div class='icon'></div>";
        $html .= "    <div class='label'>Status</div>";
        $html .= "   </div>";
        $html .= "  </a>";
        $html .= " </div>";
        $html .= "</div>";
        $order_list_html .= $html;
        
        $i++;
    }

    $love_list = array();
    $love_list_html = "";
    
    $sql = "SELECT love_list AS id ";
    $sql .= " ,mydna_musicplayer_audio.name AS song_name ";
    $sql .= " ,mydna_musicplayer_audio.image AS song_image ";
    $sql .= " ,photos.name AS photo_name ";
    $sql .= " ,photos.image AS photo_image ";
    $sql .= " ,mydna_musicplayer_video.name AS video_name ";
    $sql .= " ,mydna_musicplayer_video.image AS video_image ";
    $sql .= " FROM love_list ";
    $sql .= " JOIN mydna_musicplayer_audio ON mydna_musicplayer_audio.id = love_list.song_id ";
    $sql .= " JOIN photos ON photos.id = love_list.photo_id ";
    $sql .= " JOIN mydna_musicplayer_video ON mydna_musicplayer_video.id = love_list.video_id ";
    $sql .= " WHERE fan_id='$fan_id' ";
    $sql .= " ORDER BY id ASC";
    $love_q = mq($sql);
    
    print "sql: $sql\n";
    
    $i = 0;
    while( $love = mf($love_q) )
    {
        $order_id = $love['id'];
        
        
        $odd = "";
        if( $i % 2 == 1 )
            $odd = " odd";
        
        $html = "";
        $html .= "<div class='love_item$odd'>";
        $html .= "</div>";
        $love_list_html .= $html;
        
        $i++;
    }
        
    include_once 'templates/fan_index.html';
?>