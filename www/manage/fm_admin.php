<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId']=="")
	{
		header("Location: /index.php");
		exit();
	}
	$artist_id = $_REQUEST['artist_id'];
    if( !$artist_id )
    {
        if( $_SESSION['sess_userType'] == 'ARTIST' )
        {
            $artist_id = $_SESSION['sess_userId'];
        }
        else
        {
            header("Location: dashboard.php");
            exit();
        }
    }
    
    $streams = array();
    
    $sql = "SELECT * FROM fm_streams WHERE artist_id='$artist_id' ORDER BY `order` ASC, id ASC";
    $q = mq($sql);
    while( $s = mf($q) )
    {
        $streams[] = $s;
    }
    
    $sql = "SELECT * FROM artist_files WHERE artist_id='$artistID' AND upload_filename != '' ORDER BY id DESC";
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
    
    foreach( $streams as $i => $stream )
    {
        $id = $stream['id'];
        $streams[$i]['songs'] = array();
        $q = mq("SELECT * FROM fm_songs WHERE stream_id='$id'");
        while( $song = mf($q) )
        {
            $streams[$i]['songs'][] = $song;
        }
    }
    
    $streams_json = json_encode($streams);
    
    include_once "templates/fm_admin.html";

?>