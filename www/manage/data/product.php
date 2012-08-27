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


function do_POST()
{
    $product_id = $_POST["id"];
    $artist_id = $_POST["artistid"];
    
    $product = mf(mq("SELECT image FROM mydna_musicplayer_ecommerce_products WHERE id='$product_id'"));
    $old_image = $product["image"];

    $ret = artist_file_upload($artist_id,$_FILES["file"],$old_image);
    $image = $ret['file'];
    
    $name = my($_POST["name"]);
    $description = my($_POST["description"]);
    $price = str_replace("$", "", $_POST["price"]);
    $shipping = str_replace("$", "", $_POST["shipping"]);
    $size = $_POST["size"];
    $color = $_POST["color"];
    $tags = $_POST["tags"];
    $type = $_POST["type"];
    
    if( $type == "DIGITAL" )
        $shipping = 0.0;
    
    $values = array("artistid" => $artist_id,
                    "name" => $name,
                    "description" => $description,
                    "image" => $image,
                    "price" => $price,
                    "shipping" => $shipping,
                    "tags" => $tags,
                    "size" => $size,
                    "color" => $color,
                    "type" => $type,
                    );
    
    if( $product_id != "" ) 
    {
        mysql_update("mydna_musicplayer_ecommerce_products",$values,"id",$product_id);
    } 
    else 
    {
        mysql_insert("mydna_musicplayer_ecommerce_products",$values);
        $product_id = mysql_insert_id();
    }
    
    if( $type == "DIGITAL" )
    {
        $ret = artist_file_upload($artist_id,$_FILES["digital_download1"],FALSE);
        if( $ret['file'] )
        {
            $filename = $ret['file'];
            $upload_filename = basename($_FILES["digital_download1"]["name"]);
            $values = array("product_id" => $product_id,
                            "upload_filename" => $upload_filename,
                            "filename" => $filename,
                            );
            mysql_insert("product_files",$values);
        }
        
        if( isset($_POST["remove_digital_downloads"]) )
        {
            $remove_digital_downloads = $_POST["remove_digital_downloads"];
            if( strlen($remove_digital_downloads) > 0 )
            {
                $remove_list = explode(',',$remove_digital_downloads);
                foreach( $remove_list as $file_id )
                {
                    $updates = array("is_deleted" => 1);
                    mysql_update('product_files',$updates,'id',$file_id);
                }
            }
        }
    }

    
    $postedValues['imageSource'] = $productimage;
    $postedValues['success'] = "1";
    $postedValues['postedValues'] = $_REQUEST;

    if( $_POST['ajax'] )
    {
        $postedValues['product_data'] = get_product_data($product_id);
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
