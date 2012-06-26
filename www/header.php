<?php
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>MyArtistDNA - BE HEARD, BE SEEN, BE INDEPENDENT</title>
    <meta name="description" CONTENT="MyArtistDNA empowers artists to be heard, be seen & be independent, by giving them the tools, technology & outlet needed to create a powerful web presence." />
    
     <meta name="keywords" content="photographer websites, brand websites, simple websites, band websites, music websites, artist websites, musician websites" />

    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon"  href="favicon.ico" />

    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet">
    <script src="http://vjs.zencdn.net/c/video.js"></script>

    <script type="text/javascript">
        var g_siteUrl = "<?=trueSiteUrl();?>";
    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>
        
<?php

    if( $thisPage=="art")
    { 
        echo '<script src="js/jquery.tools.js" type="text/javascript"></script>';
        echo '<script src="js/jquery.custom.js" type="text/javascript"></script>';
    }
	else
    {
        echo '<script src="js/popup.js" type="text/javascript"></script>';
        echo '<script src="/js/homepage_slideshow.js" type="text/javascript"></script>';
    }

?>
    
    <script src="/js/jquery.kwicks-1.5.1.js" type="text/javascript"></script>
    <script src="/js/search.js" type="text/javascript"></script>
    
    <script src="/js/login_signup.js" type="text/javascript"></script>
    <script src="/js/footer.js" type="text/javascript"></script>
</head>

<body>
<section id="header">
<header>

    <h1><a href="home.php"><img src="images/MYARTISTDNA.gif" alt="MYARTISTDNA"></a></h1>

    <nav>
        <ul>
            <li>
                <a href="be-heard.php">BENEFITS</a>
                <span>
                    <ul>
                        <li></li>
                        <li><a href="be-heard.php">BE HEARD</a></li>
                        <li><a href="be-seen.php">BE SEEN</a></li>
                        <li><a href="be-independent.php">BE INDEPENDENT</a></li>
           
                    </ul>
                </span>  
            </li>
            <li><a href="artist-kit.php" target="_self">ARTIST KIT</a></li>
            <li><a href="pricing.php" target="_self">PRICING</a></li>                       
            <li><a href="tour.php">ABOUT US</a></li>                     
            <li class="nodivider"><a class="login" href="#" onclick='showLogin();'>LOGIN</a></li>
        </ul>
        <div id="search">
            <div class="search_box">
                <input />
            </div>
            <div id="search_results" class="search_results"></div>
        </div>
    </nav>

</header>
</section><!-- header -->
</section><!-- header -->
