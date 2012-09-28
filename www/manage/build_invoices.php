<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
    
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    error_reporting(E_ALL);
    
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli' )
	{
		header("Location: /index.php");
		exit();
	}
    session_write_close();

    print "<html><body><pre>\n";

    
    $user = get_current_user();
    $fd = fopen("/tmp/build_invoices_$user.lock",'w+');
    if( !$fd )
    {
        print "failed to open file\n";
        die();
    }
    if( !flock($fd,LOCK_EX | LOCK_NB) )
    {
        fclose($fd);
        print "Failed to get lock, done!\n";
        die();
    }

    print "Got lock!\n\n";
    
    $ts = time();
    $until_time = mktime(0,0,0,date("n",$ts),1,date("Y",$ts));
    $until_date = date('Y-m-d H:i:s',$until_time);
    print "Until_time: $until_time, until_date: $until_date\n";
    
    $sql = "SELECT * FROM orders ";
    $sql .= " WHERE artist_invoice_id IS NULL AND order_date < '$until_date' ";
    $sql .= " AND state IN ( 'PENDING_SHIPMENT','SHIPPED','CLOSED' ) ";
    
    print "sql: $sql\n";
    
    $order_q = mq($sql);
    
    $artists = array();
    
    while( $order = mf($order_q) )
    {
        $order_date = $order['order_date'];
        $order_time = strtotime($order_date);
        $month = date("Y-m-01",$order_time);
        
        $artist_id = $order['artist_id'];
        
        if( !isset($artists[$artist_id]) )
        {
            $artists[$artist_id] = array();
        }
        
        if( !isset($artists[$artist_id][$month]) )
        {
            $artists[$artist_id][$month] = array();
        }
        $artists[$artist_id][$month][] = $order;
    }
    
    foreach( $artists as $artist_id => $months )
    {
        print "artist_id: $artist_id\n";
        
        foreach( $months as $month => $orders )
        {
            $total = 0.0;
            print "artist_id: $artist_id, month: $month\n";
            foreach( $orders as $order )
            {
                $to_artist_amount = $order['to_artist_amount'];
                $total += $to_artist_amount;
            }
            print "artist_id: $artist_id, month: $month, total: $total\n";
            
            $values = array("artist_id" => $artist_id,
                            "invoice_date" => $month,
                            "amount" => $total,
                            );
            
            mysql_insert('artist_invoices',$values);
            $invoice_id = mysql_insert_id();
            
            foreach( $orders as $order )
            {
                $order_id = $order['id'];
                $values = array("artist_invoice_id" => $invoice_id);
                mysql_update('orders',$values,'id',$order_id);
            }
        }
    }
    
    //var_dump($artists);

?>