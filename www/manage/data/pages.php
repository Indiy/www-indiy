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
    $values['uri'] = $_REQUEST['uri'];
    $values['template_id'] = $_REQUEST['template_id'];
    $values['favicon_id'] = $_REQUEST['favicon_id'];
    
    if( isset($_REQUEST['page_id']) )
    {
        $page_id = $_REQUEST['page_id'];
        mysql_update("pages",$values,'page_id',$page_id);
    }
    else
    {
        mysql_insert("pages",$values);
        $page_id = mysql_insert_id();
        
        if( !$page_id )
        {
            header('HTTP/1.1 407 Conflict');
            $ret = array();
            $ret['postedValues'] = $_REQUEST;
            $ret['success'] = 0;
            echo json_encode($ret);
            exit();
        }
    }

    $ret = array();
    $ret['values'] = $values;
    $ret['success'] = "1";
    $ret['postedValues'] = $_REQUEST;
    $ret['page_id'] = $page_id;
    echo json_encode($ret);
    exit();
}
function do_DELETE()
{
    $page_id = $_REQUEST['page_id'];
    $sql = "DELETE FROM pages WHERE page_id='$page_id'";
    mq($sql);

    $ret = array();
    $ret['postedValues'] = $_REQUEST;
    $ret['success'] = 1;
    $ret['sql'] = $sql;
    echo json_encode($ret);
    exit();
}

?>