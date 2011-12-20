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
    <div class="addcontent">
        <h2 class="title"  id="demonstrations">Add Artist</h2>
        <form id="social_config_form"  onsubmit='return false;'>
            <div id="form_field">
            <div class="clear"></div>
            
            <label>Facebook Account</label>
            <?php
                if( $facebook )
                    echo "<input type='text' disabled='disabled' value='$facebook' class='text' />\n";
                else
                    echo "<button class='submit' onclick='clickAddFacebook();'>Add Facebook</button>\n";
            ?>
            <div class="clear"></div>
            <br/>

            <label>Twitter Account</label>
            <?php
                if( $twitter )
                    echo "<input type='text' disabled='disabled' value='$twitter' class='text' />\n";
                else
                    echo "<button class='submit' onclick='clickAddTwitter();'>Add Twitter</button>\n";
            ?>
            <div class="clear"></div>
            <br/>

            <label>Automatic Facebook</label>
            <input id='auto_fb' type="checkbox" name="auto_fb" <? if($auto_fb) echo 'checked'; ?> class="input_checkbox"/>
            <div class="clear"></div>

            <label>Automatic Tweet</label>
            <input id='auto_tw' type="checkbox" name="auto_tw" <? if($auto_tw) echo 'checked'; ?> class="input_checkbox"/>
            <div class="clear"></div>
            
            <button class="submit" onclick='onSocialConfigSave();'>Save</button>
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



