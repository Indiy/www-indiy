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


    
	if($_REQUEST['artist'] != "") 
    {
        $artist = $_REQUEST['artist'];
        
        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
	}
?>

<div id="popup">
    <div class="addcontent">
        <h2 class="title"  id="demonstrations">Add Artist</h2>
        <form id="none"  onsubmit='return false;'>
            <div id="form_field">
            <div class="clear"></div>
            
            <label>Facebook Account</label>
            <?php
                if( $facebook )
                    echo "<input type='text' disabled='disabled' value='$facebook' class='text' />\n";
                else
                    echo "<buton onclick=''>Add Facebook</button>\n";
            ?>
            
            <label>Twitter Account</label>
            <?php
                if( $twitter )
                    echo "<input type='text' disabled='disabled' value='$twitter' class='text' />\n";
                else
                    echo "<buton onclick=''>Add Twitter</button>\n";
            ?>
            <div class="clear"></div>

            
            <button class="submit" onclick='$.facebox.close();'>Close</button>
            <div id='status'></div>
        </div>
        </form>
    </div>
    <div style="clear: both;">&nbsp;</div>
</div>
<!-- end #content -->
<div id="sidebar">

</div>
<!-- end #sidebar -->
<div style="clear: both;">&nbsp;</div>



