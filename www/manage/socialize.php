<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId'] == "")
	{
		header("Location: index.php");
		exit();
	}
	
	if($_REQUEST['artist'] != "") 
    {

        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
	}
?>

<div id="popup">
    <div class="addcontent">
        <h2 class="title"  id="demonstrations">Add Artist</h2>
        <form id="none"  onsubmit='return false;'>
            <div id="form_field">
            <div class="clear"></div>
            
            <label>Type your text below:</label>
            <textarea name="description" id="input" class="textarea"></textarea>
            <p>Warning - You can not undo once you publish</p>
            <div class="clear"></div>

            <input type="radio" name="fb_or_tw" value="facebook" class="radio" /> Facebook
            <input type="radio" name="fb_or_tw" value="twitter" class="radio" /> Twitter
            <div class="clear"></div>
            
            <button id='socialize_publish' class="submit" onclick='onSocializePublish();'>Publish</button>
            <div id='status'></div>
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

