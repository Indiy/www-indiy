<?

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");


    function generatePassword($length = 8)
    {
        $possible = "123467890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $possible_len = strlen($possible);
        
        $password = "";
        for($i = 0 ; $i < $length ; $i++ ) 
        {
            $char = substr($possible, mt_rand(0, $possible_len-1), 1);
            $password .= $char;
        }
        return $password;
    }

    $email = $_REQUEST['email'];

    $error = FALSE;
    $msg = '';

    $sql = "SELECT * FROM mydna_musicplayer WHERE email = '$email' LIMIT 1";
    $q = mysql_query($sql) or die("bad sql: '$sql'");
    if( mysql_num_rows($q) == 0 )
    {
        $error = 1;
    }
    else
    {
        $user = mf($q);
        $new_password = generatePassword();
        mysql_update("mydna_musicplayer",
                     array("password" => md5($new_password)),
                     "id",
                     $user['id']);
        $error = 0;
        $msg = "Your new password has been emailed to $email.";
    }

    $output = array("error" => $error,"msg" => $msg);
    print json_encode($output);

?>

