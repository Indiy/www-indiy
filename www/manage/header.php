<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>MYARTISTDNA</title>
    <link href="css/styles.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery1.6.js"></script>
	<link href="blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
    <!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script> -->
    <!--[if IE]>
        <script src="js/html5.js"></script>
    <![endif]-->
<!--PLAYLIST STARTS-->
<script type="text/javascript"> 
$(document).ready(function(){	
	//Set default open/close settings
	$('.list').hide(); //Hide/close all containers
	<?
	if(!isset($_SESSION['tabOpen']) ||  $_SESSION['tabOpen']=='playlist'){?>
	$('.heading:first').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container
	<?}else{?>
	$('.<?=$_SESSION["tabOpen"]?> .heading').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container
	 <?}?>
	//On Click
	$('.heading').click(function(){
		if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
			$('.heading').removeClass('active').next().slideUp(); //Remove all .heading classes and slide up the immediate next container
			$(this).toggleClass('active').next().slideDown(); //Add .heading class to clicked trigger and slide down the immediate next container
		}
		return false; //Prevent the browser jump to the link anchor
	}); 
});
</script>
<!-- ADD BY ME 16-09-2011-->
<link href="facefiles/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="facefiles/facebox.js" type="text/javascript"></script>
<script src="color/jscolor.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[rel*=facebox]').facebox() ;	  
    })
</script>
<!--PLAYLIST ENDS-->
</head>
<body>
<section id="bgtopbar">
	<section id="topbar">
		<p>Logged in as <a href="#"><?php echo $_SESSION['sess_userName']; ?></a> | <a href="logout.php">Logout</a></p>
	</section>
</section>
<section id="headerinner">
<header>
    <h1><a href="#"><img src="images/MYARTISTDNA.png" alt="MYARTISTDNA"></a></h1>
	<nav>
    <ul>
        <?php 
            echo $_SESSION['sess_userType'];
        if( $_SESSION['sess_userType'] == 2 ) 
        {
            echo '<li><a class="active" href="dashboard.php">DASHBOARD</a></li>';
            echo '<li><a href="#">ADD ARTIST</a></li>';
            echo '<li><a href="#">ADD LABEL</a></li>';
            echo '<li class="nodivider"><a href="dashboard.php">BACK TO MAIN</a></li>';
         }
         else
         {
             $host_explode = explode(".", $_SERVER["HTTP_HOST"]);
             if( $host_explode[0] == $_SESSION['sess_userURL'] )
             {
                 $artist_home_host = $_SERVER["HTTP_HOST"];
             }
             else if( $host_explode[0] == 'www' )
             {
                 $artist_home_host = $_SESSION['sess_userURL'] . '.' . implode(".",array_slice($host_explode,1));
             }
             else
             {
                 $artist_home_host = $_SESSION['sess_userURL'] . $_SERVER["HTTP_HOST"];
             }
             //echo "<li class='active'><a href='/manage/artist_management.php?userId=".$_SESSION['sess_userId']."'>DASHBOARD</a></li>";
             echo "<li class='nodivider'>";
             echo "<a href='http://$artist_home_host'>VIEW MY SITE</a>";
             echo "</li>";
         }
         ?>
    </ul>
    </nav>
</header>
</section><!-- header -->