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
        <h2>Socialize</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id="social_config_form"  onsubmit='return false;'>
        <div class='input_container'>
            <div class='left_label'Facebook Account</div>
            <?php
                if( $facebook )
                    echo "<input type='text' disabled='disabled' value='$facebook' class='right_input' />\n";
                else
                {
                    echo "<div class='right_box'>";
                    echo "<button class='submit' onclick='clickAddFacebook();'>Add Facebook</button>\n";
                    echo "</div>";
                }
            ?>
        </div>
        <div class='input_container'>
            <div class='left_label'Twitter Account</div>
            <?php
                if( $twitter )
                    echo "<input type='text' disabled='disabled' value='$twitter' class='right_input' />\n";
                else
                {
                    echo "<div class='right_box'>";
                    echo "<button class='submit' onclick='clickAddTwitter();'>Add Twitter</button>\n";
                    echo "</div>";
                }
            ?>
        </div>
        <div class='input_container'>
            <div class='left_label'Automatic Facebook</div>
            <input id='auto_fb' type="checkbox" name="auto_fb" <? if($auto_fb) echo 'checked'; ?> class="right_box"/>
        </div>
        <div class='input_container'>
            <div class='left_label'Automatic Tweet</div>
            <input id='auto_tw' type="checkbox" name="auto_tw" <? if($auto_tw) echo 'checked'; ?> class="right_box"/>
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='onSocialConfigSave();'>Save</button>
        </div>
    </form>
    <div id='status' class='form_status' style='display: none;'></div>
    
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
