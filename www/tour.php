<?php
include ('header.php');
?>

<section id="wrapper">
<section id="content">

    <div id="help">
		<h2>WHAT IS IT?</h2>
		<div class="videos">
		<!-- first try HTML5 playback: if serving as XML, expand `controls` to `controls="controls"` and autoplay likewise -->
<!-- warning: playback does not work on iOS3 if you include the poster attribute! fixed in iOS4.0 -->
<video width="638" height="358" controls>
	<!-- MP4 must be first for iPad! -->
	<source src="__MASTER.mp4" type="video/mp4" /><!-- Safari / iOS video    -->
	<!-- <source src="__VIDEO__.OGV" type="video/ogg" /> Firefox / Opera / Chrome10 -->
	
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
		<fieldset>
		<ul>
		<li class="email"><label>Name</label> <input name="" type="text"  class="input" value="John Doe" /></li>
		<li class="email last"><label>Email Address</label> <input name="" type="text"  class="input" value="email.address@domain.com" /></li>
		<li><label>Subject</label> <input name="" type="text"  class="input" /></li>
		<li><label>Description</label> <textarea name="" cols="" rows="" class="textarea"></textarea></li>
		</ul>
		<input name="" type="submit" class="button_submit" value="SUBMIT"> 
		</fieldset>
		</div>
		
		<div class="checkout">
		<h5>NEED QUICK ANWERS? <br /> CHECK OUT OUR FAQ</h5>
		<p>Cras vel sem consequat neque aliquam sagittis venenatis quis tortor. Duis nec lorem ut mi eleifend tincidunt. Donec a ornare. <a href="#">GO</a></p>
		
		<div class="reset">
		<h3>Can’t Login?</h3>
		<p>Cras vel sem consequat neque aliquam sagittis venenatis quis tortor. </p>
		<div class="button_reset"><a href="#">RESET PASSWORD</a></div>
		</div>
		</div>
		</div>
    
    </div><!-- help -->
	
</section>
</section>

<?php
include ('footer.php');
?>