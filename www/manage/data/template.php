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
        $method = strtoupper($_REQUEST['method']);
    
    if( $method == 'POST' )
        do_POST();
    else
        print "Bad method\n";
    
    exit();
    
    
    function do_POST()
    {
        $artist_id = $_REQUEST['artist_id'];
        $template_id = 0;
        if( isset($_REQUEST['id']) )
        {
            $template_id = $_REQUEST['id'];
        }
        $name = $_REQUEST['name'];
        $params_json = $_REQUEST['params_json'];
        
        $values = array(
                        "name" => $name,
                        "params_json" => $params_json
                        );
        
        if( $template_id ) 
        {
            mysql_update('templates',$values,"id",$template_id);
        } 
        else 
        {
            $type = $_REQUEST['type'];
            
            $values['artist_id'] = $artist_id;
            $values['type'] = $type;
            
            mysql_insert('templates',$values);
            $template_id = mysql_insert_id();
        }
        
        $template = mf(mq("SELECT * FROM templates WHERE id='$template_id'"));
        
        $ret['success'] = "1";
        $ret['posted_values'] = $_REQUEST;
        $ret['template'] = $template;
        
        echo json_encode($ret);
    }
    
?>