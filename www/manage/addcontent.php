<?php require_once('../includes/config.php');
	if($_SESSION['sess_userId']=="")
	{
		header("location: index.php");
		exit();
	}
	include("../includes/functions.php");
	
	$database = "[p]musicplayer_content";
    $_SESSION['tabOpen']='pages';
	if ($_POST["submit"] != "") {
		
		if ($_POST["id"] != "") {
			$row = mf(mq("select `id`,`image` from `{$database}` where `id`='{$_POST["id"]}'"));
			$old_logo = $row["image"];
		}
		
        $remove_image = $_POST["remove_image"] == 'true';
        
        if( $remove_image )
            $old_logo = '';
        
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

	$image_html = '';
	if( $content_logo != "" ) 
    {
		$image_html = "<img src='../artists/images/$content_logo' />";
        $image_html .= "<button onclick='return onImageRemove();'></button>";
	}
	
	$content_name = stripslashes($content_name);
	$content_body = stripslashes($content_body);
?>

<style type="text/css">

.editor-hidden {
    visibility: hidden;
    top: -9999px;
    left: -9999px;
    position: absolute;
}
textarea {
    border: 0;
    margin: 0;
    padding: 0;
}

.yui-skin-sam .yui-toolbar-container .yui-toolbar-editcode span.yui-toolbar-icon {
    background-image: url( /images/html_editor.gif );
    background-position: 0 1px;
left: 5px;
}
.yui-skin-sam .yui-toolbar-container .yui-button-editcode-selected span.yui-toolbar-icon {
    background-image: url( /images/html_editor.gif );
    background-position: 0 1px;
left: 5px;
}

</style>

<script type="text/javascript">

function onReady()
{
    setupRichTextEditor();
    setupQuestionTolltips();
}

$(document).ready(onReady);

var g_removeImage = false;
function onImageRemove()
{
    var result = window.confirm("Remove image from page?");
    if( result )
    {
        g_removeImage = true;
        $('.image_image').hide();
    }
    return false;
}

</script>

<div id="popup">
    <?=$successMessage;?>
    <div class='top_bar'>
        <h2><?=$head_title?> Tab</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id='ajax_form' method="post" enctype="multipart/form-data" action="addcontent.php" onsubmit='return onAddContentSubmit();'>
        <input id='artist_id' type='hidden' value="<?=$_REQUEST['artist_id']?>" name="artistid">
        <input id='content_id' type='hidden' value="<?=$_REQUEST['id']?>" name="id">
        
        <div class='input_container'>
            <div class='left_label'>Name <span id='tip_tab_name' class='tooltip'>(?)</span><span class='required'>*</span></div>
            <input id='name' type="text" name="name" value="<?=$content_name;?>" class='right_text' />
        </div>
        <div class='input_container'>
            <div class='left_image_label' style='width: 200px;'>
                <div class='image_label'>Image</div>
                <div class='image_image'><?=$image_html;?></div>
            </div>
            <input id='content_image' type="file" name="logo" class='right_file' onchange='onImageChange(this);' />
        </div>
        <div class='editor_container yui-skin-sam'>
            <textarea id="body" name="body"><?=$content_body;?></textarea>
        </div>
        <div class='submit_branding_container'>
            <input type="submit" name="submit" value="submit" class='left_submit' />
            <div class='branding_tip'>Branding Tip: Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
        </div>
    </form>
    
    <? include_once 'include/popup_messages.html'; ?>

    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
