
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
    <li><a href="community.php">All</a></li>
  
    </ul>
    </aside>

    <aside>
    <h5>SUPPORT</h5>
    <ul>
    <li><a href="faq.php">FAQ</a></li>
    <li><a href="tour.php">Contact Us</a></li>
    </ul>
    </aside>

    <aside>
    <h5>MAD NETWORK</h5>
    <ul>
    <li><a href="http://myartistdna.com/fan">MAD Fan</a></li>
    <li><a href="http://blog.myartistdna.com">MAD Blog</a></li>
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
    <li><a onclick='showNewsletter();'><span><img src="images/email.gif" alt="Email"></span> Join Newsletter</a></li>

    </ul>
    </article>

    <div class="logo"><a href="home.php"><img src="images/MYARTISTDNA_footer.gif" alt="MYARTISTDNA"></a></div>
    <p>&copy; 2012 Powered by MyArtistDNA</p>

</footer>
</section>
<!-- FOOTER -->
</section>
<!-- FOOTER -->

<div id='newsletter_mask' style='display: none;'></div>
<div id='newsletter_wrapper' style='display: none;'>
    <div id='newsletter_container'>
        <div class='top_bar'>
            <h2>Invite Friends</h2>
            <button onclick='closeNewsletter();'>CLOSE</button>
        </div>

        <div class='top_blue_bar'></div>
        <div class='top_sep'></div>
        <div id='newsletter_form'>
            <div class='flow_container'>
                <div class='line_label'>Please enter your name below and you will be registered for our newsleter.</div>
                <input id='newsletter_email' class='line_text'/>
            </div>
            <div class='submit_container'>
                <button class="submit" onclick='submitNewsletter();'>SUBMIT</button>
            </div>
        </div>
        <div id='newsletter_success' class='form_message' style='display: none;'>
        Thank you for your email.  You will be added to our newsletter list.
        </div>

        <div class='bottom_sep'></div>
        <div class='bottom_blue_bar'></div>
        </div>
</div>

<!-- Tracking code Starts --> 
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-15194524-1']);
  _gaq.push(['_setDomainName', 'myartistdna.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!-- Tracking code Ends -->

<script type="text/javascript" charset="utf-8">
    var is_ssl = ("https:" == document.location.protocol);
    var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
    document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript" charset="utf-8">
    var feedback_widget_options = {};

    feedback_widget_options.display = "overlay";  
    feedback_widget_options.company = "myartistdna";
    feedback_widget_options.placement = "left";
    feedback_widget_options.color = "#222";
    feedback_widget_options.style = "idea";
    var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
</script>

</body>
</html>
