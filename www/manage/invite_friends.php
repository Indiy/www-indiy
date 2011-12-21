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
    $song_id = $_REQUEST['song_id'];
    $song = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id = '$song_id'"));
    $short_link = make_short_link($song['abbrev']);
    $update_text = "Check out my new song: $short_link";
?>

<script type="text/javascript"> 
var g_artistId = '<?=$artist_id;?>';
</script>

<div id="popup">
    <div class="addcontent">
        <h2 class="title"  id="demonstrations">Invite Friends to MyArtistDNA</h2>
        <form id="invite_friends_form" onsubmit='return false;'>
            <div id="form_field">
            <div class="clear"></div>
            
            <label>Enter your friends emails below.  Seperate with commas.</label>
            <div class="clear"></div>
            <textarea id="friends_text" class="friends_textarea"></textarea>
            <div class="clear"></div>
            <br/>

            <button class="submit" onclick='onInviteFriends();'>Send</button>
        </div>
        </form>
        <div class="clear"></div>
        <div id='status' class='form_status' style='display: none;'></div>
        <div class="clear"></div>

    </div>
    <div style="clear: both;">&nbsp;</div>
</div>
<!-- end #content -->
<div id="sidebar">

</div>
<!-- end #sidebar -->
<div style="clear: both;">&nbsp;</div>

