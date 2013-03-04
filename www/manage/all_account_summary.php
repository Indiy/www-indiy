<?php
  
    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
    if( $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
    {
		header("Location: /index.php");
		exit();
    }

    $sql = "";
    $sql .= "SELECT COUNT(*) AS order_count ";
    $sql .= " ,orders.artist_id AS artist_id ";
    $sql .= " ,SUM(charge_amount) AS charge_total ";
    $sql .= " ,SUM(to_artist_amount) AS artist_total ";
    $sql .= " ,mydna_musicplayer.artist AS artist_name ";
    $sql .= " FROM orders ";
    $sql .= " JOIN mydna_musicplayer ON orders.artist_id = mydna_musicplayer.id ";
    $sql .= " WHERE orders.state='PENDING_SHIPMENT' OR orders.state='SHIPPED' OR orders.state='CLOSED' ";
    $sql .= " GROUP BY orders.artist_id "; 

    $order_q = mq($sql);
    $artist_list = array();
    while( $order = mf($order_q) )
    {
        $artist_id = $order['artist_id'];
        
        $artist_name = $order['artist_name'];
        $order_count = floatval($order['order_count']);
        
        $charge_total = floatval($order['charge_total']);
        $artist_total = floatval($order['artist_total']);
        
        $artist = array("artist_id" => $artist_id,
                        "artist_name" => $artist_name,
                        "order_count" => $order_count,
                        "charge_total" => $charge_total,
                        "artist_total" => $artist_total,
                        );
                        
        $artist_list[] = $artist;
    }
    
    $artist_list_json = json_encode($artist_list);
    
    $artist_edit_url = "/manage/dashboard.php";
    
    $include_order = TRUE;
    $include_editor = FALSE;
    include_once "templates/all_account_summary.html";

?>