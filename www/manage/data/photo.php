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
    
    function get_photo_data($photo_id)
    {
        $row = mf(mq("SELECT * FROM photos WHERE id='$photo_id'"));
        
        array_walk($row,cleanup_row_element);
        
        $image_path = PATH_TO_ROOT . "artists/files/" . $row['image'];
        if( !empty($row['image']) && file_exists($image_path) )
            $row['image_url'] = "/artists/files/" . $row['image'];
        else
            $row['image_url'] = "images/photo_video_01.jpg";

        return $row;
    }
    
    function do_POST()
    {
        $artist_id = $_POST['artist_id'];
        
        $photo_id = $_POST['id'];
        if( $photo_id ) 
        {
            $row = mf(mq("SELECT * FROM photos WHERE id='$photo_id'"));
            
            $old_image_file = $row["image"];
            $old_image_data = $row["image_data"];
        }
        
        $name = $_POST["name"];
        $location = $_POST["location"];
        $bg_color = $_POST["bg_color"];
        $bg_style = $_POST["bg_style"];
        $tags = $_POST["tags"];
        $image_data = $old_image_data;
        
        $image = $old_image;
        
        $ret = artist_file_upload($artist_id,$_FILES["logo"],$old_image_file);
        $image_file = $ret['file'];
        if( isset($ret['image_data']) )
            $image_data = $ret['image_data'];
        else
            $image_data = $old_image_data;
        
        $values = array("artist_id" => $artist_id,
                        "name" => $name,
                        "location" => $location,
                        "image" => $image_file,
                        "bg_color" => $bg_color,
                        "bg_style" => $bg_style,
                        "tags" => $audio_tags,
                        "image_data" => $image_data,
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
