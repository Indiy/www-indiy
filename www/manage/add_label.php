<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if($_SESSION['sess_userId'] == "")
	{
		header("Location: index.php");
		exit();
	}
	
	if($_REQUEST['name'] != "") 
    {
        $name = $_REQUEST['name'];
        $email = $_REQUEST['email'];
        $password = md5($_REQUEST['password']);

        $tables = "name|email|password";
		$values = "{$name}|{$email}|{$password}";
        insert('myartist_users',$tables,$values);

        $postedValues['success'] = "1";
		$postedValues['postedValues'] = $_REQUEST;
		echo json_encode($postedValues);
		exit();
	}
?>

<div id="popup">
    <div class="addcontent">
        <h2 class="title"  id="demonstrations">Add Label</h2>
        <form onsubmit='return false;'>
            <div id="form_field">
                <div class="clear"></div>
                
                <label>Label Name</label>
                <input id='name' type="text" name="name" value="" class="text" />
                <div class="clear"></div>
                
                <label>Email</label>
                <input id='email' type="text" name="email" value="" class="text" />
                <div class="clear"></div>
                
                <label>Password</label>
                <input id='password' type="password" name="password" value="" class="text" />
                <div class="clear"></div>
                
                <button id='add_label_submit' class="submit" onclick='onAddLabelSubmit();'>Submit</button>
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

