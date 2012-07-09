<?php
    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
	$artist_id = $_REQUEST['artist_id'];
    if( !$artist_id )
    {
        if( $_SESSION['sess_userType'] == 'ARTIST' )
        {
            $artist_id = $_SESSION['sess_userId'];
        }
        else
        {
            header("Location: dashboard.php");
            exit();
        }
    }

    $pending_shipment_orders = array();
    $shipped_orders = array();
    
    $order_q = mq("SELECT * FROM orders WHERE artist_id='$artist_id'");
    while( $order = mf($order_q) )
    {
        $id = $order['id'];
        
        $state = $order['state'];
        
        $customer_name = $order['customer_name'];
        $customer_email = $order['customer_email'];
        
        $order_date = $order['order_date'];
        
        $charge_amount = floatval($order['charge_amount']);
        $to_artist_amount = floatval($order['to_artist_amount']);

        $order = array("id" => $id,
                       "state" => $state,
                       "order_date" => $order_date,
                       "customer_name" => $customer_name,
                       "customer_email" => $customer_email,
                       "charge_amount" => $charge_amount,
                       "to_artist_amount" => $to_artist_amount,
                       );
        
        if( $state == 'PENDING_CONFIRM' )
        {
            
        }
        else if( $state == 'CANCELED' )
        {
            
        }
        else if( $state == 'ABANDONED' )
        {
            
        }
        else if( $state == 'PENDING_SHIPMENT' )
        {
            $pending_shipment_orders[] = $order;
        }
        else if( $state == 'SHIPPED' 
                || $state == 'CLOSED'
                )
        {
            $shipped_orders[] = $order;
        }
    }
    
    $include_order = FALSE;
    $include_editor = FALSE;
    
    $pending_shipment_orders_json = json_encode($pending_shipment_orders);
    $shipped_orders_json = json_encode($shipped_orders);
    
    include_once "templates/artist_settlement.html";

?>