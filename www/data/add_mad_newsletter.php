<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once('../includes/functions.php');   
    require_once('../includes/config.php');
    
    $email = $_REQUEST['add_email'];

    mysql_insert('mad_newsletter',array( 'email' => $email ));
    
    echo "{ \"success\": 1 }\n";
?>

