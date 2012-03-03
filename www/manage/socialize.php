<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
    
    require_once '../Login_Twitbook/twitter/twitteroauth.php';
    require_once '../Login_Twitbook/config/twconfig.php';
    
    require_once '../Login_Twitbook/facebook/facebook.php';
    require_once '../Login_Twitbook/config/fbconfig.php';
    
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
        
        $sql = "SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'";
        $artist = mf(mq($sql));
        if( $network == 'twitter' )
        {
            $oauth_token = $artist['oauth_token'];
            $oauth_secret = $artist['oauth_secret'];

            $postedValues['twitter_args'] = YOUR_CONSUMER_KEY .','. YOUR_CONSUMER_SECRET .','. $oauth_token .','. $oauth_secret;
            
            $connection = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $oauth_token, $oauth_secret);
            $content = $connection->get('account/verify_credentials');
            $result = $connection->post('statuses/update', array('status' => $update_text));
            $postedValues['twitter_content'] = $content;
            $postedValues['twitter_result'] = $result;
        }
        else if( $network == 'facebook' )
        {
            $fb_access_token = $artist['fb_access_token'];
            $postedValues['fb_a_t'] = $fb_access_token;
            try
            {
                $facebook = new Facebook(array('appId' => APP_ID,'secret' => APP_SECRET));
                $facebook->setAccessToken($fb_access_token);
                $result = $facebook->api('/me/feed','POST',array('message'=>$update_text));
                $postedValues['fb_result'] = $result;
            }
            catch(Exception $e)
            {
                $postedValues['fb_exception'] = $e;
            }
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
    $audio_name = $song['name'];
    $short_link = make_short_link($song['abbrev']);
    $update_text = "Check out my new song, $audio_name: $short_link via @myartistdna";
?>

<script type="text/javascript"> 
var g_artistId = '<?=$artist_id;?>';
</script>

<div id="popup">
    <div class='top_bar'>
        <h2>Socialize</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id="socialize_form" onsubmit='return false;'>
        <div class='flow_container'>
            <div class='left_label'>Type your text below</div>
            <textarea id="update_text" style="height: 40px;"><?=$update_text;?></textarea>
            <p>**Warning - You can not undo once you publish**</p>
        </div>
        <div class='input_container'>
            <div class='left_label'>Select a platform</div>
            <div class='right_box' style="margin-top: 4px;">
                <input type="radio" name="network" value="twitter" class="img_radio" checked="checked"/>
                <img src='/images/tw_icon_color.png'>
                <input type="radio" name="network" value="facebook" class="img_radio" />
                <img src='/images/fb_icon_color.png'>
            </div>
        </div>
        <div class='submit_container'>
            <button id='socialize_publish' class="submit" onclick='onSocializePublish();'>Publish</button>
        </div>
    </form>
    <div id='status' class='form_status' style='display: none;'></div>
    
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>

