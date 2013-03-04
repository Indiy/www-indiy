<?
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
    $user_id = $_SESSION['sess_userId'];
    
    if( $artist_id != $user_id )
    {
        if( $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
        {
            header("Location: /index.php");
            exit();
        }
    }

    if( $_REQUEST['confirm'] == 'true' )
    {
        $artist_id = mysql_real_escape_string($artist_id);
        $q = "DELETE FROM mydna_musicplayer WHERE id='$artist_id' LIMIT 1";
        mysql_query($q);
        header("Location: /logout.php");
        exit();
    }
    
    header("Location: /index.php");
    exit();
?>

