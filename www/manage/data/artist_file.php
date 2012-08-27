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
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
        do_POST();
    else
        print "Bad method\n";
    
    exit();
    
    function get_file_data($file_id)
    {
        $row = mf(mq("SELECT * FROM artist_files WHERE id='$file_id'"));
        
        array_walk($row,cleanup_row_element);
        
        return $row;
    }
    
    function do_POST()
    {
        $artist_id = $_POST['artist_id'];
        
        $ret = artist_file_upload($artist_id,$_FILES["file"],FALSE);
        if( $ret['id'] )
        {
            $postedValues['success'] = "1";
            $postedValues['postedValues'] = $_REQUEST;
            $postedValues['file'] = get_file_data($ret['id']);
        }
        else
        {
            if( $ret['upload_error'] )
                $postedValues['upload_error'] = $ret['upload_error'];
            else
                $postedValues['upload_error'] = TRUE;
            $postedValues['postedValues'] = $_REQUEST;
        }
        
        if( $_POST['ajax'] )
        {
            echo json_encode($postedValues);
            exit();
        }
        else
        {
            header("Location: /manage/artist_management.php?userId=$artist_id");
            exit();
        }
    }
    
?>
