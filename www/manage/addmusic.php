<?php  require_once('../includes/config.php');
	if($_SESSION['sess_userId']=="")
	{
		header("location: index.php");
		exit();
	}
	include("../includes/functions.php");

	$database = "[p]musicplayer_audio";
    $_SESSION['tabOpen']='playlist';

	if ($_POST["WriteTags"] != "") {
		
		if ($_POST["id"] != "") {
			$row = mf(mq("select id,image,audio from {$database} where id='{$_POST[id]}'"));
			
			$old_logo = $row["image"];
			$old_sound = $row["audio"];
		}
		
		extract($_REQUEST);

		$audio_name = my($_POST["name"]);
		$audio_download = $_POST["download"];
		$audio_bgcolor = $_POST["bgcolor"];
		$audio_bgposition = $_POST["bgposition"];
		$audio_bgrepeat = $_POST["bgrepeat"];
		$audio_amazon = $_POST["amazon"];
		$audio_itunes = $_POST["itunes"];
		
		// Upload Image
		if(!empty($_FILES["logo"]["name"])){
			if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
				$audio_logo = $artistid."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
				@move_uploaded_file($_FILES['logo']['tmp_name'], '../artists/images/' . $audio_logo);
			} else {
				if ($old_logo != $audio_logo) {
					$audio_logo = $old_logo;
				}
			}
		}else{
			$audio_logo = $old_logo;
		}
		//echo "{'img':'<img src=artists/images/$audio_logo>'}";
		
		// Upload Audio
		if(!empty($_FILES["audio"]["name"]))
		{
			if (is_uploaded_file($_FILES["audio"]["tmp_name"])) {
				$ext = explode(".",$_FILES["audio"]["name"]);
				
				$filename = $artistid."_".strtolower(rand(11111,99999)."_song.");
				$audio_sound = $filename.$ext[count($ext)-1];
				$audio_sound_ogg = $filename."ogg";

				@move_uploaded_file($_FILES['audio']['tmp_name'], '../artists/audio/' . $audio_sound);
				@system('/usr/local/bin/ffmpeg -i /home/madcom/public_html/artists/audio/'.$audio_sound.'  -acodec libvorbis /home/madcom/public_html/artists/audio/'.$audio_sound_ogg);

				//$audio_sound = $audio_sound_ogg;
			} else {
				if ($old_sound != $audio_sound) {
					$audio_sound = $old_sound;
				}
			}
		}else{
				$audio_sound = $old_sound;
		}
		
		//INSERTING THE DATA
		$tables = "artistid|name|image|bgcolor|bgposition|bgrepeat|audio|download|amazon|itunes";
		$values = "{$artistid}|{$audio_name}|{$audio_logo}|{$audio_bgcolor}|{$audio_bgposition}|{$audio_bgrepeat}|{$audio_sound}|{$audio_download}|{$audio_amazon}|{$audio_itunes}";
		
		if ($_POST["id"] != "") {
			update($database,$tables,$values,"id",$_POST["id"]);
		} else {
			insert($database,$tables,$values);
		}
		
		$successMessage = "<div id='notify'>Success! You are being redirected...</div>";
		
		//showing the post value after the upload //	
		$postedValues['imageSource'] = "../artists/images/".$audio_logo;
		$postedValues['audio_sound'] = "../artists/audio/".$audio_sound;
		$postedValues['success'] = "1";
		
		$postedValues['postedValues'] = $_REQUEST;

		//echo '{"Name":"'.$audio_name.'","imageSource":"artists/images/'.$audio_logo.'","":"","audio_sound":"artists/audio/'.$audio_sound.'","success":1}';
		echo json_encode($postedValues);	
		exit;		

		refresh("1","?p=home");
	}
	
	if ($_GET["id"] != "") {
		$artistid=$_REQUEST['artist_id'];
		$row = mf(mq("select * from {$database} where id='{$_GET["id"]}' and artistid='{$artistid}'"));
		$audio_id = $row["id"];
		$audio_name = $row["name"];
		$audio_logo = $row["image"];
		$audio_bgcolor = $row["bgcolor"];
		$audio_bgrepeat = $row["bgrepeat"];
		$audio_bgposition = $row["bgposition"];
		$audio_sound = $row["audio"];
		$audio_download = $row["download"];
		$audio_amazon = $row["amazon"];
		$audio_itunes = $row["itunes"];
		$head_title = "Edit";
	}else{
		$head_title = "Add";
	}
	
	if ($audio_logo != "") {
		$audio_logo = '<img src="../artists/images/'.$audio_logo.'" style=" margin-top: 0px; height: 25px;" />';
	}
	
	if ($audio_download == "1") { $yesDownload = " checked"; } else { $noDownload = " checked"; }
	$audio_name = stripslashes($audio_name);
	
?>
	
    <link rel="stylesheet" media="screen" type="text/css" href="includes/css/layout.css" />
				
				
				<div id="popup">
					<?=$successMessage;?>
					<div class="addmusic">
						<h2 class="title"><?=$head_title?> Music</h2>
						<form id="ajax_from" method="post" enctype="multipart/form-data" action="addmusic.php" onsubmit='return onAddMusicSubmit();'>
						<input id='artist_id' type='hidden' value="<?=$_REQUEST['artist_id']?>" name="artistid">
						<input id='song_id' type='hidden' value="<?=$_REQUEST['id']?>" name="id">
						<div id="form_field">
							<div class="clear"></div>
							
							<label>Name</label>
							<input id='song_name' type="text" name="name" value="<?=$audio_name;?>" class="text" />
							<div class="clear"></div>
							
							<label>Image <?=$audio_logo;?></label>
							<input id='song_image' type="file" name="logo" class="text" />
							<div class="clear"></div>
							
							<label>Background Color</label>
							<input id='song_bgcolor' type="text" name="bgcolor" maxlength="6" size="6" class="color"  value="<?=$audio_bgcolor;?>" />
							<div class="clear"></div>
							
							<label>Background Position</label>
							<select id='song_bgposition' name="bgposition" class="text">
							<option value="">Select</option>
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
							<select id='song_bgrepeat' name="bgrepeat" class="text">
							<option value="">Select </option>
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
							<input id='song_audio' type="file" name="audio" class="text" /> <br><?=$audio_sound;?>
							<div class="clear"></div>
							
							<label>Free Download</label>
							<div class="floatbox">
							<input type="radio" name="download" value="1" class="radio"<?=$yesDownload;?> /> Yes
							<input type="radio" name="download" value="0" class="radio"<?=$noDownload;?> /> No
							</div>
							<div class="clear"></div>
							
							<label>Amazon MP3 URL</label>
							<input id='amazon_url' type="text" name="amazon" value="<?=$audio_amazon;?>" class="text" />
							<div class="clear"></div>
							
							<label>iTunes URL</label>
							<input id='itunes_url' type="text" name="itunes" value="<?=$audio_itunes;?>" class="text" />
							<div class="clear"></div>
							
							
							<input type="submit" name="WriteTags" value="submit" class="submit" />
                            
							
							<? if ($_GET["id"] != "") { ?>
							<!-- <p><br /><br />
							<a href="#" class="xdelete" onclick="confirmDelete('?p=home&delete=true&type=audio&a=<?=$artistid;?>&id=<?=$audio_id;?>')"><small>Delete</small></a></p> -->
							<? } ?>
						</div>
						<div id="form_message">
							<? if ($_GET["id"] != "") { ?>
								Your record was successfully updated!!!!
							<? }else{ ?>
								Your record was successfully added!!!!
							<?}?>
						</div>
						</form>
                        <div class="clear"></div>
                        <div id='status' class='form_status' style='display: none;'></div>
                        <div class="clear"></div>
                        <div id='upload_bar' style='display: none;'></div>
                        <div id='spinner' style='display: none;'>
                            <img src='/images/ajax-loader-white.gif'/>
                        </div>
					</div>

					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				<div id="sidebar">

				</div>
				<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>