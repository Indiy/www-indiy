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
            mysql_update('photos',$values,"id",$id);
            ++$count;
        }
        
        $ret = array("success" => 1);
        echo json_encode($ret);
        exit();
    }
    
    function get_photo_data($photo_id)
    {
        $row = mf(mq("SELECT * FROM photos WHERE id='$photo_id'"));
        
        array_walk($row,cleanup_row_element);
        
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = "images/photo_video_01.jpg";

        return $row;
    }
    
    function do_POST()
    {
        $artist_id = $_POST['artist_id'];
        
        $photo_id = $_POST['id'];
        
        $name = $_POST["name"];
        $location = $_POST["location"];
        $bg_color = $_POST["bg_color"];
        $bg_style = $_POST["bg_style"];
        $tags = $_POST["tags"];
        $image_file = $_POST['image_drop'];

        $values = array("artist_id" => $artist_id,
                        "name" => $name,
                        "location" => $location,
                        "image" => $image_file,
                        "bg_color" => $bg_color,
                        "bg_style" => $bg_style,
                        "tags" => $tags,
                        );
        
        if( $photo_id ) 
        {
            mysql_update('photos',$values,"id",$photo_id);
        } 
        else 
        {
            mysql_insert('photos',$values);
            $photo_id = mysql_insert_id();
        }
        
        $postedValues['success'] = "1";
        $postedValues['postedValues'] = $_REQUEST;
        
        require_once '../include/utils.php';
        
        if( $_POST['ajax'] )
        {
            $photo_data = get_photo_data($photo_id);
            $postedValues['photo_data'] = $photo_data;
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
