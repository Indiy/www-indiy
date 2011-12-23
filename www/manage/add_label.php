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
    <div class='top_bar'>
    <h2>Add Label</h2>
    <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    
    <form onsubmit='return false;'>
        <div class='input_container'>
            <div class='left_label'>Label Name</div>
            <input id='name' type="text" name="name" value="" class='right_text' />
        </div>
        <div class='input_container'>
            <div class='left_label'>Email</div>
            <input id='email' type="text" name="email" value="" class='right_text' />
        </div>
        <div class='input_container'>
            <div class='left_label'>Password</div>
            <input id='password' type="password" name="password" value="" class='right_text' />
        </div>
        <div class='submit_container'>
            <button id='add_label_submit' class="submit" onclick='onAddLabelSubmit();'>Submit</button>
        </div>
    </form>

    <div id='status'></div>
    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
