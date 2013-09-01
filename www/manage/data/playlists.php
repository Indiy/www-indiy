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

    $method = $_SERVER['REQUEST_METHOD'];
    if( isset($_REQUEST['method']) )
    {
        $method = strtoupper($_REQUEST['method']);
    }
    
    if( $method == 'POST' )
    {
        do_POST();
    }
    else if( $method == 'DELETE' )
    {
        do_DELETE();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
function do_POST()
{
    $values = array();
    $values['artist_id'] = $_REQUEST['artist_id'];
    $values['name'] = $_REQUEST['name'];
    $values['type'] = $_REQUEST['type'];
    
    if( isset($_REQUEST['playlist_id']) )
    {
        $playlist_id = $_REQUEST['playlist_id'];
        mysql_update("playlists",$values,'playlist_id',$playlist_id);
    }
    else
    {
        mysql_insert("playlists",$values);
        $playlist_id = mysql_insert_id();
    }

    $ret = array();
    $ret['values'] = $values;
    $ret['id'] = $song_id;
    $ret['success'] = "1";
    $ret['postedValues'] = $_REQUEST;

    $ret['playlist_id'] = $playlist_id;
    echo json_encode($ret);
    exit();
}
function do_DELETE()
{
    $playlist_id = $_REQUEST['playlist_id'];
    mq("DELETE FROM playlists WHERE playlist_id='$playlist_id'");

    $ret = array('success' => 1);
    echo json_encode($ret);
    exit();
}

?>