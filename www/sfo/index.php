<?
	include("includes/functions.php");
	include("includes/config.php");
	
	if ($_GET["delete"] != "") {
		$deleteID = $_GET["delete"];
		if ($_GET["artist"] == "true") {
			mq("delete from `[p]sfo_submissions` where `id`='{$deleteID}'");
		} else {
			mq("delete from `[p]sfo_artists` where `id`='{$deleteID}'");
		}
		$successMessage = "<div id='notify'>Successfully deleted!</div>";
	}
	
	if ($_GET["admin"] == "true") {
		$admin = "true";
		$adminAjx = "&delete=true";
	}
	
	if ($_POST["submit"] != "") {
		
		$audio_name = my($_POST["name"]);
		$audio_title = my($_POST["title"]);
		$audio_type = $_POST["email"];
		$audio_email = $_POST["email"];	
		// filetype
		$audio_filetype = $_FILES["file"]["type"];
		
		if ($audio_filetype == "audio/mp3") {	
			// Upload Image
			if (is_uploaded_file($_FILES["file"]["tmp_name"])) {
				$audio_file = strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["file"]["name"])));
				
				if ($audio_name == "admin") {
					@move_uploaded_file($_FILES['file']['tmp_name'], 'files/artists/'.$audio_file);
					insert("[p]sfo_artists","filename|type|name","{$audio_file}|{$audio_type}|{$audio_title}");
				} else {
					@move_uploaded_file($_FILES['file']['tmp_name'], 'files/submissions/'.$audio_file);
					insert("[p]sfo_submissions","filename|name|email|title","{$audio_file}|{$audio_name}|{$audio_email}|{$audio_title}");
				}
				$successMessage = "<div id='notify'>Successfully uploaded!</div>";
			}
		} else {
			$errorMessage = "<div id='notify' class='error'>You have upload an invalid file type, only mp3's are accepted</div>";
		}
	}
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'> 
<html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'> 
<head> 
 
<title>Searching For An Outlet</title> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<link rel="stylesheet" type="text/css" media="screen" title="screen" href="fonts/stylesheet.css"/> 
<link rel="stylesheet" type="text/css" media="screen" title="screen" href="style.css"/> 
<script type="text/javascript" src="js/jquery-1.5.min.js"></script> 
<script type="text/javascript" src="js/java.js"></script> 
<script type="text/javascript">
	$(document).ready(function(){
	
		setTimeout(function(){
			$("#notify").slideUp("slow", function () {});
			}, 
		3500);
	
		$('.page').hide();
		$('.aClose').hide();
		$('#load').hide();
		
		$('#upload').click(function(){
			$('#load').fadeIn("fast");
		});
			
		/* Close */
		$('.aClose').click(function() {
			$('.aClose').fadeOut();
			$('.page').fadeOut();
		});
			
			
		/* Lyrics */
		$(".lyrics").click(function() {
			$(".page").html("<div class='pageload'><img src='images/page-loader.gif' border='0' /></div>").fadeIn();
			$(".aClose").fadeIn();
			var load = "&type=lyrics<?=$adminAjx;?>";
			$.post("ajax.php", load, function(data) {
				$(".page").html(data);
			});
			return false;
		});
		
		/* Acapellas */
		$(".acapellas").click(function() {
			$(".page").html("<div class='pageload'><img src='images/page-loader.gif' border='0' /></div>").fadeIn();
			$(".aClose").fadeIn();
			var load = "&type=acapellas<?=$adminAjx;?>";
			$.post("ajax.php", load, function(data) {
				$(".page").html(data);
			});
			return false;
		});


		/* Beats */
		$(".beats").click(function() {
			$(".page").html("<div class='pageload'><img src='images/page-loader.gif' border='0' /></div>").fadeIn();
			$(".aClose").fadeIn();
			var load = "&type=beats<?=$adminAjx;?>";
			$.post("ajax.php", load, function(data) {
				$(".page").html(data);
			});
			return false;
		});
		
		<? if ($admin) { ?>
		/* Files */
		$(".files").click(function() {
			$(".page").html("<div class='pageload'><img src='images/page-loader.gif' border='0' /></div>").fadeIn();
			$(".aClose").fadeIn();
			var load = "&admin=true";
			$.post("ajax.php", load, function(data) {
				$(".page").html(data);
			});
			return false;
		});		
		<? } ?>
		

	});
</script>
</head> 
<body> 
<?
	
	echo $successMessage;
	echo $errorMessage;
?>


	
	<div id="load">
		<div id="loader">
			<p>Uploading File...</p><img src="images/ajax-loader.gif" border="0" />
		</div>
	</div>

	<div class="aClose"></div>
	<div class="page"></div>


	<a href="http://www.twitter.com"><div class="twitter"></div></a>
	<a href="http://www.facebook.com"><div class="facebook"></div></a>
	
	<div id="rocker"></div>
	
	<div id="wrapper"> 
		<div id="header"></div> 
		
		<div id="content"> 
			<ul class="nav">
				<li><a href="#" class="lyrics">Lyrics</a></li>
				<li><a href="#" class="acapellas">Acapellas</a></li>
				<li><a href="#" class="beats">Beats</a></li>
				<? if ($admin) { ?>
				<li><a href="#" class="files">Files</a></li>
				<? } ?>
			</ul>
			<div class="clear"></div>
			<div class="middle">
				Calling all <strong>producers</strong> and <strong>hitmakers</strong>. Upload your <strong>"original"</strong><br />
				composed music for a chance to have Young Chris lay a verse on <strong>YOUR</strong><br />
				song. Contest ends 5/1/11. Some rules apply.
			</div>
			<div class="form">
				<form method="post" action="" enctype="multipart/form-data">
					<div class="split">
						<? if ($admin) { ?>
							<input type="hidden" name="name" id="name" class="text" value="admin" />
						<? } else { ?>	
							<input type="text" name="name" id="name" class="text" value="Your Name:" onfocus="clickclear(this, 'Your Name:')" onblur="clickrecall(this, 'Your Name:')" />
						<? } ?>
						
						<? if ($admin) { ?>
							<select name="email" class="text">
								<option value="">-- Type --</option>
								<option value=""></option>
								<option value="lyrics">Lyrics</option>
								<option value="acapellas">Acapellas</option>
								<option value="beats">Beats</option>
							</select>
						<? } else { ?>
							<input type="text" name="email" id="email" class="text" value="Email Address:" onfocus="clickclear(this, 'Email Address:')" onblur="clickrecall(this, 'Email Address:')" />
						<? } ?>
						<input type="text" name="title" id="title" class="text" value="Track Title:" onfocus="clickclear(this, 'Track Title:')" onblur="clickrecall(this, 'Track Title:')" />
					</div>
					<div class="split">
						<input type="file" name="file" class="file" value="Your File:" />
						<input type="submit" name="submit" value="Submit Your Work!" class="submit" id="upload" />
					</div>
					<div class="clear"></div>
				</form>
		</div> 
		
		<div id="footer"> 
			<div class="left">
				By using this Site you agree to be bound by these Terms of Use.
			</div>
			
			<div class="right">
				Copyright &copy; <?=date("Y");?> <a href="http://www.myartistdna.com">MyArtistDNA.com</a>. Inc. All rights reserved. 
			</div> 
			
			<div class="clear"></div>
			
		</div> 
	</div>
	
</body> 
</html>