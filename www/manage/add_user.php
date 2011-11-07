<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId']=="")
	{
		header("Location: index.php");
		exit();
	}
	
	if($_POST['artist'] != "") 
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
<script type="text/javascript">

function onSubmit()
{
    $('#add_user_submit').hide();
    $('#status').text("Adding user...");
    var artist = $('#artist').val();
    var url = $('#url').val();
    var email = $('#email').val();
    var password = $('#password').val();
    
    var post_url = "?artist=" + escape(artist);
    post_url += "&url=" + escape(url);
    post_url += "&email=" + escape(email);
    post_url += "&password=" + escape(password);
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'json',
        success: function(data) 
        {
            $('#status').text("User Added");
        },
        error: function()
        {
            $('#status').text("User Add Failed!");
        }
    });
    return false;
}
</script>

<div id="popup">
    <div class="addcontent">
        <h2 class="title"  id="demonstrations">Add Artist</h2>
        <!-- <form id="none" method="post" enctype="multipart/form-data" action="add_user.php"> -->
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
            
            <button id='add_user_submit' class="submit" onclick='onSubmit();'>Submit</button>
            <div id='status'></div>
        </div>
        <!-- </form> -->
    </div>
    <div style="clear: both;">&nbsp;</div>
</div>
<!-- end #content -->
<div id="sidebar">

</div>
<!-- end #sidebar -->
<div style="clear: both;">&nbsp;</div>

