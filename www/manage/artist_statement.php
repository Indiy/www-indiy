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
    $SUPER_ADMIN = FALSE;
    if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' )
        $SUPER_ADMIN = TRUE;
    
    $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$artist_id'"));
    
    $artist_invoices = array();

    $invoice_q = mq("SELECT * FROM artist_invoices WHERE artist_id='$artist_id' ORDER BY invoice_date DESC");
    while( $invoice = mf($invoice_q) )
    {
        $artist_invoice_id = $invoice['id'];

        $orders = array();
        $sql = "SELECT * FROM orders WHERE artist_invoice_id='$artist_invoice_id'";
        //print "$sql\n";
        $order_q = mq($sql);
        while( $order = mf($order_q) )
        {
            $orders[] = $order;
        }
        $invoice['orders'] = $orders;
        
        $invoice['id'] = intval($invoice['id']);
        $invoice['amount'] = floatval($invoice['amount']);
        $invoice['paid_amount'] = floatval($invoice['paid_amount']);

        $invoice_ts = strtotime($invoice['invoice_date']);
        $invoice['invoice_date'] = date("F Y",$invoice_ts);
        
        $artist_invoices[] = $invoice;
    }

    $artist_invoices_json = json_encode($artist_invoices);

    $artist_name = $artist['artist'];
    $artist_email = $artist['email'];

    $artist_edit_url = "/manage/artist_management.php?userId=$artist_id";
    
    $include_order = TRUE;
    $include_editor = FALSE;
    include_once "templates/artist_statement.html";

?>