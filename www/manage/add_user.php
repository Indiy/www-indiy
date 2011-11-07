<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId']=="")
	{
		header("Location: index.php");
		exit();
	}
	
	if ($_POST['submit'] != "") 
    {
        $artist = $_POST['artist'];
        $url = $_POST['url'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $tables = "artist|url|email|password";
		$values = "{$artist}|{$url}|{$email}|{$password}";
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

