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

    if( $_SERVER['REQUEST_METHOD'] == 'POST' )
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
        $artist_id = $_REQUEST['artist_id'];

        $fb_setting = $_REQUEST['fb_setting'];
        $tw_setting = $_REQUEST['tw_setting'];
        $fb_page_url = $_REQUEST['fb_page_url'];

        mysql_update('mydna_musicplayer',
                     array(
                           "fb_setting" => $fb_setting,
                           "tw_setting" => $tw_setting,
                           "fb_page_url" => $fb_page_url,
                           ),
                     'id',$artist_id);

        $postedValues['success'] = "1";
        $postedValues['postedValues'] = $_REQUEST;
        if( $_REQUEST['ajax'] )
        {
            $postedValues['artist_data'] = get_artist_data($artist_id);
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