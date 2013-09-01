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
        mysql_update('playlist_items',$values,"id",$id);
        ++$count;
    }
    
    $ret = array("success" => 1);
    echo json_encode($ret);
    exit();
}

function do_POST()
{
    $playlist_id = $_REQUEST['playlist_id'];

    $values = array();
    $values['playlist_id'] = $playlist_id;
    $values['name'] = $_REQUEST['name'];
    $values['image_id'] = $_REQUEST['image_id'];
    $values['bg_style'] = $_REQUEST['bg_style'];
    $values['bg_color'] = $_REQUEST['bg_color'];
    $values['media_id'] = $_REQUEST['media_id'];
    $values['tags'] = "";
    
    if( isset($_REQUEST['playlist_item_id']) )
    {
        $playlist_item_id = $_REQUEST['playlist_item_id'];
        mysql_update('playlist_items',$values,'playlist_item_id',$playlist_item_id);
    } 
    else 
    {
        mysql_insert('playlist_items',$values);
        $playlist_item_id = mysql_insert_id();
    }
    
    $ret = array();
    $ret['values'] = $values;
    $ret['playlist_item_id'] = $playlist_item_id;
    $ret['success'] = 1;
    
    $ret['postedValues'] = $_REQUEST;
    
    echo json_encode($ret);
    exit();
}
function do_DELETE()
{
    $playlist_item_id = $_REQUEST['playlist_item_id'];
    mf("DELETE FROM playlist_items WHERE playlist_item_id='$playlist_item_id'");

    $ret = array('success' => 1);
    echo json_encode($ret);
    exit();
}

?>