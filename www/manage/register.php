<?php  require_once('../includes/config.php');
	if($_SESSION['sess_userId']=="")
	{
		header("location: index.php");
		exit();
	}
	include("../includes/functions.php");

	$database = "[p]musicplayer";
    $_SESSION['tabOpen']='playlist';

	if ($_POST["WriteTags"] != "") {
		extract($_REQUEST);	
		//if ($_SESSION["sess_userId"] != "") {
			$row = mf(mq("select id,logo,password from {$database} where id='{$artistid}'"));
		//	echo "select id,logo,password from {$database} where id='{$_SESSION["sess_userId"]}'";
			$old_logo = $row["logo"];
			$old_pwd = $row["password"];
		//}
		
	

		$artist = my($_POST["artist"]);
		$email = $_POST["email"];
		$gender = $_POST["artist_gender"];
		$languages = $_POST["artist_languages"];
		$location = $_POST["artist_location"];
		$music_likes  = $_POST["artist_music_likes"];
		$url  = $_POST["url"];
		$website = $_POST["website"];
		$twitter = $_POST["twitter"];
		$facebook = $_POST["facebook"];
		$appid = $_POST["appid"];
		$password = md5($_POST["newpass"]);
		
		// Upload Image
		if(!empty($_FILES["logo"]["name"])){
			if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
				$artist_logo = $artistid."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
				@move_uploaded_file($_FILES['logo']['tmp_name'], '../artists/images/' . $artist_logo);
                $logo = $artist_logo;
			} else {
				if ($old_logo != $artist_logo) {
					$logo = $old_logo;
				}
			}
		}else{
			$logo = $old_logo;
		}
		//echo "{'img':'<img src=artists/images/$logo>'}";
		if(empty($_POST["newpass"])){
			$password = $old_pwd;
		}else{
			$_POST["newpass"] = $old_pwd;
		}
		
		
		$tables = "artist|email|gender|languages|location|music_likes|url|website|twitter|facebook|appid|password|IsArtist|logo";
		$values = "{$artist}|{$email}|{$gender}|{$languages}|{$location}|{$music_likes}|{$url}|{$website}|{$twitter}|{$facebook}|{$appid}|{$password}|{$IsArtist}|{$logo}";
		
		if ($artistid != "") {
			update($database,$tables,$values,"id",$artistid);
		} else {
			insert($database,$tables,$values);
		}
		
		$successMessage = "<div id='notify'>Success! You are being redirected...</div>";
		
		//showing the post value after the upload //	
		$postedValues['imageSource'] = "../artists/images/".$artist_logo;
		
		$postedValues['success'] = "1";
		
		$postedValues['postedValues'] = $_REQUEST;

		//echo '{"Name":"'.$artist_name.'","imageSource":"artists/images/'.$artist_logo.'","":"","artist_sound":"artists/audio/'.$artist_sound.'","success":1}';
		echo json_encode($postedValues);	
		exit;		

		refresh("1","?p=home");
	}
	
	if ($_SESSION['sess_userId'] != "") {
		$artistid=$_REQUEST['artist_id'];
		$row = mf(mq("select * from {$database} where id='{$artistid}'"));
		$artist = $row["artist"];
		$email = $row["email"];
		$gender = $row["gender"];
		$languages = $row["languages"];
		$location = $row["location"];
		$music_likes  = $row["music_likes"];
		$url  = $row["url"];
		$website = $row["website"];
		$twitter = $row["twitter"];
		$facebook = $row["facebook"];
		$appid = $row["appid"];
		$head_title = "Edit";
	}else{
		$head_title = "Add";
	}
	
	if ($artist_logo != "") {
		$artist_logo = '<img src="../artists/images/'.$artist_logo.'" style=" margin-top: 5px; height: 25px;" />';
	}
	
	if ($artist_download == "1") { $yesDownload = " checked"; } else { $noDownload = " checked"; }
	$artist_name = stripslashes($artist_name);
	
?>
	
    <link rel="stylesheet" media="screen" type="text/css" href="includes/css/layout.css" />
				
				
<div id="popup">
    <?=$successMessage;?>
    <div class='top_bar'>
        <h2>Edit Profile</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form  id="ajax_from" method="post" enctype="multipart/form-data" action="register.php">
        
        <input type="hidden" name="artistid" value="<?=$artistid?>">
        
        <div class='input_container'>
            <div class='left_label'>Name</div>
            <input type="text" class="right_text" value="<?=$artist?>" name="artist">
            <div class="clear"></div>
        </div>
        
        <div class='input_container'>
            <div class='left_label'>Email</div>
            <input type="text" class="right_text" value="<?=$email?>" name="email">
        </div>

        <div class='input_container'>
            <div class='left_label'>URL</div>
            <input type="text" class="right_text" value="<?=$url?>" name="url">
        </div>

        <div class='input_container'>
            <div class='left_label'>Logo</div>
            <input type="file" class="right_text" name="logo">
        </div>

        <div class='input_container'>
            <div class='left_label'>Twitter username</div>
            <input type="text" class="right_text" value="<?=$twitter?>" name="twitter">
        </div>

        <div class='input_container'>
            <div class='left_label'>Facebook username</div>
            <input type="text" class="right_text" value="<?=$facebook?>" name="facebook">
        </div>
        
        <div class='input_container'>
            <div class='left_label'>Show "Listen" Count</div>
            <div class="right_box">
                <input type="radio" class="radio" checked="" value="1" name="listens"> Yes
                <input type="radio" class="radio" value="0" name="listens"> No<br>
            </div>
        </div>

        <div class='input_container'>
            <div class='left_label'>New Password</div>
            <input type="password" class="right_text" value="" name="newpass">
        </div>

        <div class='submit_container'>
            <input type="submit" class="submit" value="submit" name="WriteTags" >
        </div>
        <div class="form_message">
        <?php if ($_GET["id"] != "") { ?>
            Your record successfully updated!
        <?php }else{ ?>
        <?php } ?>
        </div>
    </form>
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
