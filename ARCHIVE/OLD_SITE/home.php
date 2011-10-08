<?

	if ($_POST["submit"] != "") {

		$name = my($_POST["name"]);
		
		$email = $_POST["email"];
		$bandname = my($_POST["bandname"]);
		$type = my($_POST["type"]);
		$sxsw = my($_POST["sxsw"]);
		$url = strtolower(str_replace(" ", "-", $bandname));
		$password = rand(1111,9999);

		$emailName = nohtml($_POST["name"]);
		$siteName = siteTitle();
		$siteUrl = siteUrl();
		
		if ($type == "Fan") {
			$artist = $name;
			$url = $email;
		}
		
		$check = mq("select `url` from `[p]musicplayer` where `url`='{$url}'");
		if (num($check) > 0) {
			if ($type == "Fan") {
				$successMessage = '<div id="notify" class="error">That email is already registered with an account, please enter a new email.</div>';
			} else {
				$successMessage = '<div id="notify" class="error">That band name is already taken, please enter a new band name.</div>';
			}
		} else {
		
			if ($type == "Fan") { 
				$submitpass = md5($password);
				insert("[p]musicplayer","artist|url|password|email|type","$artist|$url|$submitpass|$email|1");
				$successMessage = "<div id='notify' class='success'>Your account has now been created and login credentials have been emailed to <br />{$email}.</div>";
				
				$to = "youngfonz@gmail.com";
				$privateSubject = "New Registered Fan";
				$subject = "Welcome to {$siteTitle}";
				$from = "From: noreply@myartistdna.com";
				$privateMessage = "
Name: $name
Email: $email
Type: $type
Met at SXSW: $sxsw
Username/URL: $url
Password: $password
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

			} else {

				$tables = "artist|url|email|password";
				$values = "{$bandname}|{$url}|{$email}|".md5($password);
				
				insert("[p]musicplayer",$tables,$values);
					
				$to = "youngfonz@gmail.com";
				$privateSubject = "New Registered Member";
				$subject = "Welcome to {$siteTitle}";
				$from = "From: noreply@myartistdna.com";
				$privateMessage = "
Name: $name
Email: $email
Band Name: $bandname
Type: $type
Met at SXSW: $sxsw
Username/URL: $url
Password: $password
";
				$message = "
Hello {$emailName},

Thank you for your interest in {$siteName}. Shortly you will be receiving a beta invitation from us.

Once you get into your account, simply click on Add Music or Add Pages and then follow the simple instructions.  Within minutes you will have an overview of your music player at which point you will be able to personalize it (add links, bio, lyrics, video, etc...).

Be sure to let us know when you have completed your music player...we love hearing new music and discovering new artists!

Thank you again, we wish you the best!

{$siteName}
{$siteUrl}
				";

				mail($to,$privateSubject,$privateMessage,$from);
				mail($email,$subject,$message,$from);
				
				$successMessage = "<div id='notify' class='success'>Your information has been submitted!</div>";
			}
		
		}
		
		
	}
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'>
<head>

	<title><?=siteTitle();?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="<?=siteUrl();?>/includes/home.css" rel="stylesheet" type="text/css" /> 
	<script type="text/javascript" src="<?=siteUrl();?>/includes/js/jquery-1.4.2.min.js"></script> 
	<script>
		$(document).ready(function(){
			
			$('#topten h1').click(function() {
				$('#toptenBody').slideToggle();
			});
			
			$('a.features').click(function() {
				$('#toptenBody').show();
			});
			
			$('#signup h1').click(function() {
				$('#signupBody').slideToggle();
			});
			
			$('a.signup').click(function() {
				$('#signupBody').show();
			});
			
			setTimeout(function(){ $('#notify').slideUp(); }, 4000);
			
		});
	</script>
	
</head>
<body>
		
	<div id="corner"></div>
	
	<div id="wrapper">

		<div id="header">
			<div id="controls">
				<a class="features" href="#features">+Features</a> &nbsp;&nbsp; <a class="signup" href="#signup">+Signup</a>
			</div>
			<h1>Welcome to MyArtistDNA.fm</h1>
		</div>
		
		<?=$successMessage;?>
		
		<div id="main" class="test">
		
		</div>
		
		<a name="features"></a>
		<div id="topten">
			<h1>+10 Reasons why you need this! Also known as features.</h1>
			<div id="toptenBody">
				<ol>
					<li>Real-time Statistics</li>
					<li>Custom Domains</li>
					<li>Build Your Mailing List</li>
					<li>Easy Setup</li>
					<li>Automatic Search Engine Optimization</li>
					<li>Magazine style layout</li>
					<li>Multi-User Access</li>
					<li>Build Your Mailing List</li>
					<li>Free mobile web app for iPhone</li>
					<li>Unlimited Songs/Pages</li>
				</ol>
			</div>		
		</div>
		
		<a name="signup"></a>
		<div id="signup">
			<h1>Sign up for your MyArtistDNA.fm account today!</h1>
			<div id="signupBody">
				<p>We are currently in private beta, but we are accepting artist to help us test this baby out. We just need to verify you are an artist and not an alien or even worse, a karaoke singer. Are you from a fancy tech bloc, startup firm, or by any chance an interested VC? If so, then <a href="#">click here</a>.</p>
				
				<form method="post" action="<?=siteUrl();?>">
					
					<div class="three">
						<label>Name</label>
						<input type="text" name="name" value="" />
						<div class="clear"></div>
					</div>
					
					<div class="three">
						<label>Email</label>
						<input type="text" name="email" value="" />
						<div class="clear"></div>
					</div>
					
					<div class="three">
						<label>Band Name</label>
						<input type="text" name="bandname" value="" />
						<div class="clear"></div>
					</div>
					
					<div class="clear">&nbsp;</div>
					
					<div class="three fan">
						<label>Are you a:</label>
						<div class="block">
							<input type="radio" class="radio" name="type" value="Band" /> Band 
							&nbsp;&nbsp;&nbsp; 
							<input type="radio" class="radio" name="type" value="Fan" /> Fan
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="three sxsw">
						<label>Did we meet you at SXSW by any chance?</label>
						<div class="block">
						<input type="checkbox" name="sxsw" value="Yes" class="checkbox" /> Yes
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="three csubmit">
						<input type="submit" name="submit" value="Submit" class="submit" />
						<div class="clear"></div>
					</div>
					
					<div class="clear"></div>

				</form>
			</div>
		</div>
		
		<div id="footer">
			<div>&copy <?=date("Y");?> myartistdna.fm All Rights Reserverd. <a href="?p=index">Client Login</a></div>
		</div>

	</div>
</body>
</html>