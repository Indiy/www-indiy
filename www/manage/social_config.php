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

    $fb_auto = $artist['fb_setting'] == 'AUTO' ? 'checked' : '';
    $fb_manual = $artist['fb_setting'] == 'MANUAL' ? 'checked' : '';
    $fb_disabled = $artist['fb_setting'] == 'DISABLED' ? 'checked' : '';
    $tw_auto = $artist['tw_setting'] == 'AUTO' ? 'checked' : '';
    $tw_manual = $artist['tw_setting'] == 'MANUAL' ? 'checked' : '';
    $tw_disabled = $artist['tw_setting'] == 'DISABLED' ? 'checked' : '';

    $artist_url = $artist['url'];
    $embed_url = playerUrl() . $artist_url . "&embed=true";
    $embed_text = "<iframe src=\"$embed_url\" border=\"0\" width=\"400\" height=\"600\" frameborder=\"0\" name=\"$artist_url\"></iframe>";
    
    $fb_page_url = $artist['fb_page_url'];
    
	if(isset($_REQUEST['fb_setting'])) 
    {
        $fb_setting = $_REQUEST['fb_setting'];
        $tw_setting = $_REQUEST['tw_setting'];
        $fb_page_url = $_REQUEST['fb_page_url'];
        
        mysql_update('mydna_musicplayer',
                     array(
                           "fb_setting" => $fb_setting,
                           "tw_setting" => $tw_setting,
                           "fb_page_url" => $fb_page_url,
                           ),
                     'id',$artist_id);
        
        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
	}
?>

<script type="text/javascript"> 
var g_artistId = '<?=$artist_id;?>';
$(document).ready(setupQuestionTolltips);

var FB_PLACEHOLDER = "You can only embed a Facebook Fan Page not a Profile Page!";

function blurFBPageURL(field)
{
    var val = $('#fb_page_url').val()
    if( val == '' )
    {
        $('#fb_page_url').val(FB_PLACEHOLDER);
        $('#fb_page_url').addClass('placeholder');
    }
}
function focusFBPageURL(field)
{
    var val = $('#fb_page_url').val();
    if( val == FB_PLACEHOLDER )
    {
        $('#fb_page_url').val('');
    }
    $('#fb_page_url').removeClass('placeholder');
}

$(document).ready(blurFBPageURL);
    
</script>

<div id="popup">
    <div class='top_bar'>
        <h2>Social Connections</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id='ajax_form'  onsubmit='return false;'>
        <div class='input_container' style='height: 55px;'>
            <div class='left_label'>Twitter Account <span id='tip_tw_account' class='tooltip'>(?)</span></div>
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
        <div class='input_container' style='height: 55px;'>
            <div class='left_label'>Facebook Account <span id='tip_fb_account' class='tooltip'>(?)</span></div>
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
            <div class='line_label'>Facebook Fan Page URL <span id='tip_fb_page_url' class='tooltip'>(?)</span></div>
            <input id='fb_page_url' class='line_text' type="text" name="name" value="<?=$fb_page_url;?>" 
                    onfocus="focusFBPageURL();" onblur="blurFBPageURL();"/>
        </div>
        <div class='input_container'>
            <div class='left_label'>Facebook Update <span id='tip_fb_setting' class='tooltip'>(?)</span></div>
            <div class='right_box'>
                <input id='fb_setting' type="radio" name="fb_setting" value="AUTO" class="radio" <?=$fb_auto;?> /> Auto
                <input id='fb_setting' type="radio" name="fb_setting" value="MANUAL" class="radio" <?=$fb_manual;?> /> Manual
                <input id='fb_setting' type="radio" name="fb_setting" value="DISABLED" class="radio" <?=$fb_disabled;?> /> Disabled
            </div>
        </div>
        <div class='input_container'>
            <div class='left_label'>Tweet <span id='tip_tw_setting' class='tooltip'>(?)</span></div>
            <div class='right_box'>
                <input id='tw_setting' type="radio" name="tw_setting" value="AUTO" class="radio" <?=$tw_auto;?> /> Auto
                <input id='tw_setting' type="radio" name="tw_setting" value="MANUAL" class="radio" <?=$tw_manual;?> /> Manual
                <input id='tw_setting' type="radio" name="tw_setting" value="DISABLED" class="radio" <?=$tw_disabled;?> /> Disabled
            </div>
        </div>
        <div class='flow_container'>
            <div class='line_label'>Embed Widget <span id='tip_embed' class='tooltip'>(?)</span></div>
            <textarea style='height: 40px; width: 320px;'><?=$embed_text;?></textarea>
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='onSocialConfigSave();'>Save</button>
        </div>
    </form>
    
    <? include_once 'include/popup_messages.html'; ?>
    
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
