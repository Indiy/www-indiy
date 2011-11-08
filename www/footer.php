<script type="text/javascript">
function send_ajax_login(o)
{			
	var username,password,msg;
	username = document.loginPopup.username.value;
	password = document.loginPopup.password.value;

	// Send the ajax request.
	 $.ajax({
	   type: "POST",
	   url: "check_login.php?username="+username+"&password="+password,
	   dataType: "json",
	   success: function(data){
            var result = data['result'];
            if( result == 0 )
            {	
                $("#validate-login").html("<span class='ui-error'>Wrong username or password. Please try again.</span>");					 
                 return false;
            }
            else if( result == 1 )
            {
                window.location.href=data['url'];	
                return true;
            }
            else
            {			
                $("#validate-login").html("<span class='ui-error'>Please enter the username and password.</span>");					 
                return false;
            },
        error: function()
        {
            $("#validate-login").html("<span class='ui-error'>Login Failed. Please try again.</span>");					 
        }
	   }
	  
	 });
}
</script>

<script type="text/javascript">
$(function() {
$(".submit").click(function() {
    var name = $("input#name").val();
	var email = $("input#email").val();
	var username = $("input#username").val();
	var password = $("input#password").val();
	
    var dataString = 'name='+ name  + '&username='+ username + '&email=' + email + '&password=' + password;
	//alert (dataString);return false;
	
	if (!document.form.agree.checked) {
		alert("You must agree to the terms.");
		return false;
	} 
	if(name=='' || email=='' || username=='' || password=='' )
	{	
		$('.success').fadeOut(200).hide();
		$('.error').fadeOut(200).show();	
	}	
	else
	{
		$.ajax({  
			type: "POST", 
			url: "join_now.php",  
			dataType: "html",
			data: dataString,
			success: function(msg){
				//alert(msg);
				$('.success').fadeIn(200).show();
				$('.error').fadeOut(200).hide();
				
				//empty the all fields
				document.form.name.value='';
				document.form.email.value='';
				document.form.username.value='';
				document.form.password.value='';
				document.form.agree.checked = false;
			}
		});
}	

    return false;
	});
});

</script>
<style type="text/css">
body{
background:#f6f6f6;
}
.error{
	color:#d12f19;
	font-size:12px;
	font-weight:bold;
	}
	.success{
	color:#006600;
	font-size:12px;
	font-weight:bold;
	}
</style>
<section id="footer">
<footer>

    <aside>
    <h5>ARTIST</h5>
    <ul>
    <li><a href="artists.php">All</a></li>
    <li><a href="music.php">Music</a></li>
    <li><a href="art.php">Art</a></li>
  
    </ul>
    </aside>

    <aside class="benefits">
    <h5>BENEFITS</h5>
    <ul>
     <li><a href="be-heard.html">Be Heard</a></li>
     <li><a href="be-seen.html">Be Seen</a></li>
     <li><a href="be-independent.html">Be Independent</a></li>
    </ul>
    </aside>

    <aside>
    <h5>SUPPORT</h5>
    <ul>
    <li><a href="faq.html">FAQs</a></li>
    <li><a href="tour.php">Help</a></li>
    <li><a href="privacy.html">Privacy</a></li>
    <li><a href="terms_service.html">Terms</a></li>
    </ul>
    </aside>

    <aside>
    <h5>AFFILIATES</h5>
    <ul>
    <li><a href="http://myartistdna.is">MAD.is</a></li>
    <li><a href="http://myartistdna.fm">MyAD.fm</a></li>
       <li><a href="http://myartistdna.tv">MAD.tv</a></li>
    </ul>
    </aside>

    <article>
    <h5>STAY CONNECTED</h5>
    <ul>
    <li><a href="http://facebook.com/pages/MyArtistDNA/106114012796731" target="_blank"><span><img src="images/facebook.gif" alt="Facebook"></span> Become a fan on Facebook</a></li>
    <li><a href="http://twitter.com/myartistdna" target="_blank"><span><img src="images/twitter.gif" alt="Twitter"></span> Follow us on Twitter</a></li>
    </ul>
    </article>

    <div class="logo"><a href="http://myartistdna.is"><img src="images/MYARTISTDNA_footer.gif" alt="MYARTISTDNA"></a></div>
    <p>&copy; 2011 <a href="http://levasent.com">Levas Entertainment</a>, All rights reserved</p>

</footer>
</section>
<!-- FOOTER -->
</section>
<!-- FOOTER -->

<!-- SIGNUP FORM -->
<div id="boxes"> 
<div id="dialog" class="window">
<div id="popup">
    <div class="topbox">
    <h3>SIGN UP FOR MYARTISTDNA</h3>
    <div class="close"><a href="#">CLOSE</a></div>
	</div>
    
    <div class="offer">
    <h2><span>You selected:</span> <br> Basic Package</h2>
    <h3>FREE</h3>
    </div>
	
    <div class="sign_up">
    <article>
    <h5>GET STARTED NOW</h5>
    <p>Log in  and get started easily using your existing Facebook <br /> or Twitter account </p>
	<div class="socialmedia">
    <ul>
    <li><a href="Login_Twitbook/login-facebook.php"><img src="images/facebook.jpg" alt=""></a></li>
    <li><a href="Login_Twitbook/login-twitter.php"><img src="images/twitter.jpg" alt=""></a></li>
    </ul>
    </div>
    </article>
    <!--
    <div class="or">OR</div>
    <span class="error" style="display:none">Please fill up all required fields.</span>
	<span class="success" style="display:none">Registration Successfull.</span>

    <article>
    <h5>Create Login</h5>	 
     <form autocomplete="off" enctype="multipart/form-data" method="post" name="form">
		<fieldset>
		<ul>
			<li><label>Name</label> <input name="name" id="name" type="text" class="input" value="" /></li>			
			<li><label>Email Address</label> <input name="email" id="email" type="text" class="input" value="" /></li>
			<li><label>Username</label> <input name="username" id="username" type="text" class="input" value="" /></li>
			<li><label>Password</label> <input name="password" id="password" type="password" class="input" value="" /></li>
			<li><input name="agree" id="agree" type="checkbox" value="agree"> <span>I agree to the <a href="#">Terms &amp; Conditions</a> of MyArtistDNA</span></li>
		</ul>
		<div class="button"><a href="#" class="submit">Complete Signup</a></div>
		</fieldset>
    </form>
    </article>
    -->
    </div> 
</div><!-- pop up -->
</div>
<!-- END SIGNUP FORM -->

<!-- LOGIN FORM -->
<div id="dialog2" class="window">
<div id="popup">
    <div class="topbox">
    <h3>LOG IN TO MYARTISTDNA</h3>
    <div class="close"><a href="#">CLOSE</a></div>
	</div>
	
    <div class="loginpop">
	<div id="validate-login"></div>
    <form action="" name="loginPopup" method="post">
    <fieldset>
    <ul>
    <li><label>Email Address</label> <input name="username" type="text" class="input" value="" /></li>
    <li><label>Password</label> <input name="password" type="password" class="input" value="" /></li>
    </ul>
    <p class="password"><a href="http://www.myartistdna.com?p=index&forgot=true">Forgot your password?</a></p>
    <div class="button"><a href="#-1" onclick="send_ajax_login('validate-login');">LOGIN</a></div>
    </fieldset>
    </form>
    <h5 class="option">OR</h5>
    
    <article>
    <h5>LOG IN WITH YOUR SOCIAL ACCOUNT</h5>
    <p>Log in  and get started easily using your existing Facebook <br /> or Twitter account</p>

	<div class="socialmedia">
    <ul>
    <li><a href="Login_Twitbook/login-facebook.php"><img src="images/facebook.jpg" alt="Facebook"></a></li>
    <li><a href="Login_Twitbook/login-twitter.php"><img src="images/twitter.jpg" alt="Twitter"></a></li>
    </ul>
    </div>
    </article>
    
    <div class="bottombox">
    <h3>NOT A MEMBER Yet?</h3>
    <div class="buttonsignup"><a href="signup_popup_step1.html">SIGN UP</a></div>
    </div>
    </div>
</div><!-- pop up -->
</div> 
<!-- END LOGIN FORM -->

<!-- Mask to cover the whole screen --> 
<div id="mask"></div> 
</div>

<!-- Tracking code Starts --> 
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-15194524-1']);
  _gaq.push(['_setDomainName', '.myartistdna.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!-- Tracking code Ends -->
</body>
</html>