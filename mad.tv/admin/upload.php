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
        @move_uploaded_file($_FILES[$name]['tmp_name'], '../media/' . $file_name);
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
        $video_file_ogv = str_replace('.mp4','.ogv',$video_file);
        @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $video_file_ogv $video_file");
    }
    
    $name = $_POST['name'];
    $artist = $_POST['artist'];
    
    $inserts = array("name" => $name,
                     "artist" => $artist,
                     "logo_file" => $logo_file,
                     "poster_file" => $poster_file,
                     "video_file" => $video_file,
                     );
                     
    mysql_insert('videos',$inserts);
}

// Upload video
if(!empty($_FILES["video"]["name"]))
{
    if (is_uploaded_file($_FILES["video"]["tmp_name"]))
    {
        ignore_user_abort(true);
        set_time_limit(0);
        $tmp_file = $_FILES['video']['tmp_name'];
        $ext = explode(".",$_FILES['video']['name']);
        $upload_ext = strtolower($ext[count($ext)-1]);
        
        $video_sound_mp4 = $artistid . '_' . strtolower( rand(11111,99999) . '_video.mp4' );
        $dest_file = '../vid/' . $video_sound_mp4;
        $dest_file_ogv = str_replace('.mp4','.ogv',$dest_file);
        
        $args = "-i_qfactor 0.71 -qcomp 0.6 -qmin 10 -qmax 63 -qdiff 4 -trellis 0 -vcodec libx264 -s 640x360 -vb 300k -ab 64k -ar 44100 -threads 4";
        if( $upload_ext == "mp4" )
        {
            @move_uploaded_file($tmp_file, $dest_file);
            @chmod($dest_file, 0644);
            //@system("/usr/bin/qafaststart $dest_file");
            @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $dest_file_ogv $dest_file");
            
            $video_sound = $video_sound_mp4;
            $upload_video_filename = $_FILES["video"]["name"];
        }
        else if( $upload_ext == "mov" )
        {
            @system("/usr/local/bin/ffmpeg -i $tmp_file $args $dest_file");
            @unlink($_FILES['video']['tmp_name']);
            //@system("/usr/bin/qafaststart $dest_file");
            @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $dest_file_ogv $dest_file");
            
            $video_sound = $video_sound_mp4;
            $upload_video_filename = $_FILES["video"]["name"];
        }
        else
        {
            $postedValues['upload_error'] = 'Please upload video files in MP4 or MOV format.';
            $video_sound = '';
        }
    } 
    else 
    {
        if ($old_sound != "") 
        {
            $video_sound = $old_sound;
        }
    }
}
else
{
    $video_sound = $old_sound;
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

