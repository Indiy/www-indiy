<?

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    require_once '../includes/login_helper.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);

    $name = $data['name'];
    $email = $data['email'];
    $username = $data['username'];
    $password = $data['password'];

    $error = FALSE;
    $url = '';

    $q = mysql_query("SELECT * FROM mydna_musicplayer WHERE username = $username OR url = $username");
    if( mysql_num_rows($q) == 0 )
    {
        $error = "Username already exists.";
    }
    else
    {
        $tables = "artist|url|email|username|password";
		$values = "{$name}|{$username}|{$email}|{$username}|{$password}";
        if( insert('[p]musicplayer',$tables,$values) )
        {
            $insert_id = mysql_insert_id();
            $q = mysql_query("SELECT * FROM mydna_musicplayer WHERE id = $insert_id");
            $row = mf($q);
            $url = loginWithRow($row);
        }
        else
        {
            $error = "Database error, please try again.";
        }
    }

    $output = array("error" => $error,"url" => $url);
    print json_encode($output);
?>
