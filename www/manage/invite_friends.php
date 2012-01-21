<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
    
    
	if($_SESSION['sess_userId'] == "")
	{
		header("Location: index.php");
		exit();
	}
	
	if($_REQUEST['friends'] != "") 
    {
        $friends = $_REQUEST['friends'];
        
        $postedValues = array();
        
        $sql = "SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'";
        $artist = mf(mq($sql));

        $artist_name = $artist['artist'];

        $to = $friends;
        $message = $data['message'];
        $subject = 'Someone has invited you to MyArtistDNA';
        
        $message = <<<END
$artist_name has invited you to MyArtistDNA.

Be Heard. Be Seen. Be Independent.
        
END;
        $from = "no-reply@myartistdna.com";
        $headers = "From:" . $from;
        
        mail($to,$subject,$message,$headers);

        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
	}
?>

<script type="text/javascript"> 
var g_artistId = '<?=$artist_id;?>';
</script>

<div id="popup">
    <div class='top_bar'>
        <h2>Invite Friends</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id='ajax_form' onsubmit='return false;'>
        <div class='flow_container'>
            <div class='left_label'>Enter your friends emails below.  Seperate with commas.</div>
            <textarea id="friends_text" style='height: 40px;'></textarea>
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='onInviteFriends();'>Send</button>
        </div>
    </form>
    
    <? include_once 'include/popup_messages.html'; ?>

    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
