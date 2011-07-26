<?

session_start();
//error_reporting(0);
if (!$_SESSION["cart"]) {
$_SESSION["cart"] = rand(1111111,9999999);
}

$browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
if ($browser == true || $_GET["debug"] == "true"){

	$browser = 'iphone';
	include("iphone.php");

} else if ($_GET["embed"] == "true") {

	include("iphone.php");
	
} else {

	
	if ($_GET["url"] != "") {
		$artist_url = $_GET["url"];
	} else {
		$loadUsername = explode(".", $_SERVER["HTTP_HOST"]);
		$artist_url = $loadUsername[0];	
	}
	
	$row = mf(mq("select * from `[p]musicplayer` where `url`='{$artist_url}' limit 1"));
	if ($row["type"] == "1") {
		// fan music player
		$fan = "true";		
	}
	$artist_id = $row["id"];
	$artist_name = stripslashes($row["artist"]);
	$artist_email = $row["email"];
	$artist_logo = $row["logo"];
	$artist_website = $row["website"];
	$artist_twitter = $row["twitter"];
	$artist_facebook = $row["facebook"];
	$artist_appid = $row["appid"];
	
	playerViews($artist_id);
	
	// Build Pages
	$loadpages = mq("select * from `[p]musicplayer_content` where `artistid`='{$artist_id}' order by `order` asc, `id` desc");
	while ($pages = mf($loadpages)) {
		$page_id = $pages["id"];
		$page_name = stripslashes($pages["name"]);
		if ($pages["body"] != "") { 
			$page_body = '<p>'.nohtml($pages["body"]).'</p>';
		} else { 
			$page_body = '';
		}
		if ($pages["video"] != "") {
			$page_video = '<div class="video">'.$pages["video"].'</div>';
		} else { 
			$page_video = '';
		}
		if ($pages["image"] != "") { 
			$page_image = '<img class="image" src="'.trueSiteUrl().'/artists/images/'.$pages["image"].'" border="0" />';
		} else { 
			$page_image = '';
		}
		$pagesList .= '<li><a href="#" class="a'.$page_id.'">'.$page_name.'</a></li>'."\n";
		$pagesJava .= '
			/* '.$page_name.' */
			$(".a'.$page_id.'").click(function() {
				$(".comments").fadeOut();
				$(".contact").fadeOut();
				$(".store").fadeOut();
				$(".page").hide();
				$(".page").html("<div class=\'pageload\'><img src=\''.trueSiteUrl().'/jplayer/images/page-loader.gif\' border=\'0\' /></div>").fadeIn();
				$(".aClose").fadeIn();
				var body'.$page_id.' = "&artist='.$artist_id.'&page='.$page_id.'";
				$.post("jplayer/ajax.php", body'.$page_id.', function(data'.$page_id.') {
					$(".page").html(data'.$page_id.');
				});
			});
		'."\n";
	}
	
	if ($fan) {
		$mQuery = "`user`='{$artist_id}'";
		$downQ = "&user={$artist_id}";
	} else {
		$mQuery = "`artistid`='{$artist_id}' and `type`='0'";	
	}
	// Build Music
	$loadmusic = mq("select * from `[p]musicplayer_audio` where {$mQuery} order by `order` asc, `id` desc");
	$m=0;
	while ($music = mf($loadmusic)) {
		$music_id = $music["id"];
		$music_audio = $music["audio"];
		$music_image = $music["image"];
		$music_bgcolor = $music["bgcolor"];
		$music_bgrepeat = $music["bgrepeat"];
		$music_bgposition = $music["bgposition"];
		$music_name = stripslashes($music["name"]);
		$music_name = str_replace('"', '&quot;', $music_name);
		$music_artistid = $music["artistid"];
		
		if ($music["download"] == "1") { 
			$music_download = '<a href=\'download.php?artist='.$music_artistid.'&id='.$music_id.$downQ.'\' title=\'Click here download '.$music_name.' for free\' class=\'download vtip\'>Download</a> '; 
		} else { 
			$music_download = ''; 
		}
		if ($m != "0") { $musicList .= ","; }
		if ($fan) {
			$music_artistid = $music["artistid"];
			$art = mf(mq("select `artist` from `[p]musicplayer` where id='{$music_artistid}' limit 1"));
			$music_artist = nohtml($art["artist"]);
			$musicList .= '{name:"<small>'.$music_artist.' - '.$music_name.'</small>",mp3:"'.trueSiteUrl().'/artists/audio/'.$music_audio.'",download:"'.$music_download.'",image:"'.$music_image.'",bgcolor:"'.$music_bgcolor.'",bgrepeat:"'.$music_bgrepeat.'",bgposition:"'.$music_bgposition.'"}';
		} else {
			$musicList .= '{name:"'.$music_name.'",mp3:"'.trueSiteUrl().'/artists/audio/'.$music_audio.'",download:"'.$music_download.'",image:"'.$music_image.'",bgcolor:"'.$music_bgcolor.'",bgrepeat:"'.$music_bgrepeat.'",bgposition:"'.$music_bgposition.'"}';
		}
		++$m;
	}
	
	// Build Store
	$check = mf(mq("select * from `[p]musicplayer_ecommerce` where `userid`='{$artist_id}' limit 1"));
	$paypalEmail = $check["paypal"];
	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>MyArtistDNA.fm<? if (!$fan) { echo " - $artist_name"; } ?></title>
<link href="<?=trueSiteUrl();?>/jplayer/style.css" rel="stylesheet" type="text/css" /> 

<link media="only screen and (max-device-width: 480px)" href="<?=trueSiteUrl();?>/jplayer/iphone.css" type= "text/css" rel="stylesheet" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script> 
<script type="text/javascript" src="jplayer/js/jquery.simplyscroll-1.0.4.js"></script>

<script type="text/javascript" src="<?=trueSiteUrl();?>/jplayer/jquery.jplayer.min.js"></script> 
<script type="text/javascript" src="<?=trueSiteUrl();?>/jplayer/demos.common.js"></script> 
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
			
			<?=$pagesJava;?>
			
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
				var cart = "&paypal=<?=$paypalEmail;?>&cart=true&artist=<?=$artist_id;?>&product="+pro;
				$.post("jplayer/ajax.php", cart, function(items) {
					$("ul.products").hide();
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
				$(this).animate({
					left: "0px"
				}, 250);				
			});
			
			$("a.jp-previous").mouseout(function(event){
				$(this).animate({
					left: "-169px"
				}, 250);
			});	
			
			$("a.jp-next").mouseover(function(event){
				$(this).animate({
					right: "0px"
				}, 250);	
			});
			
			$("a.jp-next").mouseout(function(event){
				$(this).animate({
					right: "-138px"
				}, 250);
			});
			
			$(".socializestream").click(function(event){
				$(this).hide();
				$(".socializehide").show();
				$(".socialize").animate({
					marginTop: "-220px"
				}, 500);
			});
			
			$(".socializehide").click(function(event){
				$(this).hide();
				$(".socializestream").show();			
				$(".socialize").animate({
					marginTop: "-39px"
				}, 500);
			});			
			
		});
	</script>	

<body>

<script type="text/javascript"> 
<!--
$(document).ready(function(){
 
	var playItem = 0;
 
	var myPlayList = [
		<?=$musicList;?>
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
			listItem += "<a href='#' class='plus'></a><a href='#' id='jplayer_playlist_item_" + i + "' tabindex='1'>" + myPlayList[i].name + "<span class='songimage' style='display: none;'>" + myPlayList[i].image + "</span><div class='songbgcolor' style='display: none;'>" + myPlayList[i].bgcolor + "</div><div class='songbgposition' style='display: none;'>" + myPlayList[i].bgposition + "</div><div class='songbgrepeat' style='display: none;'>" + myPlayList[i].bgrepeat + "</div></a>" + myPlayList[i].download;
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
			
			if (repeat == "stretch") {
				$('#image').html("<img src='<?=trueSiteUrl();?>/artists/images/"+image+"' width='100%' style='vertical-align:middle;' />");	
			} else {
				$('#image').html("");	
				$('#image').css({
					backgroundImage: "url('<?=trueSiteUrl();?>/artists/images/" + image + "')", backgroundRepeat: repeat, backgroundPosition: position
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
		var from = "<?=$artist_email;?>";
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

			
			<? if (!$fan) { ?>
			
			<div id="getsocial">
			<? if ($artist_twitter != "") { ?> <a href="http://www.twitter.com/<?=$artist_twitter;?>" class="twitter">Twitter.com/<?=$artist_twitter;?></a> <? } ?>
			<? if ($artist_facebook != "") { ?> <a href="http://www.facebook.com/<?=$artist_facebook;?>" class="facebook">Facebook.com/<?=$artist_facebook;?></a> <? } ?>
			<? if ($artist_website != "") { ?> <a href="http://<?=$artist_website;?>" class="www"><?=$artist_website;?></a> <? } ?>
			<div class="clear"></div>
			</div>
			

			<? if ($artist_facebook != "") { ?>
			
			<div class="facebookstream"> 
				<div class="facebookstreamhide"></div> 
				<div class="facebookstreamshow"></div>
				<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2F<?=$artist_facebook;?>&amp;width=292&amp;colorscheme=dark&amp;show_faces=false&amp;stream=true&amp;header=false&amp;height=415" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:395px;" allowTransparency="true"></iframe>
			</div>
			
			<? } ?>
			
			
			<? if ($artist_twitter != "") { ?>
			
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
				}).render().setUser('<?=$artist_twitter;?>').start();
				</script>
			</div>
			
			<? } ?>
			
			<? } ?>
			
			<div id="image"></div>
			<div id="loader"><img src="<?=trueSiteUrl();?>/jplayer/images/ajax-loader.gif" /></div>
			
			<? if (!$fan) { ?>
			<div id="logo">
				<? if ($artist_logo) { ?><img src="<?=trueSiteUrl();?>/artists/images/<?=$artist_logo;?>" /><? } else { echo $artist_name; } ?>
			</div>
			<? } ?>
			
			
			<div id="navigation">
				<ul>
				<? if (!$fan) { ?>
					<?=$pagesList;?>
					<? if ($paypalEmail != "") { ?>
					<li><a href="#" class="aStore">Store</a></li>
					<? } ?>
					<? if ($artist_appid) { ?>
					<li><a href="#" class="aComment">Comment</a></li>
					<? } ?>
					<? if ($artist_email) { ?>
					<li><a href="#" class="aContact">Contact</a></li>
					<? } ?>
				<? } else { ?>
					<li><a href="#" style="width: 162px;">&nbsp;</a></li>
				<? } ?>
					<li>
					<!-- AddThis Button BEGIN --
					<div class="addthis_toolbox addthis_default_style ">
					<a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4db04df93b438b17" class="addthis_button_compact">Share</a>
					</div>
					<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4db04df93b438b17"></script>
					!-- AddThis Button END -->
					</li>
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

							<li><a href="#" id="jplayer_previous" class="jp-previous vtip" tabindex="1">previous</a></li>
							<li><a href="#" id="jplayer_next" class="jp-next vtip" tabindex="1">next</a></li>
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
			
			<? if ($artist_appid) { ?>
			<div class="comments">
				<h1>Comment</h1>
				<script src="http://connect.facebook.net/en_US/all.js#appId=<?=$artist_appid;?>&amp;xfbml=1"></script>
				<div id="fb-root"><fb:comments numposts="10" width="570" publish_feed="true"></fb:comments></div>
			</div>
			<? } ?>
			
			<? if ($artist_email) { ?>
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
			<? } ?>
			
			
			<? if ($paypalEmail != "") { ?>
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
				<?
				// Build Products List
					$loadpro = mq("select * from `[p]musicplayer_ecommerce_products` where `artistid`='{$artist_id}' order by `order` asc, `id` desc");
					while ($pro = mf($loadpro)) {
						$product_color = "";
						$product_size = "";
						$product_id = $pro["id"];
						$product_image = $pro["image"];
						if ($product_image != "") {
							$product_image = '<img src="artists/products/'.$product_image.'" border="0" />';
						}
						$product_name = stripslashes($pro["name"]);
						$product_desc = nohtml($pro["description"]);
						$product_price = $pro["price"];
						if ($pro["size"] != "") {
							$sizeEx = explode(",", $pro["size"]);
							$product_size .= "<select class='option' name='size'><option value=''> -- Select -- </option>";
							foreach ($sizeEx as $size) {
								$product_size .= "<option value='{$size}'>{$size}</option>";
							}
							$product_size .= "</select>";
						}
						if ($pro["color"] != "") {
							$colorEx = explode(",", $pro["color"]);
							$product_color .= "<select class='option' name='color'><option value=''> -- Select -- </option>";
							foreach ($colorEx as $color) {
								$product_color .= "<option value='{$color}'>{$color}</option>";
							}
							$product_color .= "</select>";
						}
						
						$productsList .= '<li><div class="productimage">'.$product_image.'</div><h2>'.$product_name.'</h2><div class="productdetails">'.$product_desc.''.$product_size.$product_color.'</div><hr /><div class="price">$'.$product_price.'</div><div class="addtocart">'.$product_id.'</div><div class="clear"></div></li>'."\n";
					}
					echo $productsList
				?>
				<div class="clear"></div>
				
				
				
				</ul>
			</div>
			<? } ?>
			
			<div class="socialize"> 
				<div class="socializestream"></div> 
				<div class="socializehide"></div> 
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
			
			
			
<? if ($m >= 1) {?>
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
<? } ?>
</body>
</html>
<? } ?>