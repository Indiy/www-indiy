<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<!--
	Supersized - Fullscreen Background jQuery Plugin
	Version 3.1.3 Core
	www.buildinternet.com/project/supersized
	
	By Sam Dunn / One Mighty Roar (www.onemightyroar.com)
	Released under MIT License / GPL License
	-->

	<head>
		
		<title>Supersized - Fullscreen Background jQuery Plugin</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		
		<link rel="stylesheet" href="css/supersized.core.css" type="text/css" media="screen" />

		
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
		<script type="text/javascript" src="js/supersized.3.1.3.core.min.js"></script>
		
		<script type="text/javascript">  
		
			jQuery(function($){
				$.supersized({
					//Background image
					slides	:  [ { image : 'http://www.myartistdna.fm/artists/images/53_90876_ladygaga-ads.jpg' } ]					
				});
			});
			
		</script>
		
		<style type="text/css">
			
			/*Demo Styles*/
			p{ padding:0 30px 30px 30px; color:#fff; font:11pt "Helvetica Neue", "Helvetica", Arial, sans-serif; text-shadow: #000 0px 1px 0px; line-height:200%; }
				p a{ font-size:10pt; text-decoration:none; outline: none; color:#ddd; background:#222; border-top:1px solid #333; padding:5px 8px; -moz-border-radius:3px; -webkit-border-radius:3px; border-radius:3px; -moz-box-shadow: 0px 1px 1px #000; -webkit-box-shadow: 0px 1px 1px #000; box-shadow: 0px 1px 1px #000; }
					p a:hover{ background-color:#427cb4; border-color:#5c94cb; color:#fff; }
			h3{ padding:30px 30px 20px 30px; }
			
			#content{ background:#111; background:rgb(0,0,0); background:rgba(0,0,0,0.75); width:720px; height:800px; margin:30px auto; text-align:left; }
			.plugin-logo{ float:right; }
			.stamp{ float: right; margin: 15px 30px 0 0;}
			
		</style>
	
	</head>

<body>
		
	<div id="content">

		<h3><a href="http://www.buildinternet.com" ><img src="img/buildinternet-logo.png"/></a> <a href="http://www.buildinternet.com/project/supersized" class="plugin-logo"><img src="img/supersized-logo.png"/></a></h3>
		<p>
			This demo uses Supersized only for the fullscreen background functionality, with all default options to reduce code.<br/><br/>
			Image credit:<br/>
			<a href="http://www.nonsensesociety.com/2010/12/kitty-gallannaugh/" alt="Nonsense Society" target="_blank">Kitty Gallannaugh</a>
			<br/><br/>
			Project page:<br/>

			<a href="http://www.buildinternet.com/project/supersized" alt="Supersized Project Page" target="_blank">Supersized Project</a>
		</p>
	</div>

</body>
</html>
