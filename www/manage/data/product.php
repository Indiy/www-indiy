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
    
    if( is_uploaded_file($_FILES["file"]["tmp_name"]) ) 
    {
        $productimage = strtolower(rand(111,999)."_".basename($_FILES["file"]["name"]));
        @move_uploaded_file($_FILES['file']['tmp_name'], PATH_TO_ROOT . "artists/products/$productimage");
        $image = $productimage;
    } 
    else 
    {
        if( $product_id != "" ) 
        {
            $product = mf(mq("SELECT image FROM mydna_musicplayer_ecommerce_products WHERE id='$product_id'"));
            $image = $product["image"];	
        } 
        else 
        {
            $image = "";
        }
    }
    
    $name = my($_POST["name"]);
    $description = my($_POST["description"]);
    $price = str_replace("$", "", $_POST["price"]);
    $shipping = str_replace("$", "", $_POST["shipping"]);
    $size = $_POST["size"];
    $color = $_POST["color"];
    $tags = $_POST["tags"];
    $type = $_POST["type"];
    
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
        $tmp_file = $_FILES["digital_download1"]["tmp_name"];
        if( is_uploaded_file($tmp_file) ) 
        {
            $upload_filename = basename($_FILES["digital_download1"]["name"]);
            $extension = pathinfo($upload_filename,PATHINFO_EXTENSION);
            $rand = rand(100000,999999);
            $filename =  "$rand.$extension";
            $dest = PATH_TO_ROOT . "artists/digital_downloads/$filename";
            
            @move_uploaded_file($tmp_file,$dest);
            $image = $productimage;
            
            $values = array("product_id" => $product_id,
                            "upload_filename" => $upload_filename,
                            "filename" => $filename,
                            );
                            
            mysql_insert("product_files",$values);
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
