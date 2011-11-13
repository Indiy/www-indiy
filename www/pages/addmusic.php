<?

	if (isLoggedIn() != "true") {
		if (isAdmin() || isLabel()) {
			
		} else {
			die("You must be logged in");
		}
	}
	
	$database = "[p]musicplayer_audio";

	if ($_POST["WriteTags"] != "") {
		
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
		$audio_amazon = $_POST["amazon"];
		$audio_itunes = $_POST["itunes"];
		
		// Upload Image
		if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
			$audio_logo = $_SESSION["me"]."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
			@move_uploaded_file($_FILES['logo']['tmp_name'], 'artists/images/' . $audio_logo);
		} else {
			if ($old_logo != $audio_logo) {
				$audio_logo = $old_logo;
			}
		}
		
		// Upload Audio
		if (is_uploaded_file($_FILES["audio"]["tmp_name"])) {
			$audio_sound = $_SESSION["me"]."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["audio"]["name"])));
			@move_uploaded_file($_FILES['audio']['tmp_name'], 'artists/audio/' . $audio_sound);
		} else {
			if ($old_sound != $audio_sound) {
				$audio_sound = $old_sound;
			}
		}
		
		$tables = "artistid|name|image|bgcolor|bgposition|bgrepeat|audio|download|amazon|itunes";
		$values = "{$_SESSION["me"]}|{$audio_name}|{$audio_logo}|{$audio_bgcolor}|{$audio_bgposition}|{$audio_bgrepeat}|{$audio_sound}|{$audio_download}|{$audio_amazon}|{$audio_itunes}";
		
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
		$audio_bgcolor = $row["bgcolor"];
		$audio_bgrepeat = $row["bgrepeat"];
		$audio_bgposition = $row["bgposition"];
		$audio_sound = $row["audio"];
		$audio_download = $row["download"];
		$audio_amazon = $row["amazon"];
		$audio_itunes = $row["itunes"];

	}
	
	if ($audio_logo != "") {
		$audio_logo = '<img src="artists/images/'.$audio_logo.'" style="float: right; margin-top: 5px; height: 25px;" />';
	}
	
	if ($audio_download == "1") { $yesDownload = " checked"; } else { $noDownload = " checked"; }
	$audio_name = stripslashes($audio_name);
	
?>
	<link rel="stylesheet" href="includes/css/colorpicker.css" type="text/css" />
    <link rel="stylesheet" media="screen" type="text/css" href="includes/css/layout.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
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
							
							<label>Amazon MP3 URL</label>
							<input type="text" name="amazon" value="<?=$audio_amazon;?>" class="text" />
							<div class="clear"></div>
							
							<label>iTunes URL</label>
							<input type="text" name="itunes" value="<?=$audio_itunes;?>" class="text" />
							<div class="clear"></div>
							
							<? if ($_GET["id"] != "") { ?>
							<label>Edit ID3 Tags</label>
							<div class="floatbox">
								<!-- <iframe src="id3/demos/demo.write.php?Filename=%2Fhome%2Fmyartist%2Fpublic_html%2Fartists%2Faudio%2F<?=$audio_sound;?>" frameborder="0" width="450" height="555"></iframe></div> -->
<?
$TaggingFormat = 'UTF-8';

//header('Content-Type: text/html; charset='.$TaggingFormat);
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
//echo '<html><head><title>ID3 Tag Editor</title></head><style type="text/css">BODY,TD,TH { font-family: sans-serif; font-size: 9pt;" }</style><body>';

require_once($_SERVER["DOCUMENT_ROOT"].'/id3/getid3/getid3.php');
// Initialize getID3 engine
$getID3 = new getID3;
$getID3->setOption(array('encoding'=>$TaggingFormat));

getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);

$browsescriptfilename = 'demo.browse.php';

//$Filename = (isset($_REQUEST['Filename']) ? $_REQUEST['Filename'] : '');
$Filename = "/home/myartist/public_html/artists/audio/".$audio_sound;



if (isset($_POST['WriteTags'])) {

	$TagFormatsToWrite = (isset($_POST['TagFormatsToWrite']) ? $_POST['TagFormatsToWrite'] : array());
	if (!empty($TagFormatsToWrite)) {
		echo 'Starting to write tag(s)<BR>';

		$tagwriter = new getid3_writetags;
		$tagwriter->filename       = $Filename;
		$tagwriter->tagformats     = $TagFormatsToWrite;
		$tagwriter->overwrite_tags = true;
		$tagwriter->tag_encoding   = $TaggingFormat;
		if (!empty($_POST['remove_other_tags'])) {
			$tagwriter->remove_other_tags = true;
		}

		$commonkeysarray = array('Title', 'Artist', 'Album', 'Year', 'Comment');
		foreach ($commonkeysarray as $key) {
			if (!empty($_POST[$key])) {
				$TagData[strtolower($key)][] = $_POST[$key];
			}
		}
		if (!empty($_POST['Genre'])) {
			$TagData['genre'][] = $_POST['Genre'];
		}
		if (!empty($_POST['GenreOther'])) {
			$TagData['genre'][] = $_POST['GenreOther'];
		}
		if (!empty($_POST['Track'])) {
			$TagData['track'][] = $_POST['Track'].(!empty($_POST['TracksTotal']) ? '/'.$_POST['TracksTotal'] : '');
		}

		if (!empty($_FILES['userfile']['tmp_name'])) {
			if (in_array('id3v2.4', $tagwriter->tagformats) || in_array('id3v2.3', $tagwriter->tagformats) || in_array('id3v2.2', $tagwriter->tagformats)) {
				if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
					ob_start();
					if ($fd = fopen($_FILES['userfile']['tmp_name'], 'rb')) {
						ob_end_clean();
						$APICdata = fread($fd, filesize($_FILES['userfile']['tmp_name']));
						fclose ($fd);

						list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($_FILES['userfile']['tmp_name']);
						$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
						if (isset($imagetypes[$APIC_imageTypeID])) {

							$TagData['attached_picture'][0]['data']          = $APICdata;
							$TagData['attached_picture'][0]['picturetypeid'] = $_POST['APICpictureType'];
							$TagData['attached_picture'][0]['description']   = $_FILES['userfile']['name'];
							$TagData['attached_picture'][0]['mime']          = 'image/'.$imagetypes[$APIC_imageTypeID];

						} else {
							echo '<b>invalid image format (only GIF, JPEG, PNG)</b><br>';
						}
					} else {
						$errormessage = ob_get_contents();
						ob_end_clean();
						echo '<b>cannot open '.$_FILES['userfile']['tmp_name'].'</b><br>';
					}
				} else {
					echo '<b>!is_uploaded_file('.$_FILES['userfile']['tmp_name'].')</b><br>';
				}
			} else {
				echo '<b>WARNING:</b> Can only embed images for ID3v2<br>';
			}
		}

		$tagwriter->tag_data = $TagData;
		if ($tagwriter->WriteTags()) {
			echo 'Successfully wrote tags. <a href="javascript:window.close();">Close This Window</a><BR>';
			if (!empty($tagwriter->warnings)) {
				echo 'There were some warnings:<BLOCKQUOTE STYLE="background-color:#FFCC33; padding: 10px;">'.implode('<BR><BR>', $tagwriter->warnings).'</BLOCKQUOTE>';
			}
		} else {
			echo 'Failed to write tags!<BLOCKQUOTE STYLE="background-color:#FF9999; padding: 10px;">'.implode('<BR><BR>', $tagwriter->errors).'</BLOCKQUOTE>';
		}

	} else {

		echo 'WARNING: no tag formats selected for writing - nothing written';

	}
	echo '<HR>';

}


//echo '<div style="font-size: 1.2em; font-weight: bold;">Sample tag editor/writer</div>';
//echo '<a href="'.htmlentities($browsescriptfilename.'?listdirectory='.rawurlencode(realpath(dirname($Filename))), ENT_QUOTES).'">Browse current directory</a><br>';
if (!empty($Filename)) {
	//echo '<a href="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'">Start Over</a><br><br>';
	//echo '<form action="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'" method="post" enctype="multipart/form-data">';
	echo '<table border="3" cellspacing="0" cellpadding="4"><input type="hidden" name="Filename" value="'.htmlentities($Filename, ENT_QUOTES).'">';
	//echo '<tr><th align="right">Filename:</th><td><input type="hidden" name="Filename" value="'.htmlentities($Filename, ENT_QUOTES).'"><a href="'.htmlentities($browsescriptfilename.'?filename='.rawurlencode($Filename), ENT_QUOTES).'" target="_blank">'.$Filename.'</a></td></tr>';
	if (file_exists($Filename)) {

		// Initialize getID3 engine
		$getID3 = new getID3;
		$OldThisFileInfo = $getID3->analyze($Filename);
		getid3_lib::CopyTagsToComments($OldThisFileInfo);

		switch ($OldThisFileInfo['fileformat']) {
			case 'mp3':
			case 'mp2':
			case 'mp1':
				$ValidTagTypes = array('id3v1', 'id3v2.3', 'ape');
				break;

			case 'mpc':
				$ValidTagTypes = array('ape');
				break;

			case 'ogg':
				if (!empty($OldThisFileInfo['audio']['dataformat']) && ($OldThisFileInfo['audio']['dataformat'] == 'flac')) {
					//$ValidTagTypes = array('metaflac');
					// metaflac doesn't (yet) work with OggFLAC files
					$ValidTagTypes = array();
				} else {
					$ValidTagTypes = array('vorbiscomment');
				}
				break;

			case 'flac':
				$ValidTagTypes = array('metaflac');
				break;

			case 'real':
				$ValidTagTypes = array('real');
				break;

			default:
				$ValidTagTypes = array();
				break;
		}
		echo '<tr><td align="right"><b>Title</b></td> <td><input type="text" size="40" name="Title"  value="'.htmlentities((!empty($OldThisFileInfo['comments']['title'])  ? implode(', ', $OldThisFileInfo['comments']['title'] ) : ''), ENT_QUOTES).'"></td></tr>';
		echo '<tr><td align="right"><b>Artist</b></td><td><input type="text" size="40" name="Artist" value="'.htmlentities((!empty($OldThisFileInfo['comments']['artist']) ? implode(', ', $OldThisFileInfo['comments']['artist']) : ''), ENT_QUOTES).'"></td></tr>';
		echo '<tr><td align="right"><b>Album</b></td> <td><input type="text" size="40" name="Album"  value="'.htmlentities((!empty($OldThisFileInfo['comments']['album'])  ? implode(', ', $OldThisFileInfo['comments']['album'] ) : ''), ENT_QUOTES).'"></td></tr>';
		echo '<tr><td align="right"><b>Year</b></td>  <td><input type="text" size="4"  name="Year"   value="'.htmlentities((!empty($OldThisFileInfo['comments']['year'])   ? implode(', ', $OldThisFileInfo['comments']['year']  ) : ''), ENT_QUOTES).'"></td></tr>';

		$TracksTotal = '';
		$TrackNumber = '';
		if (!empty($OldThisFileInfo['comments']['track_number']) && is_array($OldThisFileInfo['comments']['track_number'])) {
			$RawTrackNumberArray = $OldThisFileInfo['comments']['track_number'];
		} elseif (!empty($OldThisFileInfo['comments']['track']) && is_array($OldThisFileInfo['comments']['track'])) {
			$RawTrackNumberArray = $OldThisFileInfo['comments']['track'];
		} else {
			$RawTrackNumberArray = array();
		}
		foreach ($RawTrackNumberArray as $key => $value) {
			if (strlen($value) > strlen($TrackNumber)) {
				// ID3v1 may store track as "3" but ID3v2/APE would store as "03/16"
				$TrackNumber = $value;
			}
		}
		if (strstr($TrackNumber, '/')) {
			list($TrackNumber, $TracksTotal) = explode('/', $TrackNumber);
		}
		echo '<tr><td align="right"><b>Track</b></td><td><input type="text" size="2" name="Track" value="'.htmlentities($TrackNumber, ENT_QUOTES).'"> of <input type="text" size="2" name="TracksTotal" value="'.htmlentities($TracksTotal, ENT_QUOTES).'"></TD></TR>';

		$ArrayOfGenresTemp = getid3_id3v1::ArrayOfGenres();   // get the array of genres
		foreach ($ArrayOfGenresTemp as $key => $value) {      // change keys to match displayed value
			$ArrayOfGenres[$value] = $value;
		}
		unset($ArrayOfGenresTemp);                            // remove temporary array
		unset($ArrayOfGenres['Cover']);                       // take off these special cases
		unset($ArrayOfGenres['Remix']);
		unset($ArrayOfGenres['Unknown']);
		$ArrayOfGenres['']      = '- Unknown -';              // Add special cases back in with renamed key/value
		$ArrayOfGenres['Cover'] = '-Cover-';
		$ArrayOfGenres['Remix'] = '-Remix-';
		asort($ArrayOfGenres);                                // sort into alphabetical order
		echo '<tr><th align="right">Genre</th><td><select name="Genre">';
		$AllGenresArray = (!empty($OldThisFileInfo['comments']['genre']) ? $OldThisFileInfo['comments']['genre'] : array());
		foreach ($ArrayOfGenres as $key => $value) {
			echo '<option value="'.htmlentities($key, ENT_QUOTES).'"';
			if (in_array($key, $AllGenresArray)) {
				echo ' selected="selected"';
				unset($AllGenresArray[array_search($key, $AllGenresArray)]);
				sort($AllGenresArray);
			}
			echo '>'.htmlentities($value).'</option>';
		}
		echo '</select><input type="text" name="GenreOther" size="10" value="'.htmlentities((!empty($AllGenresArray[0]) ? $AllGenresArray[0] : ''), ENT_QUOTES).'"></td></tr>';

		echo '<tr><td align="right"><b>Write Tags</b></td><td>';
		foreach ($ValidTagTypes as $ValidTagType) {
			echo '<input type="checkbox" name="TagFormatsToWrite[]" value="'.$ValidTagType.'"';
			if (count($ValidTagTypes) == 1) {
				echo ' checked="checked"';
			} else {
				switch ($ValidTagType) {
					case 'id3v2.2':
					case 'id3v2.3':
					case 'id3v2.4':
						if (isset($OldThisFileInfo['tags']['id3v2'])) {
							echo ' checked="checked"';
						}
						break;

					default:
						if (isset($OldThisFileInfo['tags'][$ValidTagType])) {
							echo ' checked="checked"';
						}
						break;
				}
			}
			echo '>'.$ValidTagType.'<br>';
		}
		if (count($ValidTagTypes) > 1) {
			echo '<hr><input type="checkbox" name="remove_other_tags" value="1"> Remove non-selected tag formats when writing new tag<br>';
		}
		echo '</td></tr>';

		echo '<tr><td align="right"><b>Comment</b></td><td><textarea cols="30" rows="3" name="Comment" wrap="virtual">'.((isset($OldThisFileInfo['comments']['comment']) && is_array($OldThisFileInfo['comments']['comment'])) ? implode("\n", $OldThisFileInfo['comments']['comment']) : '').'</textarea></td></tr>';

		echo '<tr><td align="right"><b>Picture</b><br>(ID3v2 only)</td><td><input type="file" name="userfile" accept="image/jpeg, image/gif, image/png"><br>';
		echo '<select name="APICpictureType">';
		$APICtypes = getid3_id3v2::APICPictureTypeLookup('', true);
		foreach ($APICtypes as $key => $value) {
			echo '<option value="'.htmlentities($key, ENT_QUOTES).'">'.htmlentities($value).'</option>';
		}
		echo '</select></td></tr>';
		//echo '<tr><td align="center" colspan="2"><input type="submit" name="WriteTags" value="Save Changes"> ';
		//echo '<input type="reset" value="Reset"> <input type="button" value="Close this window" onclick="self.close()">';
		//echo '</td></tr>';

	} else {

		echo '<tr><td align="right"><b>Error</b></td><td>'.htmlentities($Filename).' does not exist</td></tr>';

	}
	echo '</table>';
	//echo '</form>';

}
?>


								
							<div class="clear"></div>
							<? } ?>
							
							<input type="submit" name="WriteTags" value="submit" class="submit" />
							
							<? if ($_GET["id"] != "") { ?>
							<p><br /><br />
							<a href="#" class="xdelete" onclick="confirmDelete('?p=home&delete=true&type=audio&a=<?=$_SESSION["me"];?>&id=<?=$audio_id;?>')"><small>Delete</small></a></p>
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