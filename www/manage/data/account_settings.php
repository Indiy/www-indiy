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
        header("HTTP/1.1 403 Not Authorized");
        exit();
    }
    
    if( $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
	{
		header("HTTP/1.1 403 Not Authorized");
		exit();
	}
    session_write_close();

    
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
        $account_type = $_REQUEST['account_type'];
        $player_template = $_REQUEST['player_template'];
        
        $updates = array(
                         "account_type" => $account_type,
                         "player_template" => $player_template,
                         );
        
        mysql_update('mydna_musicplayer',$updates,'id',$artist_id);
        
        $postedValues['artist_data'] = get_artist_data($artist_id);
        echo json_encode($postedValues);
        exit();
    }
?>

