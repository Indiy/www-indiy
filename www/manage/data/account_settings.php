<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
    require_once '../../includes/functions.php';
    
    if( $_SESSION['sess_userId'] == "" )
    {
        header("HTTP/1.1 403 Not Authorized");
        exit();
    }
    
    if( $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
	{
		header("HTTP/1.1 403 Not Authorized");
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
    
    
    function get_data($id)
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$id'"));
        
        if( $row['oauth_token'] && $row['oauth_secret'] && $row['twitter'] )
            $twitter = 'true';
        else
            $row['twitter'] = FALSE;
        if( $row['fb_access_token'] && $row['facebook'] )
            $facebook = 'true';
        else
            $row['facebook'] = FALSE;
        
        $store_check = mf(mq("SELECT * FROM mydna_musicplayer_ecommerce WHERE userid='$id' LIMIT 1"));
        $paypalEmail = $store_check["paypal"];
        $row['paypal_email'] = $paypalEmail;
        
        $logo = $row['logo'];
        $logo_path = "../artists/images/$logo";
        if( $row['logo'] )
            $row['logo_url'] = $logo_path;
        else
            $row['logo_url'] = 'images/NoPhoto.jpg';
        
        array_walk($row,cleanup_row_element);
        
        return $row;
    }
    
    function do_POST()
    {
        $artist_id = $_REQUEST['artist_id'];
        $account_type = $_REQUEST['account_type'];
        mysql_update('mydna_musicplayer',
                     array("account_type" => $account_type),
                     'id',$artist_id);
        
        $postedValues['artist_data'] = get_data($artist_id);
        echo json_encode($postedValues);
        exit();
    }
?>

