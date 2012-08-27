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
    if( $_REQUEST['method'] )
        $method = strtoupper($_REQUEST['method']);
    
    if( $method == 'POST' )
        do_POST();
    else if( $method == 'DELETE' )
        do_DELETE();
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
        $artist_id = $_REQUEST['artist_id'];
        
        $ret = handle_file_upload($artist_id,$_FILES["file"]);
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
    function do_DELETE()
    {
        $artist_id = $_REQUEST['artist_id'];
        $file_id = $_REQUEST['file_id'];
        
        $sql = "DELETE FROM artist_files WHERE id = '$file_id' AND artist_id = '$artist_id'";
        $ret = mq($sql);
        if( $ret )
        {
            $result = array("success" => 1,"sql" => $sql);
        }
        else
        {
            $result = array("failure" => 1,"sql" => $sql);
        }
        echo json_encode($result);
        exit();
    }
    
    function handle_file_upload($artist_id,$file)
    {
        $ret = array("id" => FALSE);
        
        if(!empty($file["name"]))
        {
            $src_file = $file["tmp_name"];
			if( is_uploaded_file($src_file) )
            {
                $upload_filename = basename($file["name"]);
                
                $path_parts = pathinfo($upload_filename);
                $extension = strtolower($path_parts['extension']);
                
                $type = get_file_type($upload_filename);
                
                $hash = hash_file("md5",$src_file);
                $save_filename = "{$artist_id}_$hash.$extension";
                
                if( PATH_TO_ROOT )
                    $dst_file = PATH_TO_ROOT . "artists/files/$save_filename";
                else
                    $dst_file = "../../artists/files/$save_filename";
                
                $sql = "SELECT * FROM artist_files WHERE artist_id='$artist_id' AND filename = '$save_filename'";
                $existing_file = mf(mq($sql));
                if( $existing_file )
                {
                    if( $existing_file['upload_filename'] )
                    {
                        $ret['upload_error'] = "File already uploaded.";
                    }
                    else
                    {
                        $id = $existing_file['id'];
                        $ret['id'] = $id;
                        $ret['file'] = $save_filename;
                        $values = array("upload_filename" => $upload_filename);
                        mysql_update('artist_files',$values,'id',$id);
                    }
                }
                else
                {
                    @move_uploaded_file($src_file, $dst_file);
                    
                    $values = array("artist_id" => $artist_id,
                                    "filename" => $save_filename,
                                    "upload_filename" => $upload_filename,
                                    "type" => $type);
                    
                    mysql_insert("artist_files",$values);
                    $ret['id'] = mysql_insert_id();
                    $ret['file'] = $save_filename;
                }
			}
		}
        return $ret;
    }
    
?>
