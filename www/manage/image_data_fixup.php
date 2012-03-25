<?php
    
    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli')
	{
		header("Location: index.php");
		exit();
	}
    
    echo "<html><body><pre>\n";
    
    $q = "SELECT * FROM mydna_musicplayer_audio WHERE image_data IS NULL";
    $rows = mf(mq($q));

    for( $rows as $row )
    {
        $id = $row['id'];
        $image = $row['image'];
        $image_path = "../artist/images/$image";
        $data = getimagesize($image_path);
        if( count($data) > 3 )
        {
            $width = $data[0];
            $height = $data[1];
            if( $width > 0 && $height > 0 )
            {
                $image_data = array("width" => $width,
                                    "height" => $height);
                
                $json = json_encode($image_data);
                
                mysql_update("mydna_musicplayer_audio",
                             array("image_data" => $json),
                             "id",$id);
                print "image: $image, w: $width, h: $height\n";
            }
            else
            {
                print "bad w/h: $image\n";
            }
        }
        else
        {
            print "bad image: $image??\n";
        }
        
    }
    
?>

