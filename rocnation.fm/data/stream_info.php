<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
    header("Access-Control-Allow-Origin: *");

    $FILE = "/tmp/rn_fm_genre_data.json";
    
    $dbhost		=	"localhost";
    $dbusername	=	"rnfm_user";
    $dbpassword	=	"rnfm_password";
    $dbname		=	"rocnationfm";

    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");

    $sql = "SELECT * FROM genres ORDER BY `order` ASC";
    $q = mysql_query($sql);
    $genre_list = array();
    while( $row = mysql_fetch_array($q) )
        $genre_list[] = $row;

    $json = file_get_contents($FILE);
    
    $ret = array();
    $ret['genre_data'] = json_decode($json);
    $ret['genre_list'] = $genre_list;
    print json_encode($ret);

?>

