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
    {
        $method = strtoupper($_REQUEST['method']);
    }
    
    if( $method == 'POST' )
    {
        do_POST();
    }
    else
    {
        print "Bad method\n";
    }
    exit();
    
    
    function do_POST()
    {
        $fm_stream_id = $_POST['fm_stream_id'];
        $scrubber_text = $_POST['scrubber_text'];
        $bottom_text = $_POST['bottom_text'];
        $audio_file_id = $_POST['audio_file_id'];
        $image_file_id = $_POST['image_file_id'];
        
        $values = array("fm_stream_id" => $fm_stream_id,
                        "scrubber_text" => $scrubber_text,
                        "bottom_text" => $bottom_text,
                        "audio_file_id" => $audio_file_id,
                        "image_file_id" => $image_file_id,
                        );
        
        if( isset($_POST['fm_song_id']) )
        {
            $id = $_POST['fm_song_id'];
            mysql_update('fm_songs',$values,'id',$id);
        }
        else
        {
            mysql_insert('fm_songs',$values);
            $id = mysql_insert_id();
        }
        
        $song = mf(mq("SELECT * FROM fm_songs WHERE id='$id'"));
        
        $ret = array("success" => 1,
                     "song" => $song,
                     );
        
        echo json_encode($ret);
        exit();
    }
    
    
?>