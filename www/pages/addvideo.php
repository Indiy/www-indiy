<?

	if (isLoggedIn() != "true") {
		if (isAdmin() || isLabel()) {
			
		} else {
			die("You must be logged in");
		}
	}
	
	$database = "[p]musicplayer_video";

	if ($_POST["WriteTags"] != "") {
		
		if ($_GET["id"] != "") {
			$row = mf(mq("select `id`,`image`,`audio` from `{$database}` where `id`='{$_GET["id"]}'"));
			$old_logo = $row["image"];
			$old_sound = $row["video"];
		}
		
		$audio_name = my($_POST["name"]);
		
		// Upload Image
		if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
			$audio_logo = $_SESSION["me"]."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
			@move_uploaded_file($_FILES['logo']['tmp_name'], 'artists/images/' . $audio_logo);
		} else {
			if ($old_logo != "") {
				$audio_logo = $old_logo;
			}
		}
		
		// Upload Audio
		if (is_uploaded_file($_FILES["audio"]["tmp_name"])) {
			$audio_sound = $_SESSION["me"]."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["audio"]["name"])));
			@move_uploaded_file($_FILES['audio']['tmp_name'], 'vid/' . $audio_sound);
		} else {
			if ($old_sound != "") {
				$audio_sound = $old_sound;
			}
		}
		
		$tables = "artistid|name|image|video";
		$values = "{$_SESSION["me"]}|{$audio_name}|{$audio_logo}|{$audio_sound}";
		
		if ($_GET["id"] != "") {
			update($database,$tables,$values,"id",$_GET["id"]);
		} else {
			insert($database,$tables,$values);
		}
		
		$successMessage = "<div id='notify'>Success! You are being redirected...</div>";
		refresh("1","?p=home");
	}
	
	if ($_GET["id"] != "") {

		$row = mf(mq("select * from `{$database}` where `id`='{$_GET["id"]}' and `artistid`='{$_SESSION["me"]}'"));
		$audio_id = $row["id"];
		$audio_name = $row["name"];
		$audio_logo = $row["image"];
		$audio_sound = $row["video"];
		
	}
	
	if ($audio_logo != "") {
		$audio_logo = '<img src="artists/images/'.$audio_logo.'" style="float: right; margin-top: 5px; height: 25px;" />';
	}
	
	if ($audio_download == "1") { $yesDownload = " checked"; } else { $noDownload = " checked"; }
	$audio_name = stripslashes($audio_name);
	
?>

				<div id="content">
					<?=$successMessage;?>
					<div class="post">
						<h2 class="title"><a href="#">Add Video</a></h2>
						<form method="post" enctype="multipart/form-data">
							<div class="clear"></div>
							
							<label>Name</label>
							<input type="text" name="name" value="<?=$audio_name;?>" class="text" />
							<div class="clear"></div>
							
							<label>Default Image</label>
							<input type="file" name="logo" class="text" /> <?=$audio_logo;?>
							<div class="clear"></div>
							
							<label>Video (flv or mp4)</label>
							<input type="file" name="audio" class="text" /> <?=$audio_sound;?>
							<div class="clear"></div>
							
							<input type="submit" name="WriteTags" value="submit" class="submit" />
							
							<? if ($_GET["id"] != "") { ?>
							<p><br /><br />
							<a href="#" class="xdelete" onclick="confirmDelete('?p=home&delete=true&type=audio&a=<?=$_SESSION["me"];?>&id=<?=$audio_id;?>')"><small>Delete</small></a>
							</p>
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