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
    <div class='top_bar'>
        <h2>Store Settings</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form onsubmit='return false;'>

        <div class='input_container'>
            <div class='left_label'>Paypal Email</div>
            <input id='paypal_email' type="text" value="<?=$paypal_email?>" class="right_text" />
        </div>
        <div class='submit_container'>
            <button id='store_settings_submit' class="submit" onclick='onStoreSettingsSubmit();'>Submit</button>
        </div>
    </form>
    <div id='status' class='form_message'></div>

    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
