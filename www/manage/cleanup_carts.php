<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
    
    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli')
	{
		header("Location: /index.php");
		exit();
	}

    echo "<html><body><pre>\n";
    
    $sql = "DELETE FROM  `mydna_musicplayer_ecommerce_cart` "
        . "WHERE `date` < DATE_SUB( NOW() , INTERVAL 1 DAY ) ";
        
    $ret = mq($sql);
    if( $ret )
    {
        $num = mysql_affected_rows();
        echo "Deleted $num rows.\n";
    }
    else
    {
        echo "Delete failed.\n";
    }
    
?>
