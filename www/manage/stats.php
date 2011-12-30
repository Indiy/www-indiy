<?
	require_once('../includes/config.php');
	include_once('../includes/functions.php');	

	if (isLoggedIn() != "true") {
		if (isAdmin()) {
			
		} else {
			die("You must be logged in");
		}
	}

	$id = $_REQUEST["userId"];
	
	// Player views
	$stats_player = mf(mq("select `views` from `[p]musicplayer` where `id`='{$id}'"));
	$total_player_views = $stats_player["views"];
	
	// Build Page Graph
	$loadpages = mq("select `views` from `[p]musicplayer_content` where `artistid`='{$id}' order by `order` asc, `id` desc");
	$c=1;
	while ($pa = mf($loadpages)) {
		$buildmax .= $pa["views"].",";
	}
	$size = 100 / max(num($loadpages),1);
	//$size = num($loadpages) / 100;
	$buildmax = explode(",", $buildmax);
	$max = max($buildmax);

	$loadpag = mq("select `id`,`name`,`artistid`,`order`,`views` from `[p]musicplayer_content` where `artistid`='{$id}' order by `order` asc, `id` desc");
	while ($pages = mf($loadpag)) {
		$page_id = $pages["id"];
		$page_views = $pages["views"];
		$page_percent = floor(($page_views / $max) * 100);
		$page_filler = floor(100 - $page_percent);
		$page_name = stripslashes($pages["name"]);
		$pageList .= '
			<div class="bar page" style="width: '.$size.'%; background-position: 0px '.$page_filler.'px !important;">'.$page_views.'</div>
		';
		$pageNames .= '
			<div style="width: '.$size.'%; float: left; text-align: center;">'.$page_name.'</div>
		';
		++$c;
	}

	
	// Build Song Graph
	$loadmuse = mq("select `views` from `[p]musicplayer_audio` where `artistid`='{$id}' and `type`='0' order by `order` asc, `id` desc");
	$c=1;
	while ($mu = mf($loadmuse)) {
		$buildmaxe .= $mu["views"].",";
	}
	$msize = 100 / max(num($loadmuse),1);
	$sizee = round(($msize) / 2, 2);
	$buildmaxe = explode(",", $buildmaxe);
	$maxe = max($buildmaxe);

	$loadmusic = mq("select * from `[p]musicplayer_audio` where `artistid`='{$id}' and `type`='0' order by `order` asc, `id` desc");
	while ($p = mf($loadmusic)) {
		$p_id = $p["id"];
		$p_views = $p["views"];
		$p_percent = floor(100 -($p_views / $maxe) * 100);
		
		$p_downloads = $p["download"];
		$p_dpercent = floor(100 - ($p_downloads / $maxe) * 100);
		
		$p_name = stripslashes($p["name"]);
		$songList .= '
			<div style="width: '.$msize.'%; height: 100px; float: left; text-align: center;">
				<div class="clear"></div>
				<div class="bar" style="width: 50%; background-position: 0px '.$p_percent.'px !important;">'.$p_views.'</div>
				<div class="bar download" style="width: 50%; background-position: 0px '.$p_dpercent.'px !important;">'.$p_downloads.'</div>
				<div class="clear"></div>
			</div>
		';
		$songNames .= '
			<div style="width: '.$msize.'%; float: left; text-align: center;">
				<small>'.$p_name.'</small>
			</div>
		';		
		++$c;
	}

	

	
	// Build Love Hate Stats
	$loadvotes = mq("select `id`,`name` from `[p]musicplayer_audio` where `artistid`='{$id}' order by `order` asc, `id` desc");
	
	while ($vote = mf($loadvotes)) {
	
		$v_id = $vote["id"];
		$v_name = stripslashes($vote["name"]);

		$loadvotestats = mq("select `vote` from `[p]musicplayer_votes` where `artistid`='{$id}' and `audioid`='{$v_id}'");
		$l=0;
		$h=0;
		while ($cv = mf($loadvotestats)) {
			if ($cv["vote"] == "1") { ++$l; }
			if ($cv["vote"] == "0") { ++$h; }
		}
		
		$lovehate .= '<div class="voteStats"><div class="voteName">'.$v_name.'</div><div class="voteResult">Love: '.$l.' / Hate: '.$h.'</div><div class="clear"></div></div>';

	}
	
	
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>MYARTISTDNA</title>

<!-- Stylesheet from old site -->
  <link rel="stylesheet" href="/includes/css/style.css" type="text/css" />
  <link rel="stylesheet" href="/includes/style.css" type="text/css" />
  <!-- Your Custom Stylesheet -->
  <link rel="stylesheet" href="/includes/css/custom.css" type="text/css" />
<!-- Stylesheet from old site END -->

<link href="css/styles.css" rel="stylesheet" type="text/css">
<link href="blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.jplayer.min.js"></script>

<!--[if IE]>
<script src="js/html5.js"></script>
<![endif]-->


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
<link href="facefiles/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="facefiles/facebox.js" type="text/javascript"></script>
<script src="color/jscolor.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
                       $('a[rel*=facebox]').facebox() ;	  
                       })
</script>
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
    if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' ) 
    {
        echo '<li class="nodivider"><a href="dashboard.php">DASHBOARD</a></li>';
    }
    else
    {
        $host = parse_url(trueSiteUrl(),PHP_URL_HOST);
        $host_explode = explode(".", $host);
        $artist_home_host = $_SESSION['sess_userURL'] . '.' . implode('.',array_slice($host_explode,1));
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
<!-- Page content -->
<div id="page">
<!-- Wrapper -->
<div class="wrapper">
<!-- Left column/section -->
<section class="column width6 first">					

				
				<div id="content">
					<div class="post">
						<h2 class="title"><a href="#">Analytics</a></h2>
					
						<h2>Total Player Views: <?=$total_player_views;?></h2>
						
						<h4>Page Views</h4>
						
							<div class="graph">
								<div style="height: 10px;">&nbsp;</div>
								<?=$pageList;?>
								<div class="clear"></div>
							</div>
							<div class="names">
								<?=$pageNames;?>
								<div class="clear"></div>
							</div>
							
						<h4>Song Plays and Downloads</h4>
						
							<div class="graph">
								<div style="height: 10px;">&nbsp;</div>
								<?=$songList;?>
								<div class="clear"></div>
							</div>
							<div class="names">
								<?=$songNames;?>
								<div class="clear"></div>
							</div>
							
							
						<h4>Love / Hate</h4>
						
							<div class="lovehate">
								<?=$lovehate;?>
								<div class="clear"></div>
							</div>

					</div>
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				
				
				
				<div id="sidebar">

				</div>
				<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>
                
</section>
</div>
<!-- End of Wrapper -->
</div>
<!-- End of Page content -->

<?php
	include('footer.php');
?>

    

