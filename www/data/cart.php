<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    require_once('../includes/functions.php');
    require_once('../includes/config.php');

    session_start();
    if( $_SESSION['cart_id'] == '' )
        $_SESSION['cart_id'] = rand(1111111,9999999);
    session_write_close();

    $cart_id = $_SESSION['cart_id'];
    $artist_id = $_REQUEST['artist_id'];
    
    if( isset($_REQUEST['method']) )
        $method = strtoupper($_REQUEST['method']);
    else
        $method = $_SERVER['REQUEST_METHOD'];
    
    if( $method == 'GET' )
    {
        send_store_cart($artist_id,$cart_id);
        exit();
    }
    elseif( $method == 'POST' )
    {
        $artist_cart_id = "$artist_id:$cart_id";
    
        if( isset($_REQUEST['cart_item_id']) )
        {
            $cart_item_id = $_REQUEST['cart_item_id'];
            $quantity = $_REQUEST['quantity'];
            
            $values = array('quantity' => $quantity);
            mysql_update('cart_items',$values,'id',$cart_item_id);
        }
        else
        {
            $product_id = $_REQUEST['product_id'];
            $quantity = $_REQUEST['quantity'];

            $values = array(
                            'cart_id' => $artist_cart_id,
                            'product_id' => $product_id,
                            'quantity' => $quantity,
                            );
            
            if( isset($_REQUEST['size']) )
            {
                $values['size'] = $_REQUEST['size'];
            }
            if( isset($_REQUEST['color']) )
            {
                $values['color'] = $_REQUEST['color'];
            }
            
            mysql_insert('cart_items',$values);
        }
        send_store_cart($artist_id,$cart_id);
        exit();
    }
    elseif( $method == 'DELETE' )
    {
        $artist_cart_id = "$artist_id:$cart_id";

        parse_str(file_get_contents('php://input'), $params);
        $cart_item_id = $params['cart_item_id'];
        
        $sql = "DELETE FROM cart_items WHERE id='$cart_item_id' AND cart_id='$artist_cart_id'";
        mq($sql);
        send_store_cart($artist_id,$cart_id);
        exit();
    }
    else
    {
        print "{ error: \"unknown method\" }";
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