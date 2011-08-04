<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>MyArtistDNA.fm - Radagun</title> 
<link href="http://www.myartistdna.fm/jplayer/new-style.css" rel="stylesheet" type="text/css" /> 
 
<link media="only screen and (max-device-width: 480px)" href="http://www.myartistdna.fm/jplayer/iphone.css" type= "text/css" rel="stylesheet" /> 
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" /> 
 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script> 
<script type="text/javascript" src="jplayer/js/jquery.simplyscroll-1.0.4.js"></script> 
 
<script type="text/javascript" src="http://www.myartistdna.fm/jplayer/jquery.jplayer.min.js"></script> 
<script type="text/javascript" src="http://www.myartistdna.fm/jplayer/demos.common.js"></script> 
	<script> 
		$(document).ready(function(){
 
			$('#image').hide();
			$('.page').hide();
			$('.comments').hide();
			$('.contact').hide();
			$('.aClose').hide();
			$('.store').hide();
			$('.checkout').hide();
			
			/* Close */
			$('.aClose').click(function() {
				$('.aClose').fadeOut();
				$('.comments').fadeOut();
				$('.contact').fadeOut();
				$('.store').fadeOut();
				$('.page').fadeOut();
			});
			
			/* Playlist Controller */
			$("#playlistaction").click(function(){
				$(this).parent(".jp-playlist").animate({"left": "0px"}, "fast");
				$(this).hide();
				$(this).parent(".jp-playlist").children("#playlisthide").show();
			});
			$("#playlisthide").click(function(){
				$(this).parent(".jp-playlist").animate({"left": "-233px"}, "fast");
				$(this).hide();
				$(this).parent(".jp-playlist").children("#playlistaction").show();
			});
			
			
			/* Tour */
			$(".a14").click(function() {
				$(".comments").fadeOut();
				$(".contact").fadeOut();
				$(".store").fadeOut();
				$(".page").hide();
				$(".page").html("<div class='pageload'><img src='http://www.myartistdna.fm/jplayer/images/page-loader.gif' border='0' /></div>").fadeIn();
				$(".aClose").fadeIn();
				var body14 = "&artist=1&page=14";
				$.post("jplayer/ajax.php", body14, function(data14) {
					$(".page").html(data14);
				});
			});
		
 
			/* News */
			$(".a16").click(function() {
				$(".comments").fadeOut();
				$(".contact").fadeOut();
				$(".store").fadeOut();
				$(".page").hide();
				$(".page").html("<div class='pageload'><img src='http://www.myartistdna.fm/jplayer/images/page-loader.gif' border='0' /></div>").fadeIn();
				$(".aClose").fadeIn();
				var body16 = "&artist=1&page=16";
				$.post("jplayer/ajax.php", body16, function(data16) {
					$(".page").html(data16);
				});
			});
		
 
			/* Biography */
			$(".a15").click(function() {
				$(".comments").fadeOut();
				$(".contact").fadeOut();
				$(".store").fadeOut();
				$(".page").hide();
				$(".page").html("<div class='pageload'><img src='http://www.myartistdna.fm/jplayer/images/page-loader.gif' border='0' /></div>").fadeIn();
				$(".aClose").fadeIn();
				var body15 = "&artist=1&page=15";
				$.post("jplayer/ajax.php", body15, function(data15) {
					$(".page").html(data15);
				});
			});
		
 
			/* Video */
			$(".a13").click(function() {
				$(".comments").fadeOut();
				$(".contact").fadeOut();
				$(".store").fadeOut();
				$(".page").hide();
				$(".page").html("<div class='pageload'><img src='http://www.myartistdna.fm/jplayer/images/page-loader.gif' border='0' /></div>").fadeIn();
				$(".aClose").fadeIn();
				var body13 = "&artist=1&page=13";
				$.post("jplayer/ajax.php", body13, function(data13) {
					$(".page").html(data13);
				});
			});
		
 
			/* Radio */
			$(".a12").click(function() {
				$(".comments").fadeOut();
				$(".contact").fadeOut();
				$(".store").fadeOut();
				$(".page").hide();
				$(".page").html("<div class='pageload'><img src='http://www.myartistdna.fm/jplayer/images/page-loader.gif' border='0' /></div>").fadeIn();
				$(".aClose").fadeIn();
				var body12 = "&artist=1&page=12";
				$.post("jplayer/ajax.php", body12, function(data12) {
					$(".page").html(data12);
				});
			});
		
			
			/* Comment */
			$('.aComment').click(function() {
				$('.page').fadeOut();
				$('.store').fadeOut();
				$('.contact').fadeOut();
				setTimeout(function(){ 
					$('.comments').fadeIn();
					$('.aClose').fadeIn();
				}, 450);
			});
			
			/* Contact */
			$('.aContact').click(function() {
				$('.page').fadeOut();
				$('.store').fadeOut();
				$('.comments').fadeOut();
				setTimeout(function(){ 
					$('.contact').fadeIn();
					$('.aClose').fadeIn();
				}, 450);
			});
			
			/* Store */
			$('.aStore').click(function() {
				$('.page').fadeOut();
				$('.comments').fadeOut();
				$('.contact').fadeOut();
				setTimeout(function(){ 
					$('.store').fadeIn();
					$('.aClose').fadeIn();
				}, 450);
			});
			
			
			
			/* $('#retract').hide(); */
			/*
			
			setTimeout(function(){ 
				$('#playlist').slideDown();
			}, 1000);
			
			$('#playTitle').click(function() {
				$('#retract').slideToggle();
			});
			*/
			// Facebook
			
			$(".facebookstreamhide").hide();
			
			$(".facebookstreamhide").click(function(event){
				$(".facebookstreamhide").hide();
				$(".facebookstreamshow").show();
				$(".facebookstream").animate({
					marginTop: "-40px"
				}, 500);
			});
			
			$(".facebookstreamshow").click(function(event){
				$(".facebookstreamshow").hide();
				$(".facebookstreamhide").show();
				$(".facebookstream").animate({
					marginTop: "-410px"
				}, 500);
			});
			
			// Twitter
			
			$(".twitterstreamhide").hide();
			
			$(".twitterstreamhide").click(function(event){
				$(".twitterstreamhide").hide();
				$(".twitterstreamshow").show();
				$(".twitterstream").animate({
					marginTop: "-40px"
				}, 500);
			});
			
			$(".twitterstreamshow").click(function(event){
				$(".twitterstreamshow").hide();
				$(".twitterstreamhide").show();
				$(".twitterstream").animate({
					marginTop: "-410px"
				}, 500);
			});
			
			// Shopping Cart Functionality
			$("div.addtocart").click(function(event){
				var pro = $(this).text();
				var cart = "&paypal=youngfonz@gmail.com&cart=true&artist=1&product="+pro;
				$.post("jplayer/ajax.php", cart, function(items) {
					$("ul.products").hide();
					//alert(items);
					$(".cart").html(items);
					$(".cart").show();
				});
			});
			
			$("div.showstore").click(function(event){
				$(".cart").hide();
				$("ul.products").fadeIn();
			});
			$("div.showcart").click(function(event){
				$("ul.products").hide();
				$(".cart").fadeIn();
			});
			
			$("a.jp-previous").mouseover(function(event){
				$(this).children("span.vtip").stop().animate({"left": "0px"}, "fast");
			});
			
			$("a.jp-previous").mouseout(function(event){
				$(this).children("span.vtip").stop().animate({"left": "-200px"}, "fast");
			});	
			
			$("a.jp-next").mouseover(function(event){
				$(this).children("span.vtip").stop().animate({"right": "30px"}, "fast");
			});
			
			$("a.jp-next").mouseout(function(event){
				$(this).children("span.vtip").stop().animate({"right": "-140px"}, "fast");
			});
			
		});
	</script>	
 
<body> 
 
<script type="text/javascript"> 
<!--
$(document).ready(function(){
 
	var playItem = 0;
 
	var myPlayList = [
		// {name:"Over",mp3:"http://www.myartistdna.fm/artists/audio/1_99957_radagun-over.mp3",download:"",image:"1_34846_499_5-pack-guitar-picks.jpg",bgcolor:"0ca2cf",bgrepeat:"repeat",bgposition:"center center"},{name:"Better's Never Right",mp3:"http://www.myartistdna.fm/artists/audio/1_44734_02-betters-never-right.mp3",download:"<a href='download.php?artist=1&id=11' title='Click here download Better's Never Right for free' class='download vtip'>Download</a> ",image:"1_43605_album.jpg",bgcolor:"000000",bgrepeat:"repeat-x",bgposition:"center center"},{name:"Don't Rush",mp3:"http://www.myartistdna.fm/artists/audio/1_50613_03-dont-rush.mp3",download:"<a href='download.php?artist=1&id=10' title='Click here download Don't Rush for free' class='download vtip'>Download</a> ",image:"1_97745_sub.jpg",bgcolor:"ffffff",bgrepeat:"no-repeat",bgposition:"center center"},{name:"Haunted",mp3:"http://www.myartistdna.fm/artists/audio/1_86669_04-haunted.mp3",download:"<a href='download.php?artist=1&id=2' title='Click here download Haunted for free' class='download vtip'>Download</a> ",image:"1_19828_haunted.jpg",bgcolor:"000000",bgrepeat:"no-repeat",bgposition:"center center"},{name:"Check Me Out",mp3:"http://www.myartistdna.fm/artists/audio/1_44130_radagun-checkmeout.mp3",download:"<a href='download.php?artist=1&id=9' title='Click here download Check Me Out for free' class='download vtip'>Download</a> ",image:"1_74935_983_life-lessons-album.jpg",bgcolor:"000000",bgrepeat:"no-repeat",bgposition:"bottom center"}	
		{name:"Over",mp3:"http://www.myartistdna.fm/artists/audio/1_99957_radagun-over.mp3",download:"",image:"1_23773_tron.jpg",bgcolor:"ffffff",bgrepeat:"stretch",bgposition:"center center"}
	];
 
	// Local copy of jQuery selectors, for performance.
	var jpPlayTime = $("#jplayer_play_time");
	var jpTotalTime = $("#jplayer_total_time");
	var jpStatus = $("#demo_status"); // For displaying information about jPlayer's status in the demo page
 
	$("#jquery_jplayer").jPlayer({
		ready: function() {
			displayPlayList();
			playListInit(true); // Parameter is a boolean for autoplay.
		},
		oggSupport: false
	})
	.jPlayer("onProgressChange", function(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime) {
		jpPlayTime.text($.jPlayer.convertTime(playedTime));
		jpTotalTime.text($.jPlayer.convertTime(totalTime));
 
		demoStatusInfo(this.element, jpStatus); // This displays information about jPlayer's status in the demo page
	})
	.jPlayer("onSoundComplete", function() {
		playListNext();
	});
 
	$("#jplayer_previous").click( function() {
		playListPrev();
		$(this).blur();
		return false;
	});
 
	$("#jplayer_next").click( function() {
		playListNext();
		$(this).blur();
		return false;
	});
 
	function displayPlayList() {
		$("#jplayer_playlist ul").empty();
		for (i=0; i < myPlayList.length; i++) {
			var listItem = (i == myPlayList.length-1) ? "<li class='jplayer_playlist_item_last'>" : "<li>";
			listItem += myPlayList[i].download + "<a href='#' id='jplayer_playlist_item_" + i + "' tabindex='1'>" + myPlayList[i].name + "<span class='songimage' style='display: none;'>" + myPlayList[i].image + "</span><div class='songbgcolor' style='display: none;'>" + myPlayList[i].bgcolor + "</div><div class='songbgposition' style='display: none;'>" + myPlayList[i].bgposition + "</div><div class='songbgrepeat' style='display: none;'>" + myPlayList[i].bgrepeat + "</div></a>";
			listItem += "<div class='clear'></div></li>";
			$("#jplayer_playlist ul").append(listItem);
			$("#jplayer_playlist_item_"+i).data( "index", i ).click( function() {
				var index = $(this).data("index");
				if (playItem != index) {
					playListChange( index );
				} else {
					$("#jquery_jplayer").jPlayer("play");
				}
				$(this).blur();
				return false;
			});
		}
	}
 
	function playListInit(autoplay) {
		if(autoplay) {
			playListChange( playItem );
		} else {
			playListConfig( playItem );
		}
	}
 
	function playListConfig( index ) {
		$("#jplayer_playlist_item_"+playItem).removeClass("jplayer_playlist_current").parent().removeClass("jplayer_playlist_current");
		$("#jplayer_playlist_item_"+index).addClass("jplayer_playlist_current").parent().addClass("jplayer_playlist_current");
		
		playItem = index;
		$("#jquery_jplayer").jPlayer("setFile", myPlayList[playItem].mp3);
		
			// Display Image			
			$('#loader').show();
			$('#image').hide();
			
			// Get Current Image
			var image = $("#jplayer_playlist_item_"+index).children("span.songimage").text();
			var color = $("#jplayer_playlist_item_"+index).children("div.songbgcolor").text();
			var position = $("#jplayer_playlist_item_"+index).children("div.songbgposition").text();
			var repeat = $("#jplayer_playlist_item_"+index).children("div.songbgrepeat").text();
			
			//alert(image + " - " + color + " - " + position + " - " + repeat);
			//$('#image').css("background-image", "url('http://www.myartistdna.fm/artists/images/"+image+"')");// OLD VERSION
			
			if (repeat == "stretch") {
				//alert("stretching");
				$('#image').html("<img src='http://www.myartistdna.fm/artists/images/"+image+"' width='100%' style='vertical-align:middle;' />");	
			} else {
				//alert(image + " - " + color + " - " + position + " - " + repeat);
				//$('#image').css("background-image", "url('http://www.myartistdna.fm/artists/images/"+image+"')");
				//$('#image').css("backgroundPosition", position);
				$('#image').html("");
				$('#image').css({
					backgroundImage: "url('http://www.myartistdna.fm/artists/images/" + image + "')", backgroundRepeat: repeat, backgroundPosition: position
				});
			}
			
			$('#image').css("background-color", "#"+color);
			$('#image').fadeIn();
			
			// Display Current Track Title
			var track = "&track="+image;
			$.post('jplayer/ajax.php', track, function(data) {
					$('.current-track').html(data);
			});
			
			setTimeout(function(){ 
				$('#loader').hide();
			}, 1000);
	}
 
	function playListChange( index ) {
		playListConfig( index );
		$("#jquery_jplayer").jPlayer("play");
	}
 
	function playListNext() {
		var index = (playItem+1 < myPlayList.length) ? playItem+1 : 0;
		playListChange( index );
	}
 
	function playListPrev() {
		var index = (playItem-1 >= 0) ? playItem-1 : myPlayList.length-1;
		playListChange( index );
	}
		
 
	$(".submitform").click(function(event){
	
		var loading = "Message pending...";
		$(".notify").slideToggle();
		$(".notify").html(loading);
		
		// Grab the current data
		var from = "todd@radagun.com";
		var pname = document.getElementById('name').value;
		var pemail = document.getElementById('email').value;
		var pphone = document.getElementById('phone').value;
		var pcomments = document.getElementById('comments').value;
		var submit = "&form=send&from=" + from + "&name=" + pname + "&email=" + pemail + "&phone=" + pphone + "&comments=" + pcomments;
		
		//submit the data for processing
		$.post("jplayer/ajax.php", submit, function(response) {
			$(".notify").html(response);
			setTimeout('$(".notify").slideToggle()', 3000);	
			document.getElementById('name').value = "Name...";
			document.getElementById('email').value = "Email...";
			document.getElementById('phone').value = "Phone...";
			document.getElementById('comments').value = "Message...";
		});
		
		return false;
		
	});
	
	
});
 
	// Clear empty form
	function clickclear(thisfield, defaulttext) {
		if (thisfield.value == defaulttext) {
		thisfield.value = "";
		}
	}	
 
	// Refill empty form
	function clickrecall(thisfield, defaulttext) {
		if (thisfield.value == "") {
		thisfield.value = defaulttext;
		}
	}
 
-->
</script> 
 
			<div id="results"></div>
			
 
			<div class="facebookstream"> 
				<div class="facebookstreamhide"></div> 
				<div class="facebookstreamshow"></div> 
				<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fradagun&amp;width=292&amp;colorscheme=dark&amp;show_faces=false&amp;stream=true&amp;header=false&amp;height=415" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:395px;" allowTransparency="true"></iframe> 
			</div> 
						
			
			<div class="twitterstream"> 
				<div class="twitterstreamhide"></div> 
				<div class="twitterstreamshow"></div> 
				<script src="http://widgets.twimg.com/j/2/widget.js"></script> 
				<script> 
				new TWTR.Widget({
				  version: 2,
				  type: 'profile',
				  rpp: 5,
				  interval: 6000,
				  width: 292,
				  height: 315,
				  theme: {
					shell: {
					  background: '#333333',
					  color: '#ffffff'
					},
					tweets: {
					  background: '#000000',
					  color: '#ffffff',
					  links: '#4aed05'
					}
				  },
				  features: {
					scrollbar: true,
					loop: false,
					live: false,
					hashtags: true,
					timestamp: true,
					avatars: true,
					behavior: 'all'
				  }
				}).render().setUser('radagun').start();
				</script> 
			</div> 
			
						
			
			<div id="image"></div> 
			<div id="loader"><img src="http://www.myartistdna.fm/jplayer/images/ajax-loader.gif" /></div> 
			
			<div id="logo"> 
				<img src="http://www.myartistdna.fm/artists/images/83732_radagun_we-just-woke-up_free-music.jpg" />			
			</div> 
						
			
			<div id="navigation"> 
				<ul> 
					<li><a href="#" class="a14">Tour</a></li>
					<li><a href="#" class="a16">News</a></li> 
					<li><a href="#" class="a15">Biography</a></li> 
					<li><a href="#" class="a13">Video</a></li> 
					<li><a href="#" class="a12">Radio</a></li> 
					<li><a href="#" class="aStore">Store</a></li> 
					<li><a href="#" class="aComment">Comment</a></li> 
					<li><a href="#" class="aContact">Contact</a></li>
				</ul> 
				<div class="clear"></div> 
			</div> 
			
			<div id="jquery_jplayer"></div> 
			
			<div class="top-bg"></div>
			<div id="playlister">
				<div class="playlist-main"></div>
				<div class="playlist-bottom"></div>
			</div>
			
			<div class="jp-audio"> 
				<div class="jp-playlist-player"> 
				
					<div id="jp_interface_2" class="jp-interface"> 
						<ul class="jp-controls"> 
							<li><a href="#" id="jplayer_play" class="jp-play" tabindex="1">play</a></li> 
							<li><a href="#" id="jplayer_pause" class="jp-pause" tabindex="1">pause</a></li> 
							<li><a href="#" id="jplayer_stop" class="jp-stop" tabindex="1">stop</a></li> 
 
							<li><a href="#" id="jplayer_previous" class="jp-previous vtip" tabindex="1">previous<span class="vtip">Play the previous track</span></a></li> 
							<li><a href="#" id="jplayer_next" class="jp-next vtip" tabindex="1">next<span class="vtip">Play the next track</span></a></li> 
						</ul> 
 
						<div id="jplayer_volume_bar" class="jp-volume-bar"> 
							<div id="jplayer_volume_bar_value" class="jp-volume-bar-value"></div> 
						</div> 
 
						<div class="current-track"></div> 
						
						<div id="jplayer_play_time" class="jp-current-time"></div> 
						<div class="slash">/</div> 
						<div id="jplayer_total_time" class="jp-duration"></div>	
						<div class="clear"></div> 
						
						<div class="jp-progress"> 
							<div id="jplayer_load_bar" class="jp-seek-bar jp-load-bar"> 
								<div id="jplayer_play_bar" class="jp-play-bar"></div> 
							</div> 
						</div> 
						
					</div> 
					
					<div id="jplayer_playlist" class="jp-playlist"> 
						<div id="retract"> 
							<ul id="playlist"> 
								<li></li> 
							</ul> 
						</div> 
						<div id="playlistaction"></div>
						<div id="playlisthide"></div>
						<div class="clear"><div>
					</div> 
				</div> 
			</div> 
			
			<div id="volumebg"></div>
			<div id="progressbg"></div>
			
			<div class="space"></div> 
			<div class="aClose"></div> 
			<div class="page"></div> 
			
			<div class="comments"> 
				<h1>Comment</h1> 
				<script src="http://connect.facebook.net/en_US/all.js#appId=109074395847889&amp;xfbml=1"></script> 
				<div id="fb-root"><fb:comments numposts="10" width="570" publish_feed="true"></fb:comments></div> 
			</div> 
						
			<div class="contact"> 
				<h1>Contact</h1> 
				<div class="form"> 
					<div class="notify"></div> 
					<input type="text" value="Name..." name="name" id="name" onfocus="clickclear(this, 'Name...')" onblur="clickrecall(this, 'Name...')" /> 
					<div class="clear"></div> 
					<input type="text" value="Email..." name="email" id="email" onfocus="clickclear(this, 'Email...')" onblur="clickrecall(this, 'Email...')" /> 
					<div class="clear"></div> 
					<input type="text" value="Phone..." name="phone" id="phone" onfocus="clickclear(this, 'Phone...')" onblur="clickrecall(this, 'Phone...')" /> 
					<div class="clear"></div> 
					<textarea name="comments" class="textarea" id="comments" onfocus="clickclear(this, 'Message...')" onblur="clickrecall(this, 'Message...')">Message...</textarea> 
					<div class="clear"></div> 
					<div class="submitform">Submit Form</div> 
					<div class="clear"></div> 		
				</div> 
			</div> 

			<div class="store">
			
				<div class="cartnav"> 
					<div class="showstore">Store Front</div> 
					<div class="showcart">View Cart</div> 
					<div class="clear"></div> 
				</div> 
			
				<h1>Store</h1> 
				
				<div class="clear"></div> 
				<div class="cart"></div> 
				
				<ul class="products"> 
					<li><div class="productimage"><img src="artists/products/497_radagun_we-just-woke-up_free-music.jpg" border="0" /></div><h2>Test</h2><div class="productdetails">Test</div><hr /><div class="price">$10.00</div><div class="addtocart">12</div><div class="clear"></div></li>
					<div class="clear"></div>
				</ul>
				
			</div> 
						
			<div class="socialize"> 
				<div class="socializestream"></div> 
				<div id="getsocial"> 
					<a href="http://www.twitter.com/radagun" class="twitter">Twitter.com/radagun</a> 			 
					<a href="http://www.facebook.com/radagun" class="facebook">Facebook.com/radagun</a> 			 
					<a href="http://www.radagun.com" class="www">www.radagun.com</a> 			
					<div class="clear"></div> 
				</div>
			</div> 
			
			<div class="footerfade"></div> 

			<a class="jp-play-fake"></a>
			<a class="jp-pause-fake"></a>

			
<script type="text/javascript"> 
(function($) {
	$(function() { //on DOM ready
		setTimeout(function(){ 
			$("#playlist").simplyScroll({
				className: 'vert',
				horizontal: false,
				frameRate: 20,
				speed: 5
			});		
		}, 2000);
	});
})(jQuery);
</script> 
</body> 
</html> 