<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
    
    require_once '../Login_Twitbook/twitter/twitteroauth.php';
    require_once '../Login_Twitbook/config/twconfig.php';
    
	if($_SESSION['sess_userId'] == "")
	{
		header("Location: index.php");
		exit();
	}
	
	if($_REQUEST['update_text'] != "") 
    {
        $artist_id = $_REQUEST['artist_id'];
        $update_text = $_REQUEST['update_text'];
        $network = $_REQUEST['network'];
        
        $postedValues = array();
        
        if( $network == 'twitter' )
        {
            $sql = "SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'";
            $artist = mf(mq($sql));
            $oauth_token = $artist['oauth_token'];
            $oauth_secret = $artist['oauth_secret'];
            $connection = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $oauth_token, $oauth_secret);
            //$content = $connection->get('account/verify_credentials');
            $result = $connection->post('statuses/update', array('status' => $update_text));
            $postedValues['twitter_content'] = $content;
            $postedValues['twitter_result'] = $result;
        }
        else if( $network == 'facebook' )
        {
            header("HTTP/1.0 500 Server Error");
            exit();
        }
        else
        {
            header("HTTP/1.0 500 Server Error");
            exit();
        }

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
        <h2 class="title"  id="demonstrations">Socialize</h2>
        <form id="socialize_form" onsubmit='return false;'>
            <div id="form_field">
            <div class="clear"></div>
            
            <label>Type your text below:</label>
            <div class="clear"></div>
            <textarea id="update_text" class="social_textarea"><?=$update_text;?></textarea>
            <p>Warning - You can not undo once you publish</p>
            <div class="clear"></div>
            <br/>

            Select a platform:
            <input type="radio" name="network" value="twitter" class="radio" /> Twitter
            <input type="radio" name="network" value="facebook" class="radio" /> Facebook
            <div class="clear"></div>
            
            <button id='socialize_publish' class="submit" onclick='onSocializePublish();'>Publish</button>
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

