<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    
    session_start();
    session_write_close();
    if( $_SESSION['sess_userId'] == "" )
    {
        header("Location: /index.php");
        exit();
    }
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
        do_POST();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
    
    function do_POST()
    {
        $artist_id = $_REQUEST['artist_id'];
        $paypal_email = $_REQUEST['paypal_email'];
        
        $tables = "userid|paypal";
		$values = "{$artist_id}|{$paypal_email}";
        $row = mf(mq("SELECT * FROM mydna_musicplayer_ecommerce WHERE userid = '$artist_id'"));
        if( $row )
            update('mydna_musicplayer_ecommerce',$tables,$values,"userid",$artist_id);
        else
            insert('mydna_musicplayer_ecommerce',$tables,$values);
        
        $postedValues['success'] = "1";
        $postedValues['postedValues'] = $_REQUEST;
        $postedValues['artist_data'] = get_artist_data($artist_id);
        echo json_encode($postedValues);
        exit();
    }
?>

