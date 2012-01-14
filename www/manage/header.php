<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>MYARTISTDNA</title>

    <!-- Combo-handled YUI CSS files: --> 
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.9.0/build/assets/skins/sam/skin.css"> 

    <link href="css/styles.css" rel="stylesheet" type="text/css">
	<link href="blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>

    <!-- Combo-handled YUI JS files: --> 
    <script type="text/javascript" src="http://yui.yahooapis.com/combo?2.9.0/build/yahoo-dom-event/yahoo-dom-event.js&2.9.0/build/container/container_core-min.js&2.9.0/build/menu/menu-min.js&2.9.0/build/element/element-min.js&2.9.0/build/button/button-min.js&2.9.0/build/editor/editor-min.js"></script>

    <script src="js/tooltip_text.js" type="text/javascript"></script>
    <script src="js/manage.js" type="text/javascript"></script>
    <script src="/js/ZeroClipboard.js" type="text/javascript"></script>

    <!--[if IE]>
        <script src="js/html5.js"></script>
    <![endif]-->
<!--PLAYLIST STARTS-->
<script type="text/javascript"> 
$(document).ready(function(){	
	//Set default open/close settings
	$('.list').hide(); //Hide/close all containers
	<?
        $active_tab = 'branding_tips';
        if( isset($_SESSION['tabOpen']) )
            $active_tab = $_SESSION['tabOpen'];
        
    ?>
	$('.<?=$active_tab?> .heading').addClass('active').next().show(); 
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
<div id='mask' style='display: none;'></div>
<div id='link_tooltip'>
    <div class='link_url' id='link_url'>madna.co/aa_bb</div>
    <div class='link_copy_sep'>
        <div class='link_sep_bar'> </div>
        <div class='link_copy'>Copy</div>
    </div>
</div>
<div id='question_tooltip' style='display: none;'>
    Tooltip Text
</div>
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
        if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' ) 
        {
            echo '<li><a class="active" href="dashboard.php">DASHBOARD</a></li>';
            echo '<li><a href="add_user.php" rel="facebox[.bolder]">ADD ARTIST</a></li>';
            echo '<li class="nodivider"><a href="add_label.php" rel="facebox[.bolder]">ADD LABEL</a></li>';
        }
        else if( $_SESSION['sess_userType'] == 'LABEL' )
        {
            echo '<li><a class="active" href="dashboard.php">DASHBOARD</a></li>';
            echo '<li class="nodivider"><a href="add_user.php" rel="facebox[.bolder]">ADD ARTIST</a></li>';
        }
         else
         {
             $host = parse_url(trueSiteUrl(),PHP_URL_HOST);
             $host_explode = explode(".", $host);
             $artist_home_host = $_SESSION['sess_userURL'] . '.' . implode('.',array_slice($host_explode,1));
             
             $artist_id = $_SESSION['sess_userId'];
             
             echo "<li>";
             echo "<a href='http://$artist_home_host'>VIEW SITE</a>";
             echo "</li>";
             echo "<li>";
             echo "<a href='register.php?artist_id=$artist_id' rel='facebox[.bolder]'>EDIT PROFILE</a>";
             echo "</li>";
             echo "<li class='nodivider'>";
             echo "<a href='invite_friends.php?artist_id=$artist_id' rel='facebox[.bolder]'>INVITE FRIENDS</a>";
             echo "</li>";
         }
         ?>
    </ul>
    </nav>
</header>
</section><!-- header -->