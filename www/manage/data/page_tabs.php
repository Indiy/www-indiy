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
        mysql_update('page_tabs',$values,'page_tab_id',$id);
        ++$count;
    }
    
    $ret = array("success" => 1);
    echo json_encode($ret);
    exit();
}

function get_item($page_tab_id)
{
    $sql = "SELECT page_tabs.*, mydna_musicplayer_content.name AS tab_name ";
    $sql .= " FROM page_tabs ";
    $sql .= " LEFT JOIN mydna_musicplayer_content ON page_tabs.tab_id = mydna_musicplayer_content.id ";
    $sql .= " WHERE page_tab_id = '$page_tab_id' ";
    $q = mq($sql);
    $row = mf($q);
    return $row;
}

function do_POST()
{
    $values = array();
    $values['page_id'] = $_REQUEST['page_id'];
    $values['tab_id'] = $_REQUEST['tab_id'];
    
    mysql_insert('page_tabs',$values);
    $page_tab_id = mysql_insert_id();
    
    $ret = array();
    $ret['page_tab'] = get_item($page_tab_id);
    $ret['values'] = $values;
    $ret['page_tab_id'] = $page_tab_id;
    $ret['success'] = 1;
    $ret['postedValues'] = $_REQUEST;
    echo json_encode($ret);
    exit();
}
function do_DELETE()
{
    $page_tab_id = $_REQUEST['page_tab_id'];
    mq("DELETE FROM page_tabs WHERE page_tab_id='$page_tab_id'");

    $ret = array('success' => 1);
    echo json_encode($ret);
    exit();
}

?>