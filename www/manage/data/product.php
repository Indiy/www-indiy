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
            mysql_update('mydna_musicplayer_ecommerce_products',$values,"id",$id);
            ++$count;
        }
        
        $ret = array("success" => 1);
        echo json_encode($ret);
        exit();
    }


function do_POST()
{
    $product_id = $_POST["id"];
    $artist_id = $_POST["artistid"];
    
    $product = mf(mq("SELECT image FROM mydna_musicplayer_ecommerce_products WHERE id='$product_id'"));
    $old_image = $product["image"];

    $image = $_POST['image_drop'];
    
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = str_replace("$", "", $_POST["price"]);
    $shipping = str_replace("$", "", $_POST["shipping"]);
    $size = $_POST["size"];
    $color = $_POST["color"];
    $type = $_POST["type"];
    $extra_json = $_POST["extra_json"];
    
    if( $type == "DIGITAL" )
        $shipping = 0.0;
    
    $values = array("artistid" => $artist_id,
                    "name" => $name,
                    "description" => $description,
                    "image" => $image,
                    "price" => $price,
                    "shipping" => $shipping,
                    "size" => $size,
                    "color" => $color,
                    "type" => $type,
                    "extra_json" => $extra_json,
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
        if( isset($_POST["add_digital_downloads"]) )
        {
            $add_digital_downloads = $_POST["add_digital_downloads"];
            if( strlen($add_digital_downloads) > 0 )
            {
                $add_list = explode(',',$add_digital_downloads);
                foreach( $add_list as $file_id )
                {
                    $file = mf(mq("SELECT * FROM artist_files WHERE id='$file_id' AND artist_id='$artist_id'"));
                    if( $file )
                    {
                        $filename = $file['filename'];
                        $upload_filename = $file['upload_filename'];
                        
                        $values = array("product_id" => $product_id,
                                        "upload_filename" => $upload_filename,
                                        "filename" => $filename,
                                        );
                        mysql_insert("product_files",$values);
                    }
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
