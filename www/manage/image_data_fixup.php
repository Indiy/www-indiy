<?php
    
    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if( $_SESSION['sess_userId'] == '' && php_sapi_name() != 'cli')
	{
		header("Location: index.php");
		exit();
	}
    session_write_close();
    
    echo "<html><body><pre>\n";
    
    print "audio image data\n";
    
    $sql = "SELECT * FROM mydna_musicplayer_audio WHERE image_data IS NULL OR image_data LIKE \"\"";
    $image_q = mq($sql);

    $total = 0;
    $good = 0;

    while( $row = mf($image_q) )
    {
        $total++;
        $id = $row['id'];
        $image = $row['image'];
        $image_path = "../artists/files/$image";
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
                $good++;
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
    
    print "total: $total, good: $good\n";

    print "\n\n============================================\n\n";
    print "video image data\n";
    
    $sql = "SELECT * FROM mydna_musicplayer_video WHERE image_data IS NULL OR image_data LIKE \"\"";
    $image_q = mq($sql);
    
    $total = 0;
    $good = 0;
    
    while( $row = mf($image_q) )
    {
        $total++;
        $id = $row['id'];
        $image = $row['image'];
        $image_path = "../artists/files/$image";
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
                
                mysql_update("mydna_musicplayer_video",
                             array("image_data" => $json),
                             "id",$id);
                print "image: $image, w: $width, h: $height\n";
                $good++;
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
    
    print "total: $total, good: $good\n";

    print "done done\n\n";
    
?>

