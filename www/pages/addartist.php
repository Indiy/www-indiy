<?php if ($_SESSION["me"] == "") { 
		die("You must be logged in");
	}

	if (isLoggedIn() != "true") {
		if (isAdmin()) {
			
		} else {
			die("You must be logged in");
		}
	}

	$database = "[p]musicplayer";

	if ($_POST["submit"] != "") {
		
		if ($_SESSION["me"] != "") {
			$row = mf(mq("select id,logo from {$database} where id='{$_SESSION["me"]}'"));
			$old_logo = $row["logo"];
		}
		
		$artist_artist = my($_POST["artist"]);
		$artist_url = $_POST["url"];
		$artist_website = str_replace("http://", "", $_POST["website"]);
		$artist_twitter = str_replace("http://www.twitter.com", "", str_replace("http://www.twitter.com/", "", $_POST["twitter"]));
		$artist_facebook = str_replace("http://www.facebook.com", "", str_replace("http://www.facebook.com/", "", $_POST["facebook"]));
		//$artist_twitter = $_POST["twitter"];
		//$artist_facebook = $_POST["facebook"];
		$artist_appid = $_POST["appid"];
		$artist_email = $_POST["email"];
		$use_listens = $_POST["listens"];
		$artist_password = $_POST["newpass"];
		if ($artist_password == "" && $_SESSION["me"] != "") {
			$getpass = mf(mq("select password from [p]musicplayer where id='{$_SESSION["me"]}' limit 1"));
			$artist_password = $getpass["password"];
		} else {
			$artist_password = md5($artist_password);
		}

		//new added fields
		$artist_gender = $_POST["artist_gender"];
		$artist_language = $_POST["artist_language"];
		$artist_music_likes = $_POST["artist_music_likes"];
		$artist_location = $_POST["artist_location"];
		  
		
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

		$tables = "artist|url|logo|website|twitter|facebook|appid|email|password|root|listens|gender|languages|music_likes|location";
		$values = "{$artist_artist}|{$artist_url}|{$artist_logo}|{$artist_website}|{$artist_twitter}|{$artist_facebook}|{$artist_appid}|{$artist_email}|{$artist_password}|{$use_root}|{$use_listens}|{$artist_gender}|{$artist_language}|{$artist_music_likes}|{$artist_location}";
		
		if ($_SESSION["me"] != "") {
			update($database,$tables,$values,"id",$_SESSION["me"]);
		} else {
			insert($database,$tables,$values);
			// create store entry
			insert("[p]musicplayer_ecommerce","userid",mysql_insert_id());
		}
		
		$successMessage = "<div id='notify'>Success!</div>";
		refresh("1","?p=home");
	}
	
	if ($_SESSION["me"] != "") {

		$row = mf(mq("select * from {$database} where id='{$_SESSION["me"]}'"));
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
		$artist_listens = $row["listens"];
		$artist_gender = $row["gender"];
		$artist_lang = $row["languages"];
		$artist_password = $row["password"];
		$artist_music_likes = $row["music_likes"];
		//$artist_music_likes = substr($artist_music_likes,0,strlen($artist_music_likes)-1);
		$artist_location = $row["location"];
		
		if ($artist_listens == "1") {
			$showListens = " checked";
		} else {
			$hideListens = " checked";
		}

	}
	
	if ($artist_logo != "") {
		$artist_logo = '<img src="artists/images/'.$artist_logo.'" style="float: right; margin-top: 5px; height: 25px;" />';
	}
	
	$labels = mq("select id,artist from [p]musicplayer where type='3' order by artist asc");
	$labelList .= '<option value="">Select Label</option><option value=""></option>';
	while ($label = mf($labels)) {
		if ($artist_label == $label["id"] || $_GET["label"] == $label["id"]) { $selected = ' selected'; } else { $selected = ''; }
		
		$labelList .= '<option value="'.$label["id"].'"'.$selected.'>'.stripslashes($label["artist"]).'</option>';
	}
	
?>
				
<script>
	function validatefrm(obj)
	{
		if(!(obj.email.value))
		{
			alert("Please give us your email.");
			obj.email.value=""
			obj.email.focus();
			return false;	
		}
	}
</script>
				<div id="content">
					<?=$successMessage;?>
					<div class="post">
					<? if (isFan()) { ?>
						<h2 class="title">Account</h2>
					<? } else { ?>
						<h2 class="title">Artist Account</h2>
					<? } ?>
					
					<?php
					 if($row["facebook"] == '' || $row["twitter"] == '') {
					?>
						<div class="three sxsw" style="float:right; width: 400px !important;">
							<?php if($row["facebook"] == '') {?>
							<div style="float:right; padding: 5px;padding-left: 75px;">
								 <a href="/Login_Twitbook/index.php?login&oauth_provider=facebook"><img src="/includes/images/facebook.jpg" width="169" height="21" border="0" alt="facebook.jpg"></a>
							</div>
							<?php } ?>
							<div class="clear"></div>
							<?php if($row["twitter"] == '') {?>
							<div style="float:right; padding: 5px;">
								 <a href="/Login_Twitbook/index.php?login&oauth_provider=twitter"><img src="/includes/images/twitter.jpg" width="169" height="21" border="0" alt="facebook.jpg"></a>
							</div>
							<?php } ?>	
						</div>
					<?php } ?>	

					<?php
					if($artist_email=='' || $artist_password=='')
					{
					?>
						<div style="color:#003333;">Please give us your email and create a password in case you ever want to login with that information instead of your social network profile.</div>
						<div class="clear"></div>
					<?php
					}
					?>

						<form method="post" enctype="multipart/form-data"  onsubmit="return validatefrm(this)">
							<div class="clear"></div>
							
							<label>Name</label>
							<input type="text" name="artist" value="<?=$artist_artist;?>" class="text" />
							<div class="clear"></div>
							
							<label>Email</label>
							<input type="text" name="email" value="<?=$artist_email;?>" class="text" />
							<div class="clear"></div>

							<label>Gender</label>
							<input type="text" name="artist_gender" value="<?=$artist_gender;?>" class="text" />
							<div class="clear"></div>
								 
							<label>Language</label>
							<input type="text" name="artist_language" value="<?=$artist_lang;?>" class="text" />
							<div class="clear"></div>

							<label>Location</label>
							<input type="text" name="artist_location" value="<?=$artist_location;?>" class="text" />
							<div class="clear"></div>

							<label>Music likes</label>
							<input type="text" name="artist_music_likes" value="<?=$artist_music_likes;?>" class="text" />
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
							<input type="file" name="logo" class="text" /> <?//=$artist_logo;?>
							&nbsp;
							<?php
								if($row["oauth_provider"]=='facebook')
									echo '<img src="https://graph.facebook.com/'.$row["username"].'/picture" border="0" alt="" width="25px" height="25px">';
								elseif($row["oauth_provider"]=='twitter')
									echo '<img src="'.$row['profile_image_url'].'" border="0" alt="" width="25px" height="25px">';
							?>
							<div class="clear"></div>
							
							<label>Official Website</label>
							<input type="text" name="website" value="<?=$artist_website;?>" class="text" />
							<div class="clear"></div>

							<?php
							if($row['twitter_screen_name']!='')	
								$twitter_url_data = "http://www.twitter.com/".$row['twitter_screen_name']
							?>
							<label>Twitter URL</label>
							<input type="text" name="twitter" value="<?=$twitter_url_data;?>" class="text" readonly/>
							<div class="clear"></div>

							<?php
							if($row['facebook']!='')	
								$facebook_url_data = "http://www.facebook.com/".$row['facebook']
							?>
							<label>Facebook URL</label>
							<input type="text" name="facebook" value="<?=$facebook_url_data;?>" class="text" readonly/>
							<div class="clear"></div>
							
							<label>Facbook App ID</label>
							<input type="text" name="appid" value="<?=$artist_appid;?>" class="text" /> &nbsp;&nbsp;<small><a href="http://developers.facebook.com/" target="_blank">Get App ID</a></small>
							<div class="clear"></div>
							
							<label>Show "Listen" Count</label>
							<div class="group">
							<input type="radio" name="listens" value="1"<?=$showListens;?> class="radio" /> Yes<br />
							<input type="radio" name="listens" value="0"<?=$hideListens;?> class="radio" /> No<br />
							</div>
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