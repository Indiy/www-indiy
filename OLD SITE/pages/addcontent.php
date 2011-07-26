<?

	if (isLoggedIn() != "true") {
		if (isAdmin() || isLabel()) {
			
		} else {
			die("You must be logged in");
		}
	}
	
	$database = "[p]musicplayer_content";

	if ($_POST["submit"] != "") {
		
		if ($_GET["id"] != "") {
			$row = mf(mq("select `id`,`image` from `{$database}` where `id`='{$_GET["id"]}'"));
			$old_logo = $row["image"];
		}
		
		$content_name = my($_POST["name"]);
		$content_video = $_POST["video"];
		$content_body = my($_POST["body"]);
		
		// Upload Image
		if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
			$content_logo = $_GET["artist"]."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
			@move_uploaded_file($_FILES['logo']['tmp_name'], 'artists/images/' . $content_logo);
		} else {
			if ($old_logo != $content_logo) {
				$content_logo = $old_logo;
			}
		}
		
		$tables = "artistid|name|image|video|body";
		$values = "{$_GET["artist"]}|{$content_name}|{$content_logo}|{$content_video}|{$content_body}";
		
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
		$content_id = $row["id"];
		$content_name = $row["name"];
		$content_logo = $row["image"];
		$content_video = $row["video"];
		$content_body = $row["body"];

	}
	
	if ($content_logo != "") {
		$content_logo = '<img src="artists/images/'.$content_logo.'" style="float: right; margin-top: 5px; height: 25px;" />';
	}
	
	$content_name = stripslashes($content_name);
	$content_body = stripslashes($content_body);
?>
				
				
				<div id="content">
					<?=$successMessage;?>
					<div class="post">
						<h2 class="title"><a href="#">Add Page</a></h2>
						<form method="post" enctype="multipart/form-data">
							<div class="clear"></div>
							
							<label>Name</label>
							<input type="text" name="name" value="<?=$content_name;?>" class="text" />
							<div class="clear"></div>
							
							<label>Image</label>
							<input type="file" name="logo" class="text" /> <?=$content_logo;?>
							<div class="clear"></div>
							
							<label>Video URL<br /><small>(Youtube or Vimeo)</small></label>
							<input type="text" name="video" value="<?=$content_video;?>" class="text" />
							<div class="clear"></div>
							
							<label>Body</label>
							<textarea name="body" class="textarea"><?=$content_body;?></textarea>
							<div class="clear"></div>
							
							<input type="submit" name="submit" value="submit" class="submit" />
							
							<? if ($_GET["id"] != "") { ?>
							<p><br /><br />
							<a href="#" class="xdelete" onclick="confirmDelete('?p=home&delete=true&type=content&a=<?=$_GET["artist"];?>&id=<?=$content_id;?>')"><small>Delete</small></a></p>
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