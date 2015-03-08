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
    else if( $method == 'ORDER' )
        do_ORDER();
    else
        print "Bad method\n";
    
    exit();
    
    function do_ORDER()
    {
        $array = $_REQUEST['arrayorder'];
        $count = 1;
        foreach( $array as $id )
        {
            $values = array("order" => $count);
            mysql_update('mydna_musicplayer_content',$values,"id",$id);
            ++$count;
        }
        
        $ret = array("success" => 1);
        echo json_encode($ret);
        exit();
    }
    
    function get_data($id)
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer_content WHERE id='$id'"));
        
        array_walk($row,cleanup_row_element);
        
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = static_file_url("/manage/images/photo_video_01.jpg");
        
        return $row;
    }
    

    function do_POST()
    {
        $artist_id = $_POST["artistid"];
        $tab_id = $_POST["id"];
    
        $content_name = $_POST["name"];
        $content_video = $_POST["video"];
        $content_body = $_POST["body"];
        $item_datetime = $_POST["item_datetime"];

        $image_file = $_POST['image_drop'];
        
        $tables = "artistid|name|image|video|body|item_datetime";
        $values = "$artist_id|$content_name|$image_file|$content_video|$content_body|$item_datetime";
        
        if( $tab_id != "" )
        {
            update("mydna_musicplayer_content",$tables,$values,"id",$tab_id);
        } 
        else 
        {
            insert("mydna_musicplayer_content",$tables,$values);
            $tab_id = mysql_insert_id();
        }
        $postedValues['imageSource'] = $image_file;
        $postedValues['success'] = "1";
        $postedValues['postedValues'] = $_REQUEST;
        
        if( $_POST['ajax'] )
        {
            $postedValues['tab_data'] = get_data($tab_id);
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