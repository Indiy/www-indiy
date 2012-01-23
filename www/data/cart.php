<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('../includes/functions.php');   
    include('../includes/config.php');

    if( $_SESSION['cart'] == '' )	
        $_SESSION['cart'] = rand(1111111,9999999);
    session_write_close();

    $cart_userid = $_SESSION['cart'];
    
    function get_cart()
    {
        $cart_list = array();
        $q = mq("SELECT * FROM `mydna_musicplayer_ecommerce_cart` WHERE `userid`='$cart_userid' ORDER BY `id` ASC");
        while($cart = mf($q)) 
        {
            $id = $cart['id'];
            $product_id = $cart['productid'];
            $price = $cart['price'];
            $name = $cart['name'];
            $image = productImage($product_id);
            $shipping = 0.0;
            
            $item = array("id" => $id,
                          "product_id" => $product_id,
                          "price" => $price,
                          "name" => $name,
                          "image" => $image,
                          "shipping" => $shipping,
                          );
            $cart_list[] = $item;
        }
        return $cart_list;
    }


    if( $_SERVER['REQUEST_METHOD'] == 'GET' )
    {
        echo json_encode(get_cart());
        exit();
    }
    elseif( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
        $product_id = $_POST['product_id'];
        
        $row = mf(mq("SELECT * FROM `mydna_musicplayer_ecommerce_products` WHERE `id`='$product_id' LIMIT 1"));
        
        $product_name = $row["name"];
        $product_price = $row["price"];

        if( $_POST["size"] != "" ) 
            $product_name .= " - ".$_POST["size"]; 
        if( $_POST["color"] != "" ) 
            $product_name .= " - ".$_POST["color"]; 
        
        $values = array("userid" => $cart_userid,
                        "productid" => $product_id,
                        "price" => $product_price,
                        "name" => $product_name,
                        );
        mysql_insert('mydna_musicplayer_ecommerce_cart',$values);
        echo json_encode(get_cart());
        exit();
    }
    elseif( $_SERVER['REQUEST_METHOD'] == 'DELETE' )
    {
        
    }

?>

