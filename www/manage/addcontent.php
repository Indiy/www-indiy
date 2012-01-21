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


<script type="text/javascript">

var toolbar_config = 
{
collapse: true,
titlebar: 'Body',
draggable: false,
buttonType: 'advanced',
buttons: [
    { group: 'fontstyle', label: 'Font Name and Size',
    buttons: [
        { type: 'select', label: 'Arial', value: 'fontname', disabled: true,
        menu: [
            { text: 'Arial', checked: true },
            { text: 'Arial Black' },
            { text: 'Comic Sans MS' },
            { text: 'Courier New' },
            { text: 'Lucida Console' },
            { text: 'Tahoma' },
            { text: 'Times New Roman' },
            { text: 'Trebuchet MS' },
            { text: 'Verdana' }
            ]
        },
        { type: 'spin', label: '13', value: 'fontsize', range: [ 9, 75 ], disabled: true }
        ]
    },
    { type: 'separator' },
    { group: 'textstyle', label: 'Font Style',
    buttons: [
        { type: 'push', label: 'Bold CTRL + SHIFT + B', value: 'bold' },
        { type: 'push', label: 'Italic CTRL + SHIFT + I', value: 'italic' },
        { type: 'push', label: 'Underline CTRL + SHIFT + U', value: 'underline' },
        { type: 'separator' },
        { type: 'color', label: 'Font Color', value: 'forecolor', disabled: true },
        { type: 'color', label: 'Background Color', value: 'backcolor', disabled: true },
        { type: 'separator' },
        { type: 'push', label: 'Show/Hide Hidden Elements', value: 'hiddenelements' }
        ]
    },
    { type: 'separator' },
    { group: 'alignment', label: 'Alignment',
    buttons: [
        { type: 'push', label: 'Align Left CTRL + SHIFT + [', value: 'justifyleft' },
        { type: 'push', label: 'Align Center CTRL + SHIFT + |', value: 'justifycenter' },
        { type: 'push', label: 'Align Right CTRL + SHIFT + ]', value: 'justifyright' },
        { type: 'push', label: 'Justify', value: 'justifyfull' }
        ]
    },
    { type: 'separator' },
    { group: 'indentlist', label: 'Lists',
    buttons: [
        { type: 'push', label: 'Create an Unordered List', value: 'insertunorderedlist' },
        { type: 'push', label: 'Create an Ordered List', value: 'insertorderedlist' }
        ]
    },
    { type: 'separator' },
    { group: 'insertitem', label: 'Insert Item',
    buttons: [
        { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink', disabled: true },
        { type: 'push', label: 'Insert Image', value: 'insertimage' }
        ]
    }
    ]
};

var g_editor = false;
function onReady()
{
    g_editor = new YAHOO.widget.Editor('body', {
                                           height: '300px',
                                           width: '750px',
                                           dompath: false, 
                                           animate: false,
                                           handleSubmit: true,
                                           toolbar: toolbar_config
                                           });
    g_editor.render();
}

$(document).ready(onReady);

</script>

<div id="popup">
    <?=$successMessage;?>
    <div class='top_bar'>
        <h2><?=$head_title?> Tab</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id="ajax_form" method="post" enctype="multipart/form-data" action="addcontent.php" onsubmit='return onAddContentSubmit();'>
        <input id='artist_id' type='hidden' value="<?=$_REQUEST['artist_id']?>" name="artistid">
        <input id='content_id' type='hidden' value="<?=$_REQUEST['id']?>" name="id">
        
        <div class='input_container'>
            <div class='left_label'>Name</div>
            <input id='name' type="text" name="name" value="<?=$content_name;?>" class='right_text' />
        </div>
        <div class='input_container'>
            <div class='left_label'>Image</div>
            <input id='content_image' type="file" name="logo" class='right_file' /> <?=$content_logo;?>
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
