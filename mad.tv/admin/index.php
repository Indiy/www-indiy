<?

//Production 
error_reporting(0);
$dbhost		=	"localhost";
$dbusername	=	"madtv_user";
$dbpassword	=	"MyartistDNA!";
$dbname		=	"madtv_mysql";

//echo "<html><body><pre>\n";

/*
 // MADDEV.COM
 error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
 $dbhost		=	"localhost";
 $dbusername	=	"maddvcom_user";
 $dbpassword	=	"MyartistDNA!";
 $dbname		=	"maddvcom_mysql";
 */

$connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
mysql_select_db($dbname,$connect) or die ("Could not select database");

$sql = "SELECT * FROM videos ORDER BY `order` ASC, `id` ASC";
$q = mysql_query($sql);
$video_list = array();
while( $row = mysql_fetch_array($q) )
{
    $logo = $row['logo_file'];
    $poster = $row['poster_file'];
    $video_file = $row['video_file'];
    $item = array("artist" => $row['artist'],
                  "name" => $row['name'],
                  "logo" => "/media/$logo",
                  "poster" => "/media/$poster",
                  "video_file" => "/media/$video_file",
                  );
    $video_list[] = $item;
}

$video_list_json = json_encode($video_list);


?>

<html>
<body>
    <h1>Video List</h1>
    
    <?
        
        for( $video_list as $k => $video )
        {
            echo "Name: " . $video['name'] . "<br/>\n";
            echo "Artist: " . $video['name'] . "<br/>\n";
            echo "Logo: " . $video['name'] . "<br/>\n";
            echo "Name: " . $video['name'] . "<br/>\n";
            echo "Name: " . $video['name'] . "<br/>\n";
        }
    
    ?>
</body>
</html>