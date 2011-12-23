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
    <div class='top_bar'>
    <h2>Add Artist</h2>
    <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id="none"  onsubmit='return false;'>
        <div class='input_container'>
            <div class='left_label'>Artist</div>
            <input id='artist' type="text" name="artist" value="" class='right_text' />
        </div>
        <div class='input_container'>
            <div class='left_label'>URL</div>
            <input id='url' type="text" name="url" value="" class='right_text' />
        </div>
        <div class='input_container'>
            <div class='left_label'>Email</div>
            <input id='email' type="text" name="email" value="" class='right_text' />
        </div>
        <div class='input_container'>
            <div class='left_label'>Password</div>
            <input id='password' type="password" name="password" value="" class='right_text' />
        </div>
        <div class='submit_container'>
            <button id='add_user_submit' class="submit" onclick='onAddUserSubmit();'>Submit</button>
        </div>
    </form>
    
    <div id='status'></div>
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
