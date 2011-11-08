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
        $artist = $_REQUEST['artist'];
        $url = $_REQUEST['url'];
        $email = $_REQUEST['email'];
        $password = md5($_REQUEST['password']);
        $label_id = "NULL";
        if( $_SESSION['sess_userType'] == 'LABEL' )
            $label_id = $_SESSION['sess_userId'];

        $tables = "artist|url|email|password|label_id";
		$values = "{$artist}|{$url}|{$email}|{$password}|{$label_id}";
        insert('[p]musicplayer',$tables,$values);

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
            
            <label>Artist</label>
            <input id='artist' type="text" name="artist" value="" class="text" />
            <div class="clear"></div>
            
            <label>URL</label>
            <input id='url' type="text" name="url" value="" class="text" />
            <div class="clear"></div>

            <label>Email</label>
            <input id='email' type="text" name="email" value="" class="text" />
            <div class="clear"></div>
            
            <label>Password</label>
            <input id='password' type="password" name="password" value="" class="text" />
            <div class="clear"></div>
            
            <button id='add_user_submit' class="submit" onclick='onAddUserSubmit();'>Submit</button>
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

