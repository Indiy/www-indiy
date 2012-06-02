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
        
        $image_path = PATH_TO_ROOT . "artists/photo/" . $row['image'];
        if( !empty($row['image']) && file_exists($image_path) )
            $row['image_url'] = "/artists/photo/" . $row['image'];
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
            
            $old_image = $row["image"];
            $old_image_data = $row["image_data"];
        }
        
        $name = $_POST["name"];
        $location = $_POST["location"];
        $bg_color = $_POST["bg_color"];
        $bg_style = $_POST["bg_style"];
        $tags = $_POST["tags"];
        $image_data = $old_image_data;
        
        $image = $old_image;
        
        if(!empty($_FILES['image']['name']))
        {
            if (is_uploaded_file($_FILES['image']['tmp_name'])) 
            {
                $image_data = get_image_data($_FILES['image']['tmp_name']);
                if( $image_data != NULL )
                {
                    $image = $artist_id."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES['image']['name'])));
                    @move_uploaded_file($_FILES['image']['tmp_name'], PATH_TO_ROOT . "artists/photo/$image");
                }
                else
                {
                    $image_data = $old_image_data;
                    $postedValues['image_error'] = "Image format not recognized.";
                }
            }
        }
        
        $values = array("artist_id" => $artist_id,
                        "name" => $name,
                        "location" => $location,
                        "image" => $image,
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
