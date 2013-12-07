<?php

	require_once('includes/config.php');
	require_once('includes/functions.php');	

	$artist = $_GET['artist'];
	$getid = $_GET['id'];
    $email = $_GET['email'];
    
    if( $email && $artist )
    {
        setcookie('PAGE_VIEWER_EMAIL',$email,time() + 365*24*60*60,'/');

        $values = array('artistid' => $artist,
                        'email' => $email,
                        );
        mysql_insert("mydna_musicplayer_subscribers",$values);
    }
    
	trackDownloads($getid);
	$music = mf(mq("select * from `[p]musicplayer_audio` where `artistid`='{$artist}' and `id`='{$getid}'"));
	$filename = 'artists/files/'.$music["audio"];
    
    $download_filename = $music["upload_audio_filename"];
    if( !$download_filename )
        $download_filename = $music["audio"];
    

// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');

// addition by Jorg Weske
$file_extension = strtolower(substr(strrchr($filename,"."),1));

if( $filename == "" ) 
{
  echo "<html><title>Download</title><body>ERROR: download file NOT SPECIFIED.</body></html>";
  exit;
} elseif ( ! file_exists( $filename ) ) 
{
  echo "<html><title>Download</title><body>ERROR: File not found.</body></html>";
  exit;
};
switch( $file_extension )
{
  case "pdf": $ctype="application/pdf"; break;
  case "exe": $ctype="application/octet-stream"; break;
  case "zip": $ctype="application/zip"; break;
  case "doc": $ctype="application/msword"; break;
  case "xls": $ctype="application/vnd.ms-excel"; break;
  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
  case "gif": $ctype="image/gif"; break;
  case "png": $ctype="image/png"; break;
  // case "mp3": $ctype="audio/mpeg"; break;
  case "jpeg":
  case "jpg": $ctype="image/jpg"; break;
  default: $ctype="application/force-download";
}
header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
header("Content-Type: $ctype");
// change, added quotes to allow spaces in filenames, by Rajkumar Singh
header("Content-Disposition: attachment; filename=\"".basename($download_filename)."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($filename));
readfile("$filename");
exit();

?>
    