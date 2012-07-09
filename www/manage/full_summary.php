<?php
  
    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	
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

    print "sql: $sql\n";

    $order_q = mq($sql);
    while( $order = mf($order_q) )
    {
        $artist_id = $order['artist_id'];
        
        $artist_name = $order['artist_name'];
        $order_count = $order['order_count'];
        
        $charge_total = floatval($order['charge_total']);
        $artist_total = floatval($order['artist_total']);
        
        print "artist_id: $artist_id\n";
        print "arist_name: $artist_name\n";
        print "order_count: $order_count\n";
        print "charge_total: $charge_total\n";
        print "artist_total: $artist_total\n";
        print "\n";
        print "=======================\n\n";
    }

?>