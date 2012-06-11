<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    
    $cart_list = store_get_cart();
    $cart_list_json = json_encode($cart_list);

    include_once 'templates/cart.html';
?>