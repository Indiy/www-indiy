<?php
include ('header.php');
?>

<script src="js/tour.js" type="text/javascript"></script> 

<section id="wrapper">
<section id="content">

    <div id="help">
		<h2>WHAT IS IT?</h2>
		   <div class="video-js-box vim-css">
                    <video id="mad_video_1" class="video-js" width="853" height="480" controls="controls" preload="auto" poster="/images/mad_poster.png">
                        <source src="http://www.myartistdna.com/mad.iphone.mp4" type="video/mp4" />
                        <source src="http://www.myartistdna.com/mad.webm" type="video/webm" />
                        <source src="http://www.myartistdna.com/mad.ogv" type="video/ogg" />
                        <!-- Flash Fallback. Use any flash video player here. Make sure to keep the vjs-flash-fallback class. -->
                        <object id="flash_fallback_1" class="vjs-flash-fallback" width="640" height="264" type="application/x-shockwave-flash"
                            data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
                            <param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
                            <param name="allowfullscreen" value="true" />
                            <param name="flashvars" value='config={"playlist":["http://www.myartistdna.com/images/mad_poster.png", {"url": "http://www.myartistdna.com/mad.mp4","autoPlay":false,"autoBuffering":true}]}' />
                            <!-- Image Fallback. Typically the same as the poster image. -->
                            <img src="/images/mad_poster.png" width="853" height="480" alt="Poster Image"
                            title="No video playback capabilities." />
                        </object>
                    </video>
                </div>
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