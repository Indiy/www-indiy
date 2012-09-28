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
    
    $artist_invoices = array();

    $invoice_q = mq("SELECT * FROM artist_invoices WHERE artist_id='$artist_id'");
    while( $invoice = mf($invoice_q) )
    {
        $artist_invoice_id = $invoice['artist_invoice_id'];

        $orders = array();
        $order_q = mq("SELECT * FROM orders WHERE artist_invoice_id='$artist_invoice_id'");
        while( $order = mf($order_q) )
        {
            $order['amount'] = floatval($order['amount']);
            $order['paid_amount'] = floatval($order['paid_amount']);
            $orders[] = $order;
        }
        $invoice['orders'] = $orders;
        
        $artist_invoices[] = $invoice;
    }

    $artist_invoices_json = json_encode($artist_invoices);

    $artist_edit_url = "/manage/artist_management.php?userId=$artist_id";
    
    $include_order = TRUE;
    $include_editor = FALSE;
    include_once "templates/artist_statement.html";

?>