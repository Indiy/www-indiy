<?php
    
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");
    
    include('../includes/config.php');
    include('../includes/functions.php');
    
    $artist_id = $_REQUEST['artist_id'];
    $form_tag = $_REQUEST['form_tag'];
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $phone = $_REQUEST['phone'];
    
    $values = array(
                    "artist_id" => $artist_id,
                    "form_tag" => $form_tag,
                    "name" => $name,
                    "email" => $email,
                    "phone" => $phone,
                    );
    
    mysql_insert('subscribers',$values);

    $ret = array("success" => 1);

    $json = json_encode($ret);
    if( isset($_REQUEST['callback']) )
    {
        $callback = $_REQUEST['callback'];
        echo "$callback($json);";
    }
    else
    {
        echo $json;
    }
    
?>