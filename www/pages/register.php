<?
	
	if ($_POST["register"]) {
		
		$artist = nohtml($_POST["artist"]);
		$email = nohtml($_POST["mymail"]);
		$password = md5($_POST["mypass"]);
		$url = $email;
		
		insert("[p]musicplayer","artist|url|password|email|type","$artist|$url|$password|$email|1");
		$to = "youngfonz@gmail.com";
		$privateSubject = "New Registered Fan";
		$subject = "Welcome to {$siteTitle}";
		$from = "From: noreply@myartistdna.com";
		$privateMessage = "
Name: $name
Email: $email
Username/URL: $url
";
		$message = "
Hello {$emailName},

Thank you for your interest in {$siteName}. 

Your username is {$url} and your password is {$password}

You can now login by going to {$siteUrl}/?p=index

{$siteName}
{$siteUrl}
		";

		mail($to,$privateSubject,$privateMessage,$from);
		mail($email,$subject,$message,$from);				
		
?>
	<h1>Success!</h1>
	<p>Your account has been created, you can now login by <a href="?p=index">clicking here</a>.</p>
<?
	
	} else {
	
?>

	<h1>Fan Account Registration</h1>
	<p>With a fan account you can create your own playlist from any MyArtistDNA.fm artists.</p>
	
	<form method="post">
		
		<label>Full Name</label>
		<input type="text" name="artist" value="" />
		<div class="clear"></div>
		
		<label>Email</label>
		<input type="text" name="mymail" value="" />
		<div class="clear"></div>
		
		<label>Password</label>
		<input type="text" name="mypass" value="" />
		<div class="clear"></div>
		
		<input type="button" name="register" class="submit" value="Register" />
		
	</form>

<? } ?>