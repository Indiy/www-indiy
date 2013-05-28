<?php
include ('header.php');
?>

<script src="js/tour.js" type="text/javascript"></script> 

<section id="wrapper">
<section id="content">

    <div id="help">
		<h2>NEED MORE INFO?</h2>
		<div class="videos">


<iframe src="http://player.vimeo.com/video/67163369" width="638" height="358" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>


		</div>
    
		<div class="info">
		<div class="contact">
		<h3>Contact Support Team</h3>
        <div id="contact_form">
            <fieldset>
            <ul>
            <li class="email"><label>Name</label> <input id="contact_name" type="text"  class="input" value="" /></li>
            <li class="email last"><label>Email Address</label> <input id="contact_email" type="text"  class="input" value="" /></li>
            <li><label>Subject</label> <input id="contact_subject" type="text"  class="input" /></li>
            <li><label>Description</label> <textarea id="contact_body" cols="" rows="" class="textarea"></textarea></li>
            </ul>
            <button class="button_submit" onclick="sendContactForm();"> SUBMIT</button> 
            </fieldset>
        </div>
        <div id="contact_success" style="display:none;">
        <br/>Thank you for your submission!<br/>
        <br/>
        Our support team will respond to you as soon as they can.<br/>
        </div>
		</div>
		
		<div class="checkout">
		<h5>NEED QUICK ANWERS? <br /> CHECK OUT OUR FAQ</h5>
		<p>Any additional questions or concerns please send us a message and our team will respond respectively. <a href="faq.php">GO</a></p>
		
		<div class="reset">
		<h3>Can’t Login?</h3>
		<p>Click the button below and we will email instructions to you on how to reset your password. </p>
		<div class="button_reset"><a href="/forgot_password.php">RESET PASSWORD</a></div>
		</div>
		</div>
		</div>
    
    </div><!-- help -->
	
</section>
</section>

<?php
include ('footer.php');
?>