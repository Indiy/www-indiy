
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
     <li><a href="be-heard.php">Be Heard</a></li>
     <li><a href="be-seen.php">Be Seen</a></li>
     <li><a href="be-independent.php">Be Independent</a></li>
    </ul>
    </aside>

    <aside>
    <h5>SUPPORT</h5>
    <ul>
    <li><a href="faq.php">FAQs</a></li>
    <li><a href="tour.php">Help</a></li>
    <li><a href="privacy.php">Privacy</a></li>
    <li><a href="terms_service.php">Terms</a></li>
    </ul>
    </aside>

    <aside>
    <h5>AFFILIATES</h5>
    <ul>
    <li><a href="http://myartistdna.is">MAD.is</a></li>
    <li><a href="http://myartistdna.fm">MAD.fm</a></li>
    <li><a href="http://myartistdna.tv">MAD.tv</a></li>
    </ul>
    </aside>

    <article>
    <h5>STAY CONNECTED</h5>
    <ul>
    <li><a href="http://facebook.com/pages/MyArtistDNA/106114012796731" target="_blank"><span><img src="images/facebook.gif" alt="Facebook"></span> Become a fan on Facebook</a></li>
    <li><a href="http://twitter.com/myartistdna" target="_blank"><span><img src="images/twitter.gif" alt="Twitter"></span> Follow us on Twitter</a></li>
    <li><a href="#" target="_blank"><span><img src="images/email.gif" alt="Email"></span> Join Newsletter</a></li>

    </ul>
    </article>

    <div class="logo"><a href="home.php"><img src="images/MYARTISTDNA_footer.gif" alt="MYARTISTDNA"></a></div>
    <p>&copy; 2011 <a href="http://levasent.com">Levas Entertainment</a>, All rights reserved</p>

</footer>
</section>
<!-- FOOTER -->
</section>
<!-- FOOTER -->

<?php
include_once 'includes/login_signup.html';
?>

<style type="text/css">

#newsletter_mask
{
    position: fixed;
    left: 0; 
    top: 0;
    width: 100%;
    height: 100%;
    background: #080808;
    opacity: 0.65;
    z-index: 9049;
}

#newsletter_wrapper, #newsletter_container {
    width: 500px;
}
#newsletter_wrapper  {
    bottom: 50%;
    right: 50%;
    position: fixed;
    max-height: 100%;
    max-width: 100%;
}
#newsletter_container {
    left: 50%;
    position: relative;
    top: 50%;
    background-color: white;
    z-index: 9050;
}
.top_bar {
    width: 100%;
    height: 50px;
    background: url("/images/bg_login01.gif") repeat-x;
    background-size: auto 100%;
}
.top_bar h2 {
    float:left;
    padding-left: 20px;
    padding-top: 17px;
    color: white;
    font-size: 22px;
    font-weight: normal;
    color: #ddd;
}
.top_bar button {
    height: 100%;
    width: 57px;
    padding-left: 10px;
    padding-right: 4px;
    color: #51C3C4;
    border: 0 none;
    float: right;
    font-size: 12px;
    background: url("/images/bg_close.gif") no-repeat;
    cursor: pointer;
}
.top_blue_bar, .bottom_blue_bar {
    background-color: #51C3C4;
    height: 10px;
}
.top_sep {
    height: 1px;
    padding-bottom: 20px;
} 
.bottom_sep {
    height: 15px;
}
.flow_container {
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 15px;
}
.line_label {
    font-size: 12px;
    font-family: sans-serif;
    color: #555;
    padding-bottom: 15px;
}
.line_text {
    width: 100%;
    height: 20px;
    padding-left: 3px;
    border-radius: 3px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border: 1px solid #ccc;
}
.submit_container {
    text-align: center;
}
.submit {
    width: 150px;
    height: 40px;
    color: #51C3C4;
    background-color: black;
    border: 0 none;
    text-transform: uppercase;
    font-size: 14px;
    font-family: sans-serif;
    cursor: pointer;
}
.form_message {
    text-align: center;
    width: 100%;
    height: 30px;
    padding-top: 20px;
}

</style>
<div id='newsletter_mask' style='display: none;'></div>
<div id='newsletter_wrapper' style='display: none;'>
    <div id='newsletter_container'>
        <div class='top_bar'>
            <h2>Invite Friends</h2>
            <button onclick='closeNewsletter();'>CLOSE</button>
        </div>

        <div class='top_blue_bar'></div>
        <div class='top_sep'></div>
        <div class='flow_container'>
            <div class='line_label'>Please enter your name below and you will be registered for our newsleter.</div>
            <input id='newsletter_email' class='line_text'/>
        </div>
        <div class='submit_container'>
            <button class="submit" onclick='onInviteFriends();'>Send</button>
        </div>
        <div id='status' class='form_status' style='display: none;'></div>

        <div class='bottom_sep'></div>
        <div class='bottom_blue_bar'></div>
        </div>
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
