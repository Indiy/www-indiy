<?php session_start();
include('includes/config.php');
include('includes/functions.php');

if ($_REQUEST["username"]!='' && $_REQUEST["email"]!='') {
	$user = addslashes($_REQUEST['username']);
	$email = addslashes($_REQUEST['email']);
	$result = mysql_query("select id from mydna_musicplayer where (username='$user') || email='$email'");
	$me_row = mysql_num_rows($result);	
	if ($me_row > 0) {	
		echo "2";
	}
	else {
		$inser_sql = "";
		$artist = nohtml($_POST["name"]);
		$username = nohtml($_POST["username"]);
		$email = nohtml($_POST["email"]);
		$password = md5($_POST["password"]);
	
		
		insert("[p]musicplayer","artist|url|username|password|email|type","$artist|$username|$username|$password|$email|1");
		$to = "$email";	
		$privateSubject = "MyArtistDNA";
		$subject = "Welcome to {$siteTitle}";
		$from = "From: noreply@myartistdna.com";
		$privateMessage = "
					Name: $artist
					Email: $email
					Username/URL: $username
					";
							$message = "
					Hello {$artist},

					Thank you for your interest in {$siteName}. 

					Your username is {$username} and your password is {$_POST[password]}

					You can now login by going to {$siteUrl}/?p=index

					{$siteName}
					{$siteUrl}
							";

		mail($to,$privateSubject,$privateMessage,$from);
		mail($email,$subject,$message,$from);		
		echo '0';
	}
}
else{
	echo '1';
}
?>