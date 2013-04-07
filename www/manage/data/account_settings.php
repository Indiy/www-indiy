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
        $template_id = $_REQUEST['template_id'];
        if( $template_id == 'null' )
        {
            $template_id = NULL;
        }
        
        $aws_cloudfront_enable = $_REQUEST['aws_cloudfront_enable'] == "1";
        
        $artist_data = mf(mq("SELECT extra_json FROM mydna_musicplayer WHERE id='$artist_id'"));
        $extra_json = $artist_data['extra_json'];
        $extra = json_decode($extra_json,TRUE);
        if( !isset($extra['aws']) )
        {
            $extra['aws'] = array(
                                  'cloudfront_enable' => FALSE,
                                  'published' => FALSE,
                                  );
        }
        $extra['aws']['cloudfront_enable'] = $aws_cloudfront_enable;

        $extra_json = json_encode($extra);
        
        $updates = array(
                         "account_type" => $account_type,
                         "template_id" => $template_id,
                         "extra_json" => $extra_json,
                         "last_update" => mysql_now(),
                         );
        
        mysql_update('mydna_musicplayer',$updates,'id',$artist_id,TRUE);
        print "mysql_error: " . mysql_error() . "\n";
        
        $ret['artist_data'] = get_artist_data($artist_id);
        $ret['post_values'] = $_REQUEST;
        $ret['posted_template_id'] = $template_id;
        
        echo json_encode($ret);
        exit();
    }
?>

