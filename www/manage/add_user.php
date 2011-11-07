<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId']=="")
	{
		header("location: index.php");
		exit();
	}
	
	$database = "[p]musicplayer_content";
    $_SESSION['tabOpen']='pages';
	if ($_POST["submit"] != "") {
		
		if ($_POST["id"] != "") {
			$row = mf(mq("select `id`,`image` from `{$database}` where `id`='{$_POST["id"]}'"));
			$old_logo = $row["image"];
		}
		
		$content_name = my($_POST["name"]);
		$content_video = $_POST["video"];
		$content_body = my($_POST["body"]);
		
		// Upload Image
		if(!empty($_FILES["logo"]["name"])){
			if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
				$content_logo = $artistid."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
				@move_uploaded_file($_FILES['logo']['tmp_name'], '../artists/images/' . $content_logo);
			} else {
				if ($old_logo != "") {
					$content_logo = $old_logo;
				}
			}
		}else{
					$content_logo = $old_logo;
		}
		
		$tables = "artistid|name|image|video|body";
		$values = "{$artistid}|{$content_name}|{$content_logo}|{$content_video}|{$content_body}";
		
		if ($_POST["id"] != "") {
			update($database,$tables,$values,"id",$_POST["id"]);
		} else {
			insert($database,$tables,$values);
		}
		
		$postedValues['imageSource'] = "../artists/images/".$content_logo;
		//$postedValues['video_sound'] = "artists/video/".$audio_sound;
		$postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		//echo '{"Name":"'.$audio_name.'","imageSource":"artists/images/'.$audio_logo.'","":"","audio_sound":"artists/audio/'.$audio_sound.'","success":1}';
		echo json_encode($postedValues);
		exit;
	}
	
	if ($_GET["id"] != "") {
		$artistid=$_REQUEST['artist_id'];
		$row = mf(mq("select * from `{$database}` where `id`='{$_GET["id"]}' and `artistid`='{$artistid}'"));
		$content_id = $row["id"];
		$content_name = $row["name"];
		$content_logo = $row["image"];
		$content_video = $row["video"];
		$content_body = $row["body"];
		$head_title	=	"Edit";
	}else{
		$head_title	=	"Add";
	}

	
	if ($content_logo != "") {
		$content_logo = '<img src="../artists/images/'.$content_logo.'" style="margin-top: 5px; height: 25px;" />';
	}
	
	$content_name = stripslashes($content_name);
	$content_body = stripslashes($content_body);
?>
				
				
<div id="popup">
    <?=$successMessage;?>
    <div class="addcontent">
        <h2 class="title"  id="demonstrations"><?=$head_title?> Page</h2>
        <form id="ajax_from" method="post" enctype="multipart/form-data" action="add_user.php">
            <div id="form_field">
            <div class="clear"></div>
            
            <label>Artist</label>
            <input type="text" name="artist" value="" class="text" />
            <div class="clear"></div>
            
            <label>URL</label>
            <input type="text" name="url" value="" class="text" />
            <div class="clear"></div>

            <label>Email</label>
            <input type="text" name="email" value="" class="text" />
            <div class="clear"></div>
            
            <label>Password</label>
            <input type="password" name="password" value="" class="text" />
            <div class="clear"></div>
            
            <input type="submit" name="submit" value="submit" class="submit" />
        </div>
        </form>
    </div>
    <div style="clear: both;">&nbsp;</div>
</div>
<!-- end #content -->
<div id="sidebar">

</div>
<!-- end #sidebar -->
<div style="clear: both;">&nbsp;</div>

