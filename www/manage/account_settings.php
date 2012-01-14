<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if( $_SESSION['sess_userId'] == "" 
       || $_SESSION['sess_userType'] != 'SUPER_ADMIN' )
	{
		header("Location: index.php");
		exit();
	}
    
    $artist_id = $_REQUEST['artist_id'];
    $row = mf(mq("SELECT * FROM mydna_musicplayer WHERE id = '$artist_id'"));
    $account_type = $row['account_type'];
	
	if($_REQUEST['account_type'] != "") 
    {
        $account_type = $_REQUEST['account_type'];
        mysql_update('mydna_musicplayer',
                     array("account_type" => $account_type),
                     'id',$artist_id);

        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
	}
?>



<div id="popup">
    <div class='top_bar'>
    <h2>Account Settings</h2>
    <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    
    <form id='ajax_form' onsubmit='return false;'>
        <input name='artist_id' type='hidden' value="<?=$artist_id;?>">
        <div class='input_container'>
            <div class='left_label'>Account Type</div>
            <select name='account_type' class='right_drop'>
            <?
                $options = array("REGULAR","PREMIUM");
                foreach ($options as $option) 
                {
                    if($account_type == $option) 
                        $selected = " selected ";
                    else 
                        $selected = "";
                    $option_value = ucfirst(strtolower($option));
                    echo "<option value='$option' $selected>$option_value</option>\n";
                }
            ?>
            </select>
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='onAccountSettingsSubmit();'>Submit</button>
        </div>
    </form>

    <div id='status' class='form_message' style='display: none;'></div>
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
