<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);

    $name = $data['name'];
    $url = $data['url'];
    $email = $data['email'];
    $password = md5($data['password']);

    $error = FALSE;

    $sql = "SELECT * FROM mydna_musicplayer";
    $sql .= " WHERE url = '$url' ";
    $sql .= " OR email = '$email' ";
    $q = mysql_query($sql) or die("bad sql: '$sql'");
    $row = mf($q);
    if( $row )
    {
        if( $row['url'] == $url )
            $error = "That URL is already taken.";
        else
            $error = "User already exists with that name or email address.";
    }
    else
    {
        $values = array("artist" => $name,
                        "url" => $url,
                        "email" => $email,
                        "password" => $password,
                        );
        if( mysql_insert('mydna_musicplayer',$values) )
        {
            $insert_id = mysql_insert_id();
            $q = mysql_query("SELECT * FROM mydna_musicplayer WHERE id = $insert_id");
            $row = mf($q);
            post_signup($row);
            $url = loginArtistFromRow($row);
        }
        else
        {
            $error = "Database error, please try again.";
        }
    }

    $output = array("error" => $error,"url" => $url);
    print json_encode($output);
?>
