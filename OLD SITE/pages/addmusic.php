<?

	if (isLoggedIn() != "true") {
		if (isAdmin() || isLabel()) {
			
		} else {
			die("You must be logged in");
		}
	}
	
	$database = "[p]musicplayer_audio";

	if ($_POST["submit"] != "") {
		
		if ($_GET["id"] != "") {
			$row = mf(mq("select `id`,`image`,`audio` from `{$database}` where `id`='{$_GET["id"]}'"));
			$old_logo = $row["image"];
			$old_sound = $row["audio"];
		}
		
		$audio_name = my($_POST["name"]);
		$audio_download = $_POST["download"];
		$audio_bgcolor = $_POST["bgcolor"];
		$audio_bgposition = $_POST["bgposition"];
		$audio_bgrepeat = $_POST["bgrepeat"];
		
		// Upload Image
		if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
			$audio_logo = $_GET["artist"]."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
			@move_uploaded_file($_FILES['logo']['tmp_name'], 'artists/images/' . $audio_logo);
		} else {
			if ($old_logo != $audio_logo) {
				$audio_logo = $old_logo;
			}
		}
		
		// Upload Audio
		if (is_uploaded_file($_FILES["audio"]["tmp_name"])) {
			$audio_sound = $_GET["artist"]."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["audio"]["name"])));
			@move_uploaded_file($_FILES['audio']['tmp_name'], 'artists/audio/' . $audio_sound);
		} else {
			if ($old_sound != $audio_sound) {
				$audio_sound = $old_sound;
			}
		}
		
		$tables = "artistid|name|image|bgcolor|bgposition|bgrepeat|audio|download";
		$values = "{$_GET["artist"]}|{$audio_name}|{$audio_logo}|{$audio_bgcolor}|{$audio_bgposition}|{$audio_bgrepeat}|{$audio_sound}|{$audio_download}";
		
		if ($_GET["id"] != "") {
			update($database,$tables,$values,"id",$_GET["id"]);
		} else {
			insert($database,$tables,$values);
		}
		
		$successMessage = "<div id='notify'>Success! You are being redirected...</div>";
		refresh("1","?p=home");
	}
	
	if ($_GET["id"] != "") {

		$row = mf(mq("select * from `{$database}` where `id`='{$_GET["id"]}' and `artistid`='{$_GET["artist"]}'"));
		$audio_id = $row["id"];
		$audio_name = $row["name"];
		$audio_logo = $row["image"];
		$audio_bgcolor = $row["bgcolor"];
		$audio_bgrepeat = $row["bgrepeat"];
		$audio_bgposition = $row["bgposition"];
		$audio_sound = $row["audio"];
		$audio_download = $row["download"];

	}
	
	if ($audio_logo != "") {
		$audio_logo = '<img src="artists/images/'.$audio_logo.'" style="float: right; margin-top: 5px; height: 25px;" />';
	}
	
	if ($audio_download == "1") { $yesDownload = " checked"; } else { $noDownload = " checked"; }
	$audio_name = stripslashes($audio_name);
	
?>
	<link rel="stylesheet" href="includes/css/colorpicker.css" type="text/css" />
    <link rel="stylesheet" media="screen" type="text/css" href="includes/css/layout.css" />
	<script type="text/javascript" src="includes/js/jquery.js"></script>
	<script type="text/javascript" src="includes/js/colorpicker.js"></script>
    <script type="text/javascript" src="includes/js/eye.js"></script>
    <script type="text/javascript" src="includes/js/utils.js"></script>
    <script type="text/javascript" src="includes/js/layout.js?ver=1.0.2"></script>
				
				
				<div id="content">
					<?=$successMessage;?>
					<div class="post">
						<h2 class="title"><a href="#">Add Music</a></h2>
						<form method="post" enctype="multipart/form-data">
							<div class="clear"></div>
							
							<label>Name</label>
							<input type="text" name="name" value="<?=$audio_name;?>" class="text" />
							<div class="clear"></div>
							
							<label>Image</label>
							<input type="file" name="logo" class="text" /> <?=$audio_logo;?>
							<div class="clear"></div>
							
							<label>Background Color</label>
							<input type="text" name="bgcolor" maxlength="6" size="6" id="colorpickerField1" value="<?=$audio_bgcolor;?>" />
							<div class="clear"></div>
							
							
							<label>Background Position</label>
							<select name="bgposition" class="text">
							<option value="">Select Background Position</option>
							<option value=""></option>
							<?
								$positions = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");
								foreach ($positions as $position) {
									if ($audio_bgposition == $position) {
										$selected = " selected";
									} else {
										$selected = "";
									}
									echo "<option value='{$position}'{$selected}>".ucfirst($position)."</option>\n";
								}
							?>
							</select>
							<div class="clear"></div>
							
							<label>Background Repeat</label>
							<select name="bgrepeat" class="text">
							<option value="">Select Background Repeat Pattern</option>
							<option value=""></option>
							<?
								$colors = array("repeat","repeat-x","repeat-y","no-repeat","stretch");
								foreach ($colors as $color) {
									if ($audio_bgrepeat == $color) {
										$selected = " selected";
									} else {
										$selected = "";
									}
									echo "<option value='{$color}'{$selected}>".ucfirst($color)."</option>\n";
								}
							?>
							</select>
							<div class="clear"></div>
							
							<label>MP3 File</label>
							<input type="file" name="audio" class="text" /> <?=$audio_sound;?>
							<div class="clear"></div>
							
							<label>Free Download</label>
							<div class="floatbox">
							<input type="radio" name="download" value="1" class="radio"<?=$yesDownload;?> /> Yes
							<input type="radio" name="download" value="0" class="radio"<?=$noDownload;?> /> No
							</div>
							<div class="clear"></div>
							
							<label>Edit ID3 Tags</label>
							<div class="floatbox"><a href="javascript:;window.open('id3/demos/demo.write.php?Filename=%2Fhome%2Fmyartist%2Fpublic_html%2Fartists%2Faudio%2F<?=$audio_sound;?>','editor','width=450,height=555,top='+((screen.availheight/2)-(510/2))+',left='+((screen.availwidth/2)-(450/2))+',menubar=no,menubar=no,scrollbars=no,status=no,toolbar=no,location=no,directories=no,resizable=no');void(0);">Click Here to Edit</a></div>
							<div class="clear"></div>
							
							<input type="submit" name="submit" value="submit" class="submit" />
							
							<? if ($_GET["id"] != "") { ?>
							<p><br /><br />
							<a href="#" class="xdelete" onclick="confirmDelete('?p=home&delete=true&type=audio&a=<?=$_GET["artist"];?>&id=<?=$audio_id;?>')"><small>Delete</small></a></p>
							<? } ?>
						
						</form>
					</div>
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				<div id="sidebar">

				</div>
				<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>