<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId'] == "")
	{
		header("Location: index.php");
		exit();
	}
	
    $artist_id = $_REQUEST['artist_id'];
    
	if($_REQUEST['submit'] != "") 
    {
        $paypal_email = $_REQUEST['paypal_email'];

        $tables = "userid|paypal";
		$values = "{$artist_id}|{$paypal_email}";
        $row = mf(mq("SELECT * FROM mydna_musicplayer_ecommerce WHERE userid = '$artist_id'"));
        if( $row )
            update('mydna_musicplayer_ecommerce',$tables,$values,"userid",$artist_id);
        else
            insert('mydna_musicplayer_ecommerce',$tables,$values);

        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
	}
    else
    {
        $paypal_email = '';
        $row = mf(mq("SELECT * FROM mydna_musicplayer_ecommerce WHERE userid = '$artist_id'"));
        if( $row )
        {
            $paypal_email = $row['paypal'];
        }
    }
?>

<script type="text/javascript"> 
    var g_artistId = '<?=$artist_id;?>';
</script>

<div id="popup">
    <div class="addcontent">
        <h2 class="title"  id="demonstrations">Ecommerce Settings</h2>
        <form onsubmit='return false;'>
            <div id="form_field">
                <div class="clear"></div>
                
                <label>Paypal Email</label>
                <input id='paypal_email' type="text" value="<?=$paypal_email?>" class="text" />
                <div class="clear"></div>
                
                <button id='store_settings_submit' class="submit" onclick='onStoreSettingsSubmit();'>Submit</button>
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

