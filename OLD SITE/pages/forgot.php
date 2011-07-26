<?
	
	$forgot_form .= '<p>You can reset your password, simply enter in your username and press "Reset Password".  Your new password will be emailed to you.</p>'."\n";
	$forgot_form .= '<form method="post">'."\n";
	$forgot_form .= '<input type="text" name="forgot_email" />'."\n";
	$forgot_form .= '<div style="clear: both;">&nbsp;</div><input type="submit" value="Reset Password" name="forgot" class="submit" />'."\n";
	$forgot_form .= '</form>';


	if (isset($_POST["forgot"])) {

		$forgot_email = $_POST["forgot_email"];
		
		$detect = mq("select `id`,`email` from `[p]musicplayer` where `url`='{$forgot_email}' limit 1");
		$count = num($detect);
		
		if ($count > 0) {
			
			$real_password = rand(1111,9999);
			$correct = mf($detect);	
			update("[p]musicplayer","password",md5($real_password),"id",$correct["id"]);
			
			$email_to = $correct["email"];
			$email_subject = 'Retrieve password for '.siteTitle().'!';
			$sendmessage = "Forgot your password? No worries, we created a new one for you: $real_password";
			//amail($email_to,$cc,$bcc,"$siteName <$adminEmail>",$email_subject,$email_message);		
			mail($email_to,$email_subject,$sendmessage,"From: noreply@myartistdna.com");
			$forgot_status = "<p>Success! Your new password has been emailed to ".$correct["email"].".</p>";
		
		} else {
			$forgot_status = "<p>Hmm, there doesn't appear to be an account with that username, would you like to try again?</p>".$forgot_form;
		}
	} else {
		$forgot_status = $forgot_form;
	}

	echo $forgot_status."<p><br /><a href='".siteUrl()."?p=index'>&raquo; Return to Login Page</a></p>";

?>