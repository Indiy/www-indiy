<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once '../../includes/functions.php';   
    require_once '../../includes/config.php';

    session_start();
    if( $_SESSION['sess_userId'] == "" )
    {
        header("HTTP/1.0 403 Forbidden");
        print "Not logged in";
        exit();
    }
    
    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);

    $artist_id = $data['artist_id'];
    $old_password = $data['old_password'];
    $new_password = $data['new_password'];

    $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE `id` = '$artist_id' LIMIT 1"));
    
    if( strlen($artist['password']) == 0 )
    {
        if( strlen($old_password) != 0 )
        {
            header("HTTP/1.0 403 Forbidden");
            print "old_password not blank but password is";
            exit();
        }
        print "empty password\n";
    }
    else if( md5($old_password) != $artist['password'] )
    {
        header("HTTP/1.0 403 Forbidden");
        print "old_password doesnt match password (".$artist['password'].")";
        exit();
    }

    $updates = array("password" => md5($new_password));
    $ret = mysql_update("mydna_musicplayer",$updates,"id",$artist_id);
    print_r($ret);
    print "rows: " . mssql_rows_affected();

    echo "{ \"success\": 1 }\n";
?>

