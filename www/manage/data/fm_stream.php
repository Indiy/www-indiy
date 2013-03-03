<?php

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    
    define("PATH_TO_ROOT","../../");
    
    require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
    
    if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
    session_write_close();
    
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
            mysql_update('fm_streams',$values,"id",$id);
            ++$count;
        }
        
        $ret = array("success" => 1);
        echo json_encode($ret);
        exit();
    }
    
    function do_POST()
    {
        $artist_id = $_POST['artist_id'];
        $name = $_POST['name'];
        
        $values = array("artist_id" => $artist_id,
                        "name" => $name,
                        );

        if( isset($_POST['fm_stream_id']) )
        {
            $id = $_POST['fm_stream_id'];
            mysql_update('fm_streams',$values,'id',$id);
        }
        else
        {
            mysql_insert('fm_streams',$values);
            $id = mysql_insert_id();
        }

        $stream = mf(mq("SELECT * FROM fm_streams WHERE id='$id'"));
        
        $ret = array("success" => 1,
                     "stream" => $stream,
                     );
                
        echo json_encode($ret);
        exit();
    }


?>