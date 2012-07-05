<?

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    include('../includes/functions.php');   
    include('../includes/config.php');

    if( $_SESSION['cart_id'] == '' )	
        $_SESSION['cart_id'] = rand(1111111,9999999);
    session_write_close();

    $cart_id = $_SESSION['cart_id'];
    $artist_id = $_REQUEST['artist_id'];
    
    if( $_SERVER['REQUEST_METHOD'] == 'GET' )
    {
        send_store_cart($artist_id,$cart_id);
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

        send_store_cart($artist_id,$cart_id);
        exit();
    }
    elseif( $_SERVER['REQUEST_METHOD'] == 'DELETE' )
    {
        parse_str(file_get_contents('php://input'), $params);
        $cart_item_id = $params['cart_item_id'];
        mq("DELETE FROM cart_items WHERE id='$cart_item_id' AND cart_id='$cart_id'");
        
        send_store_cart($artist_id,$cart_id);
        exit();
    }
    
    function send_store_cart($artist_id,$cart_id)
    {
        $cart = store_get_cart($artist_id,$cart_id);

        $json = json_encode($cart);
        if( isset($_REQUEST['callback']) )
        {
            $callback = $_REQUEST['callback'];
            echo "$callback($json);";
        }
        else
        {
            echo $json;
        }
    }

?>

