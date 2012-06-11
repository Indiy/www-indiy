<?

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('../includes/functions.php');   
    include('../includes/config.php');

    if( $_SESSION['cart_id'] == '' )	
        $_SESSION['cart_id'] = rand(1111111,9999999);
    session_write_close();

    $cart_id = $_SESSION['cart_id'];
    
    function get_cart()
    {
        global $cart_id;
        $cart_list = array();
        $sql = "";
        $sql .= "SELECT cart_items.*";
        $sql .= " mydna_musicplayer_ecommerce_products.name,mydna_musicplayer_ecommerce_products.image,mydna_musicplayer_ecommerce_products.price,mydna_musicplayer_ecommerce_products.shipping";
        $sql .= " FROM cart_items";
        $sql .= " JOIN mydna_musicplayer_ecommerce_products ON cart_items.product_id = mydna_musicplayer_ecommerce_products.id";
        $sql .= " WHERE cart_id='$cart_id'";
        $sql .= " ORDER BY `id` ASC";
        $q = mq($sql);
        while($cart = mf($q)) 
        {
            $id = $cart['id'];
            $product_id = $cart['product_id'];
            $price = floatval($cart['price']);
            $name = $cart['name'];
            if(  $cart['image'] )
                $image = '/artists/images/' . $cart['image'];
            else
                $image = '/images/default_product_image.jpg';

            $shipping = floatval($cart['shipping']);
            $quantity = intval($cart['quantity']);
            
            $item = array("id" => $id,
                          "product_id" => $product_id,
                          "price" => $price,
                          "name" => $name,
                          "image" => $image,
                          "shipping" => $shipping,
                          "size" => $cart['size'],
                          "color" => $cart['color'],
                          "quantity" => $quantity,
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
        $cart_item_id = $_POST['cart_item_id'];
        $product_id = $_POST['product_id'];

        $size = $_POST["size"];
        $color = $_POST["color"];
        $quantity = $_POST["quantity"];
        
        $values = array("cart_id" => $cart_id,
                        "product_id" => $product_id,
                        "quantity" => $quantity,
                        "size" => $size,
                        "color" => $color,
                        );
        if( $cart_item_id )
            mysql_update('cart_items',$values,'id',$cart_item_id);
        else
            mysql_insert('cart_items',$values);
        echo json_encode(get_cart());
        exit();
    }
    elseif( $_SERVER['REQUEST_METHOD'] == 'DELETE' )
    {
        parse_str(file_get_contents('php://input'), $params);
        $cart_item_id = $params['cart_item_id'];
        mq("DELETE FROM cart_items WHERE id='$cart_item_id' AND cart_id='$cart_id'");
        echo json_encode(get_cart());
        exit();
    }

?>

