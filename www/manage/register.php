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
		$appid = $_POST["appid"];
        $custom_domain = $_POST["custom_domain"];
		$password = md5($_POST["newpass"]);
        $user_tags = $_POST["tags"];
		
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
		
		
		$tables = "artist|email|gender|languages|location|music_likes|url|website|appid|password|IsArtist|logo|custom_domain|tags";
		$values = "{$artist}|{$email}|{$gender}|{$languages}|{$location}|{$music_likes}|{$url}|{$website}|{$appid}|{$password}|{$IsArtist}|{$logo}|{$custom_domain}|$user_tags";
		
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
        exit();
	}
	
	if ($_REQUEST['artist_id'] != "") {
		$artistid = $_REQUEST['artist_id'];
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
        $custom_domain = $row["custom_domain"];
        $account_type = $row["account_type"];
        $artist_logo = $row["logo"];
        $user_tags = $row["tags"];
        
		$head_title = "Edit";
	}else{
		$head_title = "Add";
	}
	
    $needs_image = "true";
	if( $artist_logo != "" ) 
    {
		$artist_logo = "<img src='/artists/images/$artist_logo' />";
        $needs_image = "false";
	}
	
	if ($artist_download == "1") { $yesDownload = " checked"; } else { $noDownload = " checked"; }
	$artist_name = stripslashes($artist_name);
	
?>
	
    <link rel="stylesheet" media="screen" type="text/css" href="includes/css/layout.css" />
				
<script type="text/javascript">
    $(document).ready(setupQuestionTolltips);
    
    var g_artistId = <?=$artistid;?>;
    
    var g_needsImage = <?=$needs_image;?>;
</script>

<div id="popup">
    <?=$successMessage;?>
    <div class='top_bar'>
        <h2>Edit Profile</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id='ajax_form' onsubmit="return false;">
        <input id='artist_id' type="hidden" name="artistid" value="<?=$artistid?>">
        
        <div class='input_container'>
            <div class='left_label'>Name<span class='required'>*</span></div>
            <input id='artist' type="text" class="right_text" value="<?=$artist?>" name="artist">
        </div>
        
        <div class='input_container'>
            <div class='left_label'>Email<span class='required'>*</span></div>
            <input id='email' type="text" class="right_text" value="<?=$email?>" name="email">
        </div>

        <div class='input_container'>
            <div class='left_label'>URL <span id='tip_artist_url' class='tooltip'>(?)</span><span class='required'>*</span></div>
            <input id='url' class="right_text" value="<?=$url?>" name="url">
        </div>
        <? if( $account_type == 'PREMIUM' ): ?>
            <div class='input_container'>
                <div class='line_label'>Custom Domain <span id='tip_custom_domain' class='tooltip'>(?)</span></div>
                <input id='custom_domain' name="custom_domain" class='line_text' value="<?=$custom_domain;?>">
            </div>
        <? endif; ?>
        <div class='input_container' style='height: 50px;'>
            <div class='left_image_label'>
                <div class='image_label'>Logo <span id='tip_artist_logo' class='tooltip'>(?)</span><span class='required'>*</span></div>
                <div class='image_image'><?=$artist_logo;?></div>
            </div>
            <input id='logo' type="file" name="logo" class='right_file' onchange='onImageChange(this);' />
        </div>
        <div class='input_container'>
            <div class='left_label'>Show "Listen" Count <span id='tip_listen_count' class='tooltip'>(?)</span></div>
            <div class="right_box">
                <input type="radio" class="radio" checked="" value="1" name="listens"> Yes
                <input type="radio" class="radio" value="0" name="listens"> No<br>
            </div>
        </div>
        <div class='input_container' style='height: 55px;'>
            <div class='left_label'>Password</div>
            <div class='right_box'>
                <button class='regular_button' onclick='showChangePassword();'>Change Password</button>
            </div>
        </div>
        <div class='input_container' style="height: 60px;">
            <div class='left_label'>Tags <span id='tip_tags' class='tooltip'>(?)</span></div>
            <div class='right_label'>Seperate tags with commas.</div>
            <div style='clear:both;'></div>
            <input id='user_tags' type="text" name="tags" value="<?=htmlentities($user_tags);?>" class="line_text" />
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='onEditProfileSubmit();'>Submit</button>
        </div>
    </form>
    <div id='change_password' style='display: none;'>
        <div class='input_container'>
            <div class='left_label'>Old Password</div>
            <input id='old_password' type="password" class="right_text">
        </div>
        <div class='input_container'>
            <div class='left_label'>New Password</div>
            <input id='new_password' type="password" class="right_text">
        </div>
        <div class='input_container'>
            <div class='left_label'>Confirm Password</div>
            <input id='confirm_password' type="password" class="right_text">
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='submitChangePassword();'>Submit</button>
        </div>
    </div>
    
    <? include_once 'include/popup_messages.html'; ?>
    
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
