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

    if( $_SERVER['REQUEST_METHOD'] == 'DELETE' )
    {
       parse_str(file_get_contents('php://input'), $_POST);
       $_REQUEST = array_merge($_POST,$_GET);
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
    else if( $method == 'ORDER' )
    {
        do_ORDER();
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
    
function do_ORDER()
{
    $array = $_REQUEST['arrayorder'];
    $count = 1;
    foreach( $array as $id )
    {
        $values = array("order" => $count);
        mysql_update('page_playlists',$values,'page_playlist_id',$id);
        ++$count;
    }
    
    $ret = array("success" => 1);
    echo json_encode($ret);
    exit();
}

function get_item($page_playlist_id)
{
    $sql = "SELECT page_playlists.*, playlists.name AS playlist_name ";
    $sql .= " FROM page_playlists ";
    $sql .= " LEFT JOIN playlists ON playlists.playlist_id = page_playlists.playlist_id ";
    $sql .= " WHERE page_playlist_id = '$page_playlist_id' ";
    $q = mq($sql);
    $row = mf($q);
    return $row;
}

function do_POST()
{
    $values = array();
    $values['playlist_id'] = $_REQUEST['playlist_id'];
    $values['page_id'] = $_REQUEST['page_id'];
    
    mysql_insert('page_playlists',$values);
    $page_playlist_id = mysql_insert_id();
    
    $ret = array();
    $ret['page_playlist'] = get_item($page_playlist_id);
    $ret['values'] = $values;
    $ret['page_playlist_id'] = $page_playlist_id;
    $ret['success'] = 1;
    $ret['postedValues'] = $_REQUEST;
    echo json_encode($ret);
    exit();
}
function do_DELETE()
{
    $page_playlist_id = $_REQUEST['page_playlist_id'];
    mq("DELETE FROM page_playlists WHERE page_playlist_id='$page_playlist_id'");

    $ret = array('success' => 1);
    echo json_encode($ret);
    exit();
}

?>