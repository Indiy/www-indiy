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
    
    $sql = "SELECT * FROM artist_files WHERE artist_id='$artist_id' AND upload_filename != '' AND deleted = 0 ORDER BY id DESC";
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
        
        $sql = "";
        $sql .= "SELECT fm_songs.*,";
        $sql .= " audio_file.filename AS audio_filename, audio_file.upload_filename AS audio_upload_filename,";
        $sql .= " image_file.filename AS image_filename, image_file.upload_filename AS image_upload_filename";
        $sql .= " FROM fm_songs";
        $sql .= " JOIN artist_files AS audio_file ON fm_songs.audio_file_id = audio_file.id";
        $sql .= " JOIN artist_files AS image_file ON fm_songs.image_file_id = image_file.id";
        $sql .= " WHERE fm_stream_id='$id'";
        
        $q = mq($sql);
        while( $song = mf($q) )
        {
            $streams[$i]['songs'][] = $song;
        }
    }
    
    $streams_json = json_encode($streams);
    
    include_once "templates/fm_admin.html";

?>