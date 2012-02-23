<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('../includes/functions.php');   
    include('../includes/config.php');
    if( $_SESSION['sess_userId'] == "" )
    {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }
    
    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);

    $artist_id = $data['artist_id'];
    $old_password = $data['old_password'];
    $new_password = $data['new_password'];

    $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE `id` = '$artist_id' LIMIT 1"));
    
    if( strlen($artist['password']) == 0 && strlen($old_password) != 0 )
    {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }
    else if( md5($old_password) != $artist['password'] )
    {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }

    $updates = array("password" => md5($new_password));
    mysql_update("mydna_musicplayer",$updates,"id",$artist_id);

    echo "{ \"success\": 1 }\n";
?>

