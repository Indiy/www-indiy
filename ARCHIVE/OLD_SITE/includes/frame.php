<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?=siteTitle();?></title>
<meta name="description" content="Administry - Admin Template by Zoran Juric" />
<meta name="keywords" content="Admin,Template" />
<!-- We need to emulate IE7 only when we are to use excanvas -->
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<![endif]-->
<!-- Favicons --> 
<link rel="shortcut icon" type="image/png" HREF="<?=trueSiteUrl();?>/includes/img/favicons/favicon.png"/>
<link rel="icon" type="image/png" HREF="<?=trueSiteUrl();?>/includes/img/favicons/favicon.png"/>
<link rel="apple-touch-icon" HREF="<?=trueSiteUrl();?>/includes/img/favicons/apple.png" />
<!-- Main Stylesheet --> 
<link rel="stylesheet" href="<?=trueSiteUrl();?>/includes/css/style.css" type="text/css" />
<link rel="stylesheet" href="<?=trueSiteUrl();?>/includes/style.css" type="text/css" />
<!-- Your Custom Stylesheet --> 
<link rel="stylesheet" href="<?=trueSiteUrl();?>/includes/css/custom.css" type="text/css" />
<!-- jQuery with plugins -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.js"></script> 

<script type="text/javascript">
	// Confirm Delete
    function confirmDelete(url) {
        if (confirm('Are you sure you want to delete this? This will forever remove it from the database.')) {
          self.location=url;
        }
       	return false;
    }

	$(document).ready(function(){
			setTimeout(function(){ $('#notify').slideToggle('slow'); }, 2000);
			// $("#response").hide();
			
			$(function() {
				$("ul.playlist").sortable({opacity: 0.8, cursor: 'move', update: function() {
					$("#response").html("Loading...");
						var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_audio';
						$.post("includes/ajax.php", order, function(theResponse){
							$("#response").html(theResponse);
						});
					}
				});
			});

			$(function() {
				$("ul.pages").sortable({opacity: 0.8, cursor: 'move', update: function() {
					$("#response").html("Loading...");
						var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_content';
						$.post("includes/ajax.php", order, function(theResponse){
							$("#response").html(theResponse);
						});
					}
				});
			});
			
			$(function() {
				$("ul.products").sortable({opacity: 0.8, cursor: 'move', update: function() {
					$("#response").html("Loading...");
						var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_ecommerce_products';
						$.post("includes/ajax.php", order, function(theResponse){
							$("#response").html(theResponse);
						});
					}
				});
			});
			
			
			/* View Artist Tracks */
			$('a.viewartist').click(function() {
				var artist = $(this).text();
				var post = '&musiclist=true&fromartist='+artist;
				$.post("includes/ajax.php", post, function(respond){
					$("ul.listtracks").html(respond);
				});
				return false;
			});

	});
</script>
</head>
<body>
	<!-- Header -->
	<header id="top">
		<div class="wrapper">
			<!-- Title/Logo - can use text instead of image -->
			<div id="title"><img SRC="<?=trueSiteUrl();?>/includes/img/mylogo-v2.png" alt="Administry" /><!--<span>Administry</span> demo--></div>
			<!-- Top navigation -->
			<div id="topnav">
				Logged in as <b><?=username();?></b> <span>|</span> <a href="?p=addartist&id=<?=me();?>">Account</a> <span>|</span> <a href="?p=index&logout=true">Logout</a><br />
			</div>
			<!-- End of Top navigation -->
			<!-- Main navigation -->
			<nav id="menu">
				<ul class="sf-menu">
					<li><a href="?p=home">Dashboard</a></li>
					<? if (isAdmin() || isLabel()) { ?>
					<li><a href="?p=addartist">Add Artist</a></li>
					<? } ?>
					<? if (isAdmin()) { ?>
					<li><a href="?p=addlabel">Add Label</a></li>
					<? } ?>
					<li><a href="<?=siteUrl();?>">Back to Main</a></li>
				</ul>
			</nav>
			<!-- End of Main navigation -->
		</div>
	</header>
	<!-- End of Header -->
	<!-- Page title -->
	<div id="pagetitle">
		<div class="wrapper">
			<h1>Welcome to the <?=siteTitle();?> Admin</h1>
		</div>
	</div>
	<!-- End of Page title -->

	<!-- Page content -->
	<div id="page">
		<!-- Wrapper -->
		<div class="wrapper">
				<!-- Left column/section -->
				<section class="column width6 first">					

				{inject}
					
				</section>
		</div>
		<!-- End of Wrapper -->
	</div>
	<!-- End of Page content -->

	<!-- Page footer -->
	<footer id="bottom">
		<div class="wrapper">
			<nav>
				<a href="?p=home">Dashboard</a> &middot; 
				<a href="?p=addartist">Add Artist</a> &middot; 
				<a href="/myaudioplayer/">Back to Main</a>
			</nav>
			<p>&copy; <?=date("Y");?> MyArtistDNA.fm</p>
		</div>
	</footer>
	<!-- End of Page footer -->


	<!-- Scroll to top link -->
	<a href="#" id="totop">^ scroll to top</a>

<!-- User interface javascript load -->
<script type="text/javascript" SRC="<?=trueSiteUrl();?>/includes/js/administry.js"></script>
</body>
</html>