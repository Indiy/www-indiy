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


function get_product_data($product_id)
{
    $row = mf(mq("SELECT * FROM mydna_musicplayer_ecommerce_products WHERE id='$product_id'"));
    
    array_walk($row,cleanup_row_element);
    $image_path = "../artists/products/" . $row['image'];
    if( !empty($row['image']) )
        $row['image'] = $image_path;
    else
        $row['image'] = "images/photo_video_01.jpg";
    
    return $row;
}


function do_POST()
{
    $getid = $_POST["id"];
    $artist_id = $_POST["artistid"];
    
    if( is_uploaded_file($_FILES["file"]["tmp_name"]) ) 
    {
        $productimage = strtolower(rand(111,999)."_".basename($_FILES["file"]["name"]));
        @move_uploaded_file($_FILES['file']['tmp_name'], PATH_TO_ROOT . "artists/products/$productimage");
        $image = $productimage;
    } 
    else 
    {
        if( $getid != "" ) 
        {
            $productss = mf(mq("SELECT `image` FROM mydna_musicplayer_ecommerce_products WHERE id='$getid'"));
            $image = $productss["image"];	
        } 
        else 
        {
            $image = "";
        }
    }
    
    $origin = $_POST["origin"];
    $sale = $_POST["sale"];
    $page = $_POST["page"];
    $name = my($_POST["name"]);
    $newvideo = $_POST["video"];		
    $productdescription = my($_POST["description"]);
    $price = str_replace("$", "", $_POST["price"]);
    $discount = $_POST["discount"];
    $sku = $_POST["sku"];
    $newsize = $_POST["size"];
    $newcolor = $_POST["color"];
    $newstock = "unlimited";
    $manufacturer = $_POST["manufacturer"];
    $newtags = my($_POST["tags"]);
    $newfilename = "";
    
    $tables = "artistid|page|stock|origin|subcat|name|filename|description|image|price|discount|sku|sale|manufacturer|tags|size|color";
    $values = "$artist_id|$page|$newstock|$origin|$subcat|$name|$newfilename|$productdescription|$image|$price|$discount|$sku|$sale|$manufacturer|$newtags|$newsize|$newcolor";
    
    if( $getid != "" ) 
    {
        update("mydna_musicplayer_ecommerce_products",$tables,$values,"id",$getid);
    } 
    else 
    {
        insert("mydna_musicplayer_ecommerce_products",$tables,$values);
        $getid = mysql_insert_id();
    }
    $postedValues['imageSource'] = $productimage;
    $postedValues['success'] = "1";
    $postedValues['postedValues'] = $_REQUEST;

    if( $_POST['ajax'] )
    {
        $postedValues['product_data'] = get_product_data($getid);
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
