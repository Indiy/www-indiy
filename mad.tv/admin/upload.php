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

function handle_upload($name)
{
    if(!empty($_FILES[$name]["name"]))
    {
        $ext = explode(".",$_FILES[$name]['name']);
        $upload_ext = strtolower($ext[count($ext)-1]);
        $rand = rand(11111,99999);
        $file_name = strtolower("$name$rand_." . $upload_ext);
        @move_uploaded_file($_FILES[$name]['tmp_name'], "../media/$file_name");
        return $file_name;
    }

}

function mysql_insert($table,$inserts)
{
    $values = array_map('mysql_real_escape_string', array_values($inserts));
    $keys = array_keys($inserts);
    $q = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\')';
    return mysql_query($q);
}

if( $_POST['submit'] )
{
    $logo_file = handle_upload("logo");
    $poster_file = handle_upload("poster");
    $video_file = handle_upload("video_file");
    if( $video_file )
    {
        $video_input = "../media/$video_file";
        $video_input_ogv = str_replace('.mp4','.ogv',$video_input);
        @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $video_input_ogv $video_input");
    }
    
    $name = $_POST['name'];
    $artist = $_POST['artist'];
    
    $inserts = array("name" => $name,
                     "artist" => $artist,
                     "logo_file" => $logo_file,
                     "poster_file" => $poster_file,
                     "video_file" => $video_file,
                     );
    
    echo "<html><body><pre>\n";
    
    var_dump($inserts);
    
    mysql_insert('videos',$inserts);
    
    echo "Upload successful\n";
    exit();
}

?>

<html>
<body>
<h1>Fancy Upload interface</h1>

<form method='post'>
    Name: <input name='name'><br/>
    Artist: <input name='artist'><br/>
    Logo: <input type='file' name='logo'><br/>
    Poster: <input type='file' name='poster'><br/>
    Video File: <input type='file' name='video_file'><br/>
    <br/>
    <input type='submit' value='Submit'><br/>
</form>

</body>
</html>

