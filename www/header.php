<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>MyArtistDNA - BE HEARD, BE SEEN, BE INDEPENDENT</title>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    
    <link rel="icon"  href="favicon.ico" />

    <script type="text/javascript">
    
    var g_siteUrl = "<?=trueSiteUrl();?>";
    
    </script>

    <!--[if IE]>
        <script src="js/html5.js"></script>
    <![endif]-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
        
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
    
    <!--SEARCH-->
    <script src="/js/jquery.kwicks-1.5.1.js" type="text/javascript"></script>
    <script src="/jplayer/js/ra_controls.js" type="text/javascript"></script>
    <script src="/jplayer/js/index.js" type="text/javascript"></script>

    <script src="/js/login_signup.js" type="text/javascript"></script>
</head>

<body>
<section id="header">
<header>

    <h1><a href="index.php"><img src="images/MYARTISTDNA.gif" alt="MYARTISTDNA"></a></h1>

    <nav>
    <ul>
    <li><a href="artists.php">ARTISTS</a>
		<span>
        <ul>
        <li><a href="artists.php">All</a></li>
        <li><a href="music.php">MUSIC</a></li>
        <li><a href="art.php">ART</a></li>
        </ul>
		</span>
    </li>                      
    <li><a href="http://discover.myartistdna.fm" target="_self">DISCOVER</a></li>                       
    <li><a href="be-heard.php">BENEFITS</a><span>
        <ul>
        <li></li>
        <li><a href="be-heard.php">BE HEARD</a></li>
        <li><a href="be-seen.php">BE SEEN</a></li>
        <li><a href="be-independent.php">BE INDEPENDENT</a></li>
       
        </ul>
		</span>     
    
    
    <li><a href="tour.php">What Is It?</a></li>                     
    <li class="nodivider"><a class="login" href="#" onclick='showLogin();'>LOGIN</a></li>
    </ul>
    <div id="theSearchBox">  </div>
    <!--<div class="search">
    <fieldset>
    <input name="" value="SEARCH" type="text" class="input" />
    <input name="" type="image" src="images/icon_search.gif" class="button">
    </fieldset>
    </div>-->
    </nav>

</header>
</section><!-- header -->
</section><!-- header -->
