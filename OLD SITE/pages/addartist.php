<? 
	if (isLoggedIn() != "true") {
		if (isAdmin()) {
			
		} else {
			die("You must be logged in");
		}
	}

	$database = "[p]musicplayer";

	if ($_POST["submit"] != "") {
		
		if ($_GET["id"] != "") {
			$row = mf(mq("select `id`,`logo` from `{$database}` where `id`='{$_GET["id"]}'"));
			$old_logo = $row["logo"];
		}
		
		$artist_artist = my($_POST["artist"]);
		$artist_url = $_POST["url"];
		$artist_website = str_replace("http://", "", $_POST["website"]);
		$artist_twitter = str_replace("http://www.twitter.com", "", str_replace("http://www.twitter.com/", "", $_POST["twitter"]));
		$artist_facebook = str_replace("http://www.facebook.com", "", str_replace("http://www.facebook.com/", "", $_POST["facebook"]));
		$artist_appid = $_POST["appid"];
		$artist_email = $_POST["email"];
		$artist_password = $_POST["newpass"];
		if ($artist_password == "" && $_GET["id"] != "") {
			$getpass = mf(mq("select `password` from `[p]musicplayer` where `id`='{$_GET["id"]}' limit 1"));
			$artist_password = $getpass["password"];
		} else {
			$artist_password = md5($artist_password);
		}
		
		// Upload Image
		if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
			$artist_logo = strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
			@move_uploaded_file($_FILES['logo']['tmp_name'], 'artists/images/' . $artist_logo);
		} else {
			$artist_logo = $old_logo;
		}
		
		if (isFan()) {
			$artist_url = $artist_email;
		}
		
		if (isLabel()) { $use_root = me(); } else { $use_root = $_POST["label"]; }
		
		$tables = "artist|url|logo|website|twitter|facebook|appid|email|password|root";
		$values = "{$artist_artist}|{$artist_url}|{$artist_logo}|{$artist_website}|{$artist_twitter}|{$artist_facebook}|{$artist_appid}|{$artist_email}|{$artist_password}|{$use_root}";
		
		if ($_GET["id"] != "") {
			update($database,$tables,$values,"id",$_GET["id"]);
		} else {
			insert($database,$tables,$values);
			// create store entry
			insert("[p]musicplayer_ecommerce","userid",mysql_insert_id());
		}
		
		$successMessage = "<div id='notify'>Success!</div>";
		refresh("1","?p=home");
	}
	
	if ($_GET["id"] != "") {

		$row = mf(mq("select * from `{$database}` where `id`='{$_GET["id"]}'"));
		$artist_id = $row["id"];
		$artist_artist = stripslashes($row["artist"]);
		$artist_url = $row["url"];
		$artist_logo = $row["logo"];
		$artist_website = $row["website"];
		$artist_twitter = $row["twitter"];
		$artist_facebook = $row["facebook"];
		$artist_appid = $row["appid"];
		$artist_email = $row["email"];
		$artist_label = $row["root"];

	}
	
	if ($artist_logo != "") {
		$artist_logo = '<img src="artists/images/'.$artist_logo.'" style="float: right; margin-top: 5px; height: 25px;" />';
	}
	
	$labels = mq("select `id`,`artist` from `[p]musicplayer` where `type`='3' order by `artist` asc");
	$labelList .= '<option value="">Select Label</option><option value=""></option>';
	while ($label = mf($labels)) {
		if ($artist_label == $label["id"] || $_GET["label"] == $label["id"]) { $selected = ' selected'; } else { $selected = ''; }
		
		$labelList .= '<option value="'.$label["id"].'"'.$selected.'>'.stripslashes($label["artist"]).'</option>';
	}
	
?>
				
				
				<div id="content">
					<?=$successMessage;?>
					<div class="post">
					<? if (isFan()) { ?>
						<h2 class="title">Account</h2>
					<? } else { ?>
						<h2 class="title">Artist Account</h2>
					<? } ?>
						<form method="post" enctype="multipart/form-data">
							<div class="clear"></div>
							
							<label>Name</label>
							<input type="text" name="artist" value="<?=$artist_artist;?>" class="text" />
							<div class="clear"></div>
							
							<label>Email</label>
							<input type="text" name="email" value="<?=$artist_email;?>" class="text" />
							<div class="clear"></div>
							
							<? if (isAdmin()) { ?>
							<label>Label</label>
							<select name="label">
								<?=$labelList;?>
							</select>
							<div class="clear"></div>
							<? } ?>
							
							<? if (!isFan()) { ?>
							<label>URL</label>
							<input type="text" name="url" value="<?=$artist_url;?>" class="text" />
							<div class="clear"></div>
							
							<label>Logo</label>
							<input type="file" name="logo" class="text" /> <?=$artist_logo;?>
							<div class="clear"></div>
							
							<label>Official Website</label>
							<input type="text" name="website" value="<?=$artist_website;?>" class="text" />
							<div class="clear"></div>
							
							<label>Twitter URL</label>
							<input type="text" name="twitter" value="<?=$artist_twitter;?>" class="text" />
							<div class="clear"></div>
							
							<label>Facebook URL</label>
							<input type="text" name="facebook" value="<?=$artist_facebook;?>" class="text" />
							<div class="clear"></div>
							
							<label>Facbook App ID</label>
							<input type="text" name="appid" value="<?=$artist_appid;?>" class="text" /> &nbsp;&nbsp;<small><a href="http://developers.facebook.com/" target="_blank">Get App ID</a></small>
							<div class="clear"></div>
							<? } ?>
							
							<label>New Password</label>
							<input type="password" name="newpass" value="" class="text" />
							<div class="clear"></div>
							
							<input type="submit" name="submit" value="submit" class="submit" />
						
						</form>
					</div>
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				<div id="sidebar">

				</div>
				<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>