<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';

    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
	$artistID = $_REQUEST['userId'];
    if( !$artistID )
    {
        if( $_SESSION['sess_userType'] == 'ARTIST' )
        {
            $artistID = $_SESSION['sess_userId'];
        }
        else
        {
            header("Location: dashboard.php");
            exit();
        }
    }
    setcookie('LOGIN_EMAIL',$_SESSION['sess_userEmail'], time() + 30*24*60*60,'/');
    
    $MAX_TABS = 5;
    
	$query_artistDetail = "SELECT * FROM mydna_musicplayer WHERE id='".$artistID."' ";
	$result_artistDetail = mysql_query($query_artistDetail) or die(mysql_error());
	$record_artistDetail = mysql_fetch_array($result_artistDetail);

	if(isset($_REQUEST['action']))
    {
		if(isset($_REQUEST['song_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_audio WHERE id='".$_REQUEST['song_id']."' ");
        }
		elseif(isset($_REQUEST['video_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_video WHERE id='".$_REQUEST['video_id']."' ");
        }
		elseif(isset($_REQUEST['content_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_content WHERE id='".$_REQUEST['content_id']."' ");
        }
		elseif(isset($_REQUEST['prod_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_ecommerce_products WHERE id='".$_REQUEST['prod_id']."' ");
        }
		elseif(isset($_REQUEST['photo_id']))
        {
            mysql_query("DELETE FROM photos WHERE id='".$_REQUEST['photo_id']."' ");
        }
	}
	
	$sql = "SELECT mydna_musicplayer_audio.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_audio ";
    $sql .= " LEFT JOIN artist_files ON mydna_musicplayer_audio.image = artist_files.filename";
    $sql .= " WHERE mydna_musicplayer_audio.artistid='$artistID'";
    $sql .= " ORDER BY mydna_musicplayer_audio.order ASC, mydna_musicplayer_audio.id DESC";
	$result_artistAudio = mq($sql) or die(mysql_error());
    $page_list = array();
    while( $row = mf($result_artistAudio) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        $row['short_link'] = make_short_link($row['abbrev']);
        $row['image_extra'] = $image_extra;
        array_walk($row,cleanup_row_element);
        $row['download'] = $row['download'] == "0" ? FALSE : TRUE;
        $row['product_id'] = $row['product_id'] > 0 ? intval($row['product_id']) : FALSE;
        $page_list[] = $row;
    }
    $page_list_json = json_encode($page_list);
	
	$sql = "SELECT mydna_musicplayer_video.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_video ";
    $sql .= " LEFT JOIN artist_files ON mydna_musicplayer_video.image = artist_files.filename";
    $sql .= " WHERE mydna_musicplayer_video.artistid = '$artistID'";
    $sql .= " ORDER BY mydna_musicplayer_video.order ASC, mydna_musicplayer_video.id DESC";
	$result_artistVideo = mq($sql) or die(mysql_error());
    $video_list = array();
    while( $row = mf($result_artistVideo) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        array_walk($row,cleanup_row_element);
        $row['image_extra'] = $image_extra;
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $video_list[] = $row;
    }
    $video_list_json = json_encode($video_list);
    
	$sql = "SELECT photos.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM photos ";
    $sql .= " LEFT JOIN artist_files ON photos.image = artist_files.filename";
    $sql .= " WHERE photos.artist_id = '$artistID'";
    $sql .= " ORDER BY photos.order ASC, photos.id DESC";
	$q_photo = mq($sql) or die(mysql_error());
    $photo_list = array();
    while( $row = mf($q_photo) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        array_walk($row,cleanup_row_element);
        $row['image_extra'] = $image_extra;
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $photo_list[] = $row;
    }
    $photo_list_json = json_encode($photo_list);

	$sql = "SELECT * FROM mydna_musicplayer_content  WHERE artistid='$artistID' ORDER BY `order` ASC, `id` DESC";
	$result_artistContent = mq($sql) or die(mysql_error());
    $tab_list = array();
    while( $row = mf($result_artistContent) )
    {
        array_walk($row,cleanup_row_element);
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $tab_list[] = $row;
    }
    $tab_list_json = json_encode($tab_list);
	
	$sql = "SELECT mydna_musicplayer_ecommerce_products.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_ecommerce_products ";
    $sql .= " LEFT JOIN artist_files ON mydna_musicplayer_ecommerce_products.image = artist_files.filename";
    $sql .= " WHERE mydna_musicplayer_ecommerce_products.artistid = '$artistID'";
    $sql .= " ORDER BY mydna_musicplayer_ecommerce_products.order ASC, mydna_musicplayer_ecommerce_products.id DESC";
	$result_artistProduct = mq($sql) or die(mysql_error());
	$product_list = array();
    while( $row = mf($result_artistProduct) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        $product_id = $row['id'];
        $row = get_product_data($product_id);
        $row['image_extra'] = $image_extra;
        $product_list[] = $row;
    }
    $product_list_json = json_encode($product_list);
    
    $sql = "SELECT * FROM artist_files WHERE artist_id='$artistID' AND upload_filename != '' AND deleted = 0 ORDER BY id DESC";
    $files_q = mq($sql);
    $file_list = array();
    while( $file = mf($files_q) )
    {
        $id = $file['id'];
        $filename = $file['filename'];
        $upload_filename = $file['upload_filename'];
        $type = $file['type'];
        $item = array("id" => $id,
                      "filename" => $filename,
                      "upload_filename" => $upload_filename,
                      "type" => $type,
                      "is_uploading" => FALSE,
                      "error" => $file['error'],
                      );
        $file_list[] = $item;
    }
    $file_list_json = json_encode($file_list);
	
	if($record_artistDetail['logo'] == '')
		$artist_img_logo = "/manage/images/artist_need_image.jpg";
	else
		$artist_img_logo = artist_file_url($record_artistDetail['logo']);

	$img_url = $artist_img_logo;

    $is_published = TRUE;
    $artist_url = str_replace("http://www.","http://".$record_artistDetail['url'].".",trueSiteUrl());
    if( strlen($record_artistDetail['preview_key']) > 0 )
    {
        $preview_key = $record_artistDetail['preview_key'];
        $is_published = FALSE;
        $artist_url .= "?preview_key=$preview_key";
    }

    $show_first_instruction = FALSE;
    if( $_SESSION['sess_userType'] == 'ARTIST' )
    {
        if( !$record_artistDetail['shown_first_instructions'] )
        {
            $show_first_instruction = TRUE;
        }
    }
    
    $twitter = 'false';
    $facebook = 'false';
    if( $record_artistDetail['oauth_token'] && $record_artistDetail['oauth_secret'] && $record_artistDetail['twitter'] )
    {
        $twitter = 'true';
    }
    else
    {
        $record_artistDetail['twitter'] = FALSE;
    }
    if( $record_artistDetail['fb_access_token'] && $record_artistDetail['facebook'] )
    {
        $facebook = 'true';
    }
    else
    {
        $record_artistDetail['facebook'] = FALSE;
    }
    
    $artist_data = get_artist_data($artistID);
    $artist_data_json = json_encode($artist_data);
    
    
    $template_list = array();
    $sql = "SELECT * FROM templates WHERE artist_id='$artistID'";
    $q = mq($sql);
    while( $t = mf($q) )
    {
        $params_json = $t['params_json'];
        $params = json_decode($params_json,TRUE);
        
        $t['params'] = $params;
        
        $template_list[] = $t;
    }
    $template_list_json = json_encode($template_list);
    
    $playlist_list = array();
    $sql = "SELECT * FROM playlists WHERE artist_id='$artistID'";
    $q = mq($sql);
    while( $pl = mf($q) )
    {
        $playlist_id = $pl['playlist_id'];
        $pl['items'] = array();
        
        $sql = "SELECT playlist_items.*, artist_files.filename AS image, artist_files.extra_json AS image_extra_json ";
        $sql .= " FROM playlist_items ";
        $sql .= " LEFT JOIN artist_files ON playlist_items.image_id = artist_files.id ";
        $sql .= " WHERE playlist_id='$playlist_id' ";
        $q2 = mq($sql);
        while( $row = mf($q2) )
        {
            $image_extra = json_decode($row['image_extra_json'],TRUE);
            $row['image_extra'] = $image_extra;
            if( !empty($row['image']) )
            {
                $row['image_url'] = artist_file_url($row['image']);
            }
            else
            {
                $row['image_url'] = "images/photo_video_01.jpg";
            }
            $pl['items'][] = $row;
        }
        $playlist_list[] = $pl;
    }
    $playlist_list_json = json_encode($playlist_list);
    
    require_once "templates/artist_management.html";

?>