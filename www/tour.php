<?php
include ('header.php');
?>

<script src="js/tour.js" type="text/javascript"></script> 

<section id="wrapper">
<section id="content">

    <div id="help">
		<h2>WHAT IS IT?</h2>
		<div class="videos">
		<!-- first try HTML5 playback: if serving as XML, expand `controls` to `controls="controls"` and autoplay likewise -->
<!-- warning: playback does not work on iOS3 if you include the poster attribute! fixed in iOS4.0 -->
<video controls>
	<!-- MP4 must be first for iPad! -->
   <source src="http://www.myartistdna.com/mad.iphone.mp4" type="video/mp4" />
   <source src="http://www.myartistdna.com/mad.webm" type="video/webm" />
   <source src="http://www.myartistdna.com/mad.ogv" type="video/ogg" />	<!-- <source src="__VIDEO__.OGV" type="video/ogg" /> Firefox / Opera / Chrome10 -->
	
	<!-- fallback to Flash: 
	<object width="640" height="360" type="application/x-shockwave-flash" data="__FLASH__.SWF">
		<!-- Firefox uses the `data` attribute above, IE/Safari uses the param below -->
		<param name="movie" value="__FLASH__.SWF" />
		<param name="flashvars" value="controlbar=over&amp;image=__POSTER__.JPG&amp;file=__VIDEO__.MP4" />
		<!-- fallback image. note the title field below, put the title of the video there -->
		<img src="__VIDEO__.JPG" width="640" height="360" alt="__TITLE__"
		     title="No video playback capabilities, please download the video below" />
	</object>-->
</video>
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
		<div class="button_reset"><a href="/forgot_password.html">RESET PASSWORD</a></div>
		</div>
		</div>
		</div>
    
    </div><!-- help -->
	
</section>
</section>

<?php
include ('footer.php');
?>