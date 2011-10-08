<?
		// LOGGING OUT /////////////////////////////////////////////////////////////////////

		if (isset($_GET["logout"]) && $_GET["logout"] == "true") {
			mq("UPDATE [p]musicplayer SET online='0' WHERE id='{$_SESSION["me"]}'");	
			//setcookie($cookievar, false, time() - 86400);
			session_unset();
			session_destroy();
			//refresh("1", siteUrl()."?p=index");
			refresh("1", siteUrl()."/");
		} else {	
			if (trim($_SESSION["me"]) == "") {
				/* No Session Set - Look for existing cookie */
				if(isset($_COOKIE[$cookievar])) {
					$cookie = $_COOKIE[$cookievar];
					$checkSess = mf(my("select id,online from [p]musiplayer where id='$cookie' and online='1' limit 1"));
					if ($checkSess["id"] >= "1") {
						session_regenerate_id();
						$_SESSION['me'] = $cookie;
						session_write_close();
						$me = $_SESSION["me"];
					}
				}
			} else {
				$me = $_SESSION["me"];
			}
		}
		
		// LOGGING IN /////////////////////////////////////////////////////////////////////
		
		$standardform = '
		<form method="post" class="login"> 
		
			<div class="row">
				<label>Username:</label>
				<input type="text" name="username" class="small" />
				<div class="clear"></div>
			</div>

			<div class="row">
				<label>Password:</label>
				<input type="password" name="password" class="small" />
				<div class="clear"></div>
			</div>
			
			<div class="row">
				<label>&nbsp;</label>
				<input type="submit" value="Login" name="login" class="button" style="width: auto !important;" />
				<div class="clear"></div>
			</div>
			
			<div class="row">
				<label>&nbsp;</label>
				<a href="'.$siteUrl.'?p=index&forgot=true">Forgot Password</a>
				<div class="clear"></div>
			</div>
			
		</form>';
		
		$logoutLink = "Loading...<br />If you are not redirected within 5 seconds, <a href='?p=index&logout=true'>Click Here</a> to reset.";
		
		

		if (isset($_POST["username"])) {
		
			$user = addslashes($_POST['username']);
			$pass = md5($_POST['password']);
			$result = mq("select id,url,username,password from [p]musicplayer where (url='$user' || username='$user') AND password='$pass'");
			$me_row = num($result);
			
			if ($me_row > 0) {
			
					while($row = mf($result)){
						$myid = $row["id"];
						
						//session_regenerate_id();
						$_SESSION['me'] = $row['id'];

						// Set cookie to expire in two months
						$inTwoMonths = 60 * 60 * 24 * 60 + time();
						// Set the user cookie
						setcookie($cookievar, $myid, $inTwoMonths);
						
						$status = "<p>{$logoutLink}</p>";
						$loginbox = "$status";
						refresh("1","http://www.myartistdna.fm");
						//refresh("1","?p=home");
						$homeloginbox = "
							$loginbox
							";

					}
			
			} else {
				$status = "
				<div class='notification tip'>Incorrect login. Please try again.</div>";
				$homeloginbox = "{$status}{$standardform}";
			}
			
		} else {
		
			if ($_SESSION['me'] != "") {
				$loginbox = "{$status}<p><a href='?p=home'>Administrator Page</a></p>";
				$homeloginbox = "$loginbox";
				header("Location: ?p=home");

			} else {
					
				$loginbox = $standardform;
				
				$homeloginbox = "
					$status
					$standardform
				";

			}
			
		}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		
		<!-- meta tags -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<!-- title of the page -->
		<title><?=siteTitle(); ?></title>	
		
		<!-- main CSS Stylesheet -->
		<link type="text/css" rel="stylesheet" media="all" href="<?=trueSiteUrl();?>/pages/style.css" />

	</head>
	
<body>

	<div id="login-page">
		<div id="login-wrapper">
			<div class="box-header-bg">
			<div class="box-header login">
				MyArtistDNA.fm <span class="fr"><a href="<?=trueSiteUrl();?>">Back to the site</a></span>
			</div>
			</div>

			<div class="box">
			<? 
				if ($_GET["forgot"] == "true") {
					include("forgot.php");
				} else if ($_GET["register"] == "true") {
					include("register.php");					
				} else {
					if ($_GET["logout"] == "true") {
						echo "Logging out...";
					} else {
						echo $homeloginbox; 
					}
				}
				
			?>
			</div>
		</div>
	</div>


</body>
</html>
