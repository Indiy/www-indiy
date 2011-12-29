<?php 
    
    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId'] == "")
	{
		header("Location: index.php");
		exit();
	}
	
    $artist_id = $_REQUEST['artist_id'];
    $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'"));

    $twitter = FALSE;
    $facebook = FALSE;
    if( $artist['oauth_token'] && $artist['oauth_secret'] && $artist['twitter'] )
        $twitter = $artist['twitter'];

    if( $artist['fb_access_token'] && $artist['facebook'] )
        $facebook = $artist['facebook'];

    $auto_fb = $artist['auto_fb'];
    $auto_tw = $artist['auto_tw'];

    $artist_url = $artist['url'];
    $embed_url = playerUrl() . $artist_url . "&embed=true";
    $embed_text = "<iframe src=\"$embed_url\" border=\"0\" width=\"400\" height=\"600\" frameborder=\"0\" name=\"$artist_url\"></iframe>";
    
	if(isset($_REQUEST['auto_fb'])) 
    {
        $auto_fb = $_REQUEST['auto_fb'];
        $auto_tw = $_REQUEST['auto_tw'];
        
        mysql_update('mydna_musicplayer',
                     array("auto_fb" => $auto_fb,"auto_tw" => $auto_tw),
                     'id',$artist_id);
        
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
        <h2>Social Connections</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id="social_config_form"  onsubmit='return false;'>
        <div class='input_container' style='height: 55px;'>
            <div class='left_label'>Facebook Account</div>
            <?php
                if( $facebook )
                    echo "<input type='text' disabled='disabled' value='$facebook' class='right_text' />\n";
                else
                {
                    echo "<div class='right_box'>";
                    echo "<button class='submit' onclick='clickAddFacebook();'>Add Facebook</button>\n";
                    echo "</div>";
                }
            ?>
        </div>
        <div class='input_container'>
            <div class='line_label'>Facebook Page URL</div>
            <input id='fb_page_url' type="text" name="name" value="<?=$fb_page_url;?>" class='line_text' />
        </div>
        <div class='input_container' style='height: 55px;'>
            <div class='left_label'>Twitter Account</div>
            <?php
                if( $twitter )
                    echo "<input type='text' disabled='disabled' value='$twitter' class='right_text' />\n";
                else
                {
                    echo "<div class='right_box'>";
                    echo "<button class='submit' onclick='clickAddTwitter();'>Add Twitter</button>\n";
                    echo "</div>";
                }
            ?>
        </div>
        <div class='input_container'>
            <div class='left_label'>Facebook</div>
            <div class='right_box'>
                <input id='fb_setting' type="radio" name="fb_setting" value="AUTO" class="radio" <?=$fb_auto;?> /> Auto
                <input id='fb_setting' type="radio" name="fb_setting" value="MANUAL" class="radio" <?=$fb_manual;?> /> Manual
                <input id='fb_setting' type="radio" name="fb_setting" value="DISABLED" class="radio" <?=$fb_disabled;?> /> Disabled
            </div>
        </div>
        <div class='input_container'>
            <div class='left_label'>Twitter</div>
            <div class='right_box'>
                <input id='tw_setting' type="radio" name="tw_setting" value="AUTO" class="radio" <?=$tw_auto;?> /> Auto
                <input id='tw_setting' type="radio" name="tw_setting" value="MANUAL" class="radio" <?=$tw_manual;?> /> Manual
                <input id='tw_setting' type="radio" name="tw_setting" value="DISABLED" class="radio" <?=$tw_disabled;?> /> Disabled
            </div>
        </div>
        <div class='flow_container'>
            <div class='line_label'>Embed Widget</div>
            <textarea style='height: 40px;'><?=$embed_text;?></textarea>
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='onSocialConfigSave();'>Save</button>
        </div>
    </form>
    <div id='status' class='form_status' style='display: none;'></div>
    
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
