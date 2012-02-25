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
<head>
    <title>MyAritstDNA.tv</title>
    <link href="css/video-js.css"rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css">
        
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        
    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
    
    <script src="js/video.js" type="text/javascript"></script>
    <script src="js/index.js" type="text/javascript"></script>
    <script type="text/javascript">
        var g_videoList = <?=$video_list_json;?>;
    </script>
</head>
<body>
    <div id='video_player'>
        <div class='top_bar'>
            <div class='logo_title_artist'>
                <div class='logo'>
                    <img id='logo_img'>
                </div>
                <div class='title_artist'>
                    <div id='video_title' class='title'></div>
                    <div class='artist_label'>Artist:</div>
                    <div id='artist_name' class='artist'></div>
                </div>
            </div>
        </div>
        <div id='player_body' class='player_body'>
        </div>
    </div>
</body>
</html>
