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
	$artist_listens = $row["listens"];
	if ($artist_listens == "1") { $show_listens = "true"; }
	
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
					$(".page").html("<div class=\"box-header\"></div>" + data'.$page_id.' + "<div class=\"box-footer\"></div>");
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
		$music_listens = $music["views"];
		$music_audio = $music["audio"];
		$music_image = $music["image"];
		$music_bgcolor = $music["bgcolor"];
		$music_bgrepeat = $music["bgrepeat"];
		$music_bgposition = $music["bgposition"];
		$music_name = stripslashes($music["name"]);
		$music_name = str_replace('"', '&quot;', $music_name);
		$music_artistid = $music["artistid"];
		$music_amazon = nohtml($music["amazon"]);
		$music_itunes = nohtml($music["itunes"]);
		
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
			$musicList .= '{name:"'.$music_name.'",mp3:"'.trueSiteUrl().'/artists/audio/'.$music_audio.'",download:"'.$music_download.'",image:"'.$music_image.'",bgcolor:"'.$music_bgcolor.'",bgrepeat:"'.$music_bgrepeat.'",bgposition:"'.$music_bgposition.'",plus:"",amazon:"'.$music_amazon.'",itunes:"'.$music_itunes.'"}'; //,plus:"<a href=\'http://www.google.com\' target=\'_blank\' class=\'plus\' onclick=\'javascript: void(0);\'>Test</a>
		}
		++$m;
		$total_listens = $total_listens + $music_listens;
	}
	
	
	
	$loadvideo = mq("select `id`,`name`,`image`,`artistid`,`order` from `[p]musicplayer_video` where {$mQuery} order by `order` asc, `id` desc");
	$cv = 0;
	/* Video Overlay Pagination Code Begins */
	$row_counter = 1; // Counts the number of video pages left to right
	$artist_videos .= '<div class="video-row video-row-1">';
	while ($video = mf($loadvideo)) {
		$video_id = $video["id"];
		$video_image = $video["image"];
		if( !($cv % 3) && ($cv != 0) ) {  ++$row_counter; $artist_videos .= '</div><div class="video-row video-row-' . $row_counter . '">'; } // If it has listed 3 videos, start a new row
		$artist_videos .= '
			<li id="video_'.$cv.'" class="playlist_video_master';
				
				if( !(($cv+1) % 3) ) $artist_videos .= ' last'; // Adds a CSS tag for the last video in the row
				
				$artist_videos .= '">
				<div class="playlist_video"><img src="artists/images/'.$video_image.'" border="0" /></div> // Displays video thumb
			</li>'."\n";
		++$cv;
	}	
	$artist_videos .= '</div>'; // Closes last pagination row
	
	// Build Store
	$check = mf(mq("select * from `[p]musicplayer_ecommerce` where `userid`='{$artist_id}' limit 1"));
	$paypalEmail = $check["paypal"];
	
	// Function that outputs the video overlay pagination buttons, dependant on the global $row_counter variable
	function write_row_buttons() {
		global $row_counter;
		echo "<div class='row-buttons'>";
		for( $i=1; $i <= $row_counter; $i++ ) {
			echo "<div class='row-button row-button-$i'><span>$i</span></div>";
		}
		echo "</div>";
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title><?=siteTitle(); ?><? if (!$fan) { echo " - $artist_name"; } ?></title>
<link href="<?=trueSiteUrl();?>/jplayer/style.css" rel="stylesheet" type="text/css" /> 
<!--<link rel="stylesheet" media="all and (orientation:portrait)" href="<?=trueSiteUrl();?>/jplayer/portrait.css">-->
<link rel="stylesheet" href="<?=trueSiteUrl();?>/jplayer/css/supersized.core.css" type="text/css" media="screen" />
<? // <link rel="stylesheet" media="all and (orientation:landscape)" href="/jplayer/landscape.css"> ?>

<link media="only screen and (max-device-width: 480px)" href="<?=trueSiteUrl();?>/jplayer/iphone.css" type= "text/css" rel="stylesheet" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />

<script language="JavaScript" src="<?=trueSiteUrl();?>/js/jquery-1.6.2.js" type="text/javascript"></script>

<script language="javascript" SRC="<?=trueSiteUrl();?>/jplayer/js/ra_controls.js" type="text/javascript"></script>
<script language="javascript" SRC="<?=trueSiteUrl();?>/jplayer/js/index.js" type="text/javascript"></script>
	
<script language="JavaScript" src="<?=trueSiteUrl();?>/js/swfobject.js"  type="text/javascript"></script>
<script language="JavaScript" src="<?=trueSiteUrl();?>/js/application.php?id=<?php echo $artist_id;?>"  type="text/javascript"></script>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script type="text/javascript" src="<?=trueSiteUrl();?>/jplayer/js/supersized.3.1.3.core.min.js"></script>
		
<script type="text/javascript" src="jplayer/js/jquery.simplyscroll-1.0.4.js"></script>

<script type="text/javascript" src="<?=trueSiteUrl();?>/jplayer/jquery.jplayer.min.js"></script> 
<script type="text/javascript" src="<?=trueSiteUrl();?>/jplayer/demos.common.js"></script> 
	<script type="text/javascript">

		$(document).ready(function(){
			
			$("div.playlist_video").click(function(event){
				$(".close_button").fadeIn(300);
				$("#player_bg").fadeIn(300);
				$(".player_holder").fadeIn(300);
			});
			
			// Pauses the audio player when a user opens a video				
			$("div.playlist_video img").click(function(event){
				$("#jquery_jplayer").jPlayer("pause");
			});
			
			$(".close_button").click(function(event){
				$(this).fadeOut(300);
				$("#player_bg").fadeOut(300);
				$(".player_holder").fadeOut(300);
			});			

			$('#image').hide();
			$('.page').hide();
			$('.comments').hide();
			$('.contact').hide();
			$('.aClose').hide();
			$('.store').hide();
			$('.checkout').hide();
			$('.videos').hide();
			
			/* Close */
			$('.aClose').click(function() {
				$('.aClose').fadeOut();
				$('.comments').fadeOut();
				$('.contact').fadeOut();
				$('.store').fadeOut();
				$('.page').fadeOut();
				$('.videos').fadeOut();
			});
			
			<?=$pagesJava;?>
			
			/* Comment */
			$('.aComment').click(function() {
				$('.videos').fadeOut();
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
				$('.videos').fadeOut();
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
				$('.videos').fadeOut();
				$('.page').fadeOut();
				$('.comments').fadeOut();
				$('.contact').fadeOut();
				setTimeout(function(){ 
					$('.store').fadeIn();
					$('.aClose').fadeIn();
				}, 450);
			});
			
			/* Videos */
			$('.aVideos').click(function() {
				$('.store').fadeOut();
				$('.page').fadeOut();
				$('.comments').fadeOut();
				$('.contact').fadeOut();
				setTimeout(function(){ 
					$('.videos').fadeIn();
					$('.aClose').fadeIn();
				}, 450);
			});			
			
			
			/* Playlist Controller */
			$("#playlistaction").click(function(){
				$(this).parent("pauseOthers");
				$(this).parent(".jp-playlist").animate({"left": "0px"}, "fast");
				$(this).hide();
				$(this).parent(".jp-playlist").children("#playlisthide").show();
			});
			$("#playlisthide").click(function(){
				$(this).parent(".jp-playlist").animate({"left": "-233px"}, "fast");
				$(this).hide();
				$(this).parent(".jp-playlist").children("#playlistaction").show();
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
			
			// All new socialize tab functionality
			var socialize_minimized = true;
			var socialize_tab = '';
			$(".socialize .title").click(function() {
				if( socialize_minimized )
					open_socialize();
				else
					close_socialize();
			});
			
			$(".socialize .facebook").click(function() {
				$(".socialize .body .tab").hide();
				$(".socialize .body #facebook").show();
				
				if( socialize_tab == 'facebook' ) {
					if( socialize_minimized )
						open_socialize();
					else
						close_socialize();
				}
				else {
					open_socialize();
					socialize_tab = 'facebook';
				}
				
			});

			$(".socialize .twitter").click(function() {
				$(".socialize .body .tab").hide();
				$(".socialize .body #twitter").show();
				
				if( socialize_tab == 'twitter' ) {
					if( socialize_minimized )
						open_socialize();
					else
						close_socialize();
				}
				else {
					open_socialize();
					socialize_tab = 'twitter';
				}
				
			});
			
			$(".socialize .email").click(function() {
				$(".socialize .body .tab").hide();
				$(".socialize .body #email").show();
				
				if( socialize_tab == 'email' ) {
					if( socialize_minimized )
						open_socialize();
					else
						close_socialize();
				}
				else {
					open_socialize();
					socialize_tab = 'email';
				}
				
			});
			
			function open_socialize() {
				if( socialize_minimized ) {
					$(".socialize").animate({ bottom: "0" }, 300);
					socialize_minimized = false;
				}
			}
			
			function close_socialize() {
				if( !socialize_minimized ) {
					$(".socialize").animate({ bottom: "-361px" }, 300);
					socialize_minimized = true;
				}
			}
			
			// Old socialize code, no longer used
			/*$(".socializestream").click(function(event){
				$(this).hide();
				$(".socializehide").show();
				<? if ($artist_facebook != "") { ?>
					$("#facebook").show();
				<? } else if ($artist_twitter != "") { ?>
					$("#twitter").show();
				<? } else { ?>
					$("#emaillist").show();
				<? } ?>
				$(".socialize").animate({
					marginTop: "-400px"
				}, 300);
			});
			
			$(".socializehide").click(function(event){
				$(this).hide();
				$(".socializestream").show();			
				$(".socialize").animate({
					marginTop: "-39px"
				}, 300);
			});

			$(".showfacebook").click(function(event){
				$(this).parent(".socialize").children(".padthis").children("#emaillist").hide();
				$(this).parent(".socialize").children(".padthis").children("#googleplus").hide();
				$(this).parent(".socialize").children(".padthis").children("#facebook").fadeIn();
				$(this).parent(".socialize").children(".padthis").children("#twitter").hide();
				return false;
			});	
			
			$(".showtwitter").click(function(event) {
				$(this).parent(".socialize").children(".padthis").children("#emaillist").hide();
				$(this).parent(".socialize").children(".padthis").children("#googleplus").hide();
				$(this).parent(".socialize").children(".padthis").children("#facebook").hide();
				$(this).parent(".socialize").children(".padthis").children("#twitter").fadeIn();
				return false;
			});
			
			$(".showemail").click(function(event){
				$(this).parent(".socialize").children(".padthis").children("#emaillist").fadeIn();
				$(this).parent(".socialize").children(".padthis").children("#googleplus").hide();
				$(this).parent(".socialize").children(".padthis").children("#facebook").hide();
				$(this).parent(".socialize").children(".padthis").children("#twitter").hide();
				return false;
			});	
			
			$(".showgoogle").click(function(event){
				$(this).parent(".socialize").children(".padthis").children("#emaillist").hide();
				$(this).parent(".socialize").children(".padthis").children("#googleplus").fadeIn();
				$(this).parent(".socialize").children(".padthis").children("#facebook").hide();
				$(this).parent(".socialize").children(".padthis").children("#twitter").hide();
				return false;
			});	*/
			
			$(".bottom").click(function(event){
				var logoclass = $(this).parent("#logo").attr("class");
				if (logoclass == "openlogo") {
					$(this).parent("#logo").animate({
						marginTop: "-175px"
					}, 300);
				} else {
					$(this).parent("#logo").animate({
						marginTop: "0px"
					}, 300);
				}
				$(this).parent("#logo").toggleClass("openlogo");
			});	
			
			
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
			
			$(".submitNewsletter").click(function(event){
			
				var loader = "Message pending...";
				$("#successMessage").slideToggle(100);
				$("#successMessage").html(loader);
				
				// Grab the current data
				var eartist = "<?=$artist_id;?>";
				var ename = document.getElementById('emailName').value;
				var eemail = document.getElementById('emailEmail').value;
				var submited = "&newsletter=true&artist=" + eartist + "&name=" + ename + "&email=" + eemail;
				
				//submit the data for processing
				$.post("jplayer/ajax.php", submited, function(repo) {
					$("#successMessage").html(repo);
					setTimeout('$("#successMessage").slideToggle(100)', 3000);	
					document.getElementById('emailName').value = "";
					document.getElementById('emailEmail').value = "";
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
	</script>	
<!--<script type="text/javascript">
$(id).bind($.jPlayer.event.play, function() { // Bind an event handler to the instance's play event.
  $(this).jPlayer("pauseOthers"); // pause all players except this one.
});
</script>-->

<body>

<script type="text/javascript"> 

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
	
	// Calculate the max number of video overlay rows
	var max_rows = 0;
	$(".videos .row-button").each( function() {
		++max_rows;
	});
	
	// JS code for the pagination buttons
	$(".videos .row-button").click( function() {
		var row_num = $(this).children('span').html()
		shows_video_row(row_num);
	});
	
	// JS code for the arrows of pagination
	$(".videos .nav-arrows div").click( function() {
		var new_page = $(this).children('span').html();
		if( new_page ) {
			shows_video_row(new_page);
		}
	});
	
	// Function that switches video overlay pages
	function shows_video_row(page) {
		$(".videos .row-button").css("background-position", "center top");
		$(".videos .row-button-" + page).css("background-position", "center bottom");
		$(".videos .video-row").hide();
		$(".videos .video-row-" + page).show();
		$(".videos .nav-arrows div").addClass('active');
		$(".videos .left-arrow").children('span').html( page*1-1 );
		$(".videos .right-arrow").children('span').html( page*1+1 );
		if( page == 1 ) {
			$(".videos .left-arrow").removeClass('active');
			$(".videos .left-arrow").children('span').html('');
		}
		if( page == max_rows ) {
			$(".videos .right-arrow").removeClass('active');
			$(".videos .right-arrow").children('span').html('');
		}
	}
 
	function displayPlayList() {
		$("#jplayer_playlist ul").empty();
		for (i=0; i < myPlayList.length; i++) {
			var listItem = (i == myPlayList.length-1) ? "<li class='jplayer_playlist_item_last'>" : "<li>";
			listItem += myPlayList[i].plus + "<a href='#' id='jplayer_playlist_item_" + i + "' tabindex='1'><span class='thisisthetrackname'>" + myPlayList[i].name + "</span><span class='songimage' style='display: none;'>" + myPlayList[i].image + "</span><span class='sellitunes' style='display: none;'>" + myPlayList[i].itunes + "</span><span class='sellamazon' style='display: none;'>" + myPlayList[i].amazon + "</span><div class='songbgcolor' style='display: none;'>" + myPlayList[i].bgcolor + "</div><div class='songbgposition' style='display: none;'>" + myPlayList[i].bgposition + "</div><div class='songbgrepeat' style='display: none;'>" + myPlayList[i].bgrepeat + "</div></a>" + myPlayList[i].download;
			listItem += "<div class='clear'></div>";
			listItem += "<div class='metadata'>This is a test</div>";
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
		
		
			$('span.showamazon').hide();
			$('span.showitunes').hide();
			
			// Display Image			
			$('#loader').show();
			$('#image').hide();
			
			// Get Current Image
			var sellamazon = $("#jplayer_playlist_item_"+index).children("span.sellamazon").text();
			var sellitunes = $("#jplayer_playlist_item_"+index).children("span.sellitunes").text();
			var trackname = $("#jplayer_playlist_item_"+index).children("span.thisisthetrackname").text();
			var image = $("#jplayer_playlist_item_"+index).children("span.songimage").text();
			var color = $("#jplayer_playlist_item_"+index).children("div.songbgcolor").text();
			var position = $("#jplayer_playlist_item_"+index).children("div.songbgposition").text();
			var repeat = $("#jplayer_playlist_item_"+index).children("div.songbgrepeat").text();

			// OLD SUPER-SIZER CODE, CURRENTLY NOT USED

			// Super-sizer Background image
			/*jQuery(function($){
				$.supersized({
					//Background image
					slides	:  [ { image : '<?=trueSiteUrl();?>/artists/images/'+image } ]					
				});
			});*/
			
			//if (repeat == "stretch") {
				//$('#image').css({
				//	backgroundImage: "url('<?=trueSiteUrl();?>/artists/images/')", backgroundRepeat: repeat, backgroundPosition: position
				//});
				$('#image').html("<img src='<?=trueSiteUrl();?>/artists/images/thumbs.php?src=../artists/images/" + image + "&w=" + getWindowWidth() + "&h="+ getWindowHeight() + "&zc=0&q=100' style='vertical-align:middle; margin-top:-" + (getWindowHeight()/2) + "px; margin-left:-" + (getWindowWidth()/2) + "px;' />");
				//$('#image').html("<img src='<?=trueSiteUrl();?>/artists/images/" + image + "' style='vertical-align:middle;' width='100%' />");
			//} else {
			//	$('#image').html("");	
				//$('#image').css({
				//	backgroundImage: "url('<?=trueSiteUrl();?>/artists/images/thumbs.php?src=../artists/images/" + image + "&w=220&h=248&zc=1&q=100')", backgroundRepeat: repeat, backgroundPosition: position
				//});
			//}
			
			//var bg_image = $("#jplayer_playlist_item_"+playItem).children("span.songimage").text();
			/*jQuery(function($){
				$.supersized({
					//Background image
					slides	:  [ { image : '<?=trueSiteUrl();?>/artists/images/'+image+'' } ]							
				});
			});*/
			
			$('#image').css("background-color", "#"+color);
			$('#image').fadeIn();
			$('span.trackname').text(trackname);
			
			if (sellamazon == "" && sellitunes == "") {
				$('div.mighthide').fadeOut();
			} else {
				$('div.mighthide').fadeIn();
			}
			
			if (sellamazon != "") {
				$('span.showamazon').html("<a href='" + sellamazon + "' class='buynow amazon' target='_blank'></a>");
				$('span.showamazon').show();
			}
			
			if (sellitunes != "") {
				$('span.showitunes').html("<a href='" + sellitunes + "' class='buynow itunes' target='_blank'></a>");
				$('span.showitunes').show();
			}
			
			// Display Current Track Title
			var track = "&track="+image;
			$.post('jplayer/ajax.php', track, function(data) {
					$('.current-track').html(data);
			});
			
			setTimeout(function(){ 
				$('#loader').hide();
			}, 1500);
			
	}

	// Function that gets window width
	function getWindowWidth() {
		screenMinWidth = 1024; // Minimum screen width
		var windowWidth = 0;
		if (typeof(window.innerWidth) == 'number') {
			windowWidth = window.innerWidth;
		}
		else {
			if (document.documentElement && document.documentElement.clientWidth) {
				windowWidth = document.documentElement.clientWidth;
			}
			else {
				if (document.body && document.body.clientWidth) {
					windowWidth = document.body.clientWidth;
				}
			}
		}
		if( windowWidth < screenMinWidth ) windowWidth = screenMinWidth;
		return windowWidth;
	}

	// Function that gets window height
	function getWindowHeight() {
		screenMinHeight =  768; // Minimum screen height
		var windowHeight = 0;
		if (typeof(window.innerHeight) == 'number') {
			windowHeight = window.innerHeight;
		}
		else {
			if (document.documentElement && document.documentElement.clientHeight) {
				windowHeight = document.documentElement.clientHeight;
			}
			else {
				if (document.body && document.body.clientHeight) {
					windowHeight = document.body.clientHeight;
				}
			}
		}
		if( windowHeight < screenMinHeight ) windowHeight = screenMinHeight;
		return windowHeight;
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
	
});
	
-->
</script> 

			<div id="results"></div>

			
			<? if (!$fan) { ?>
			
			<!-- Old socialize HTML, not used anymore
			
						<div class="socialize"> 
				<div class="socializestream"></div> 
				<div class="socializehide"></div>
				
				<a href="#" class="showfacebook">Facebook</a>
				<a href="#" class="showtwitter">Twitter</a>
				<a href="#" class="showemail">Email List</a>
				<a href="#" class="showgoogle">Show Google+ Stream</a>
				<div class="clear"></div>
				<div class="padthis">
				<div id="emaillist" class="hide">
					<div id="successMessage"></div>
					<p class="pad">Join our email list. We will never spam and never sell your personal information to anybody.</p>
					<label>Name</label>
					<input type="text" name="name" id="emailName" class="input" />
					<div class="clear"></div>
					<label>E-Mail</label>
					<input type="text" name="email" id="emailEmail" class="input" />
					<div class="clear"></div>
					<div class="clear"></div>
					<label>&nbsp;</label>
					<a href="#" class="submitNewsletter">Submit</a>
					<div class="clear"></div>
				</div>
				<div id="googleplus" class="hide">
					<p>This is the Google+ Stream</p>
				</div>
				<? if ($artist_facebook != "") { ?>
				<div id="facebook" class="hide">
					<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2F<?=$artist_facebook;?>&amp;width=292&amp;colorscheme=dark&amp;show_faces=false&amp;stream=true&amp;header=false&amp;height=415" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:395px;" allowTransparency="true"></iframe>
				</div>
				<? } ?>
				<? if ($artist_twitter != "") { ?>
				<div id="twitter" class="hide">
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
				</div>
			</div> 
			
			-->
			
			<!-- New socialize HTML -->
			<div class="socialize">
				<div class="header">
					<div class="title"></div>
					<div class="buttons">
						<div class="facebook"></div>
						<div class="twitter"></div>
						<div class="email"></div>
					</div>
				</div>
				
				<div class="body">
					<div id="email" class="tab">
						<div id="successMessage"></div>
						<p class="pad">Join our email list. We will never spam and never sell your personal information to anybody.</p>
						<label>Name</label>
						<input type="text" name="name" id="emailName" class="input" />
						<br /><br /><br />
						<label>E-Mail</label>
						<input type="text" name="email" id="emailEmail" class="input" />
						<br /><br /><br /><br />
						<a href="#" class="submitNewsletter">Submit</a>
					</div>
					
					<div id="facebook" class="tab">
						<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2F<?=$artist_facebook;?>&amp;width=220&amp;colorscheme=dark&amp;show_faces=false&amp;stream=true&amp;header=false&amp;height=415" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:220px; height:395px;" allowTransparency="true"></iframe>
					</div>
					
					<div id="twitter" class="tab">
						<script src="http://widgets.twimg.com/j/2/widget.js"></script>
						<script>
						new TWTR.Widget({
						  version: 2,
						  type: 'profile',
						  rpp: 5,
						  interval: 6000,
						  width: 220,
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
				
				</div>
				
			</div> 
	
			<? } ?>
			
			<div id="image"></div>
			<div id="loader"><img src="<?=trueSiteUrl();?>/jplayer/images/ajax-loader.gif" /></div>
			
			<? if (!$fan) { ?>
			<div id="logo">
				<div id="makeroomforlogo">
				<? if ($artist_logo) { ?><img src="<?=trueSiteUrl();?>/artists/images/thumbs.php?src=<?=$artist_logo;?>&q=100&h=145&w=145" /><? } ?>
				</div>
				<div id="makeroomfordetails">
					<div class="clear"></div>
					
					<? if ($show_listens == "true") { ?>
					<p>Total Listens: <?=$total_listens;?></p>
					<? } ?>
					<p>Artist Name: <?=$artist_name;?></p>
					<p>Track Name: <span class="trackname"></span></p>				
					
					<div class="mighthide">
						<div class="buynow"></div>
						<!-- <a href="#" class="buynow mystore"></a> -->
						<span class="showamazon"></span>
						<span class="showitunes"></span>
					</div>
					
					<div class="clear"></div>
				</div>
				<div class="bottom"></div>
			</div>
			<? } ?>
			
			
			<div id="navigation">
				<ul>
				<? if (!$fan) { ?>
					<? if ($artist_videos) { ?>
					<li><a href="#" class="aVideos">Videos</a></li>
					<? } ?>				
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
				<div class="box-header"></div>
				<h1>Comment</h1>
				<script src="http://connect.facebook.net/en_US/all.js#appId=<?=$artist_appid;?>&amp;xfbml=1"></script>
				<div id="fb-root"><fb:comments numposts="10" width="570" publish_feed="true"></fb:comments></div>
				<div class="box-footer"></div>
			</div>
			<? } ?>
			
			<? if ($artist_email) { ?>
			<div class="contact">
				<div class="box-header"></div>
				
				
				<div class="right">
					<h1><span class="slashes">//</span> Management</h1>
					<h2>Blue Vinyl Records</h2>
					<ul>
						<li><span>Attn:</span> Eddie Fingers McNilly</li>
						<li><span>ph:</span> 900-900-9000</li>
						<li><span>fx:</span> 900-899-8000</li>
						<li><span>email:</span> efmn@bluevinylrec.com</li>
					</ul>
					
					<br />
					
					<h1><span class="slashes">//</span> Booking</h1>
					<h2>Blue Steel Looks Booking</h2>
					<ul>
						<li><span>Attn:</span> Ms. Victoria Ballard</li>
						<li><span>ph:</span> 900-900-9000</li>
						<li><span>fx:</span> 900-899-8000</li>
						<li><span>email:</span> vic@bslooks.com</li>
					</ul>
					
				</div>
				
				<div class="left">
					<h1>CONTACT <span class="slashes">//</span> <?=$artist_name;?></h1>
					
					<table>
						<tr>
							<td><span class="red">*</span> Name:</td>
							<td><input type="text" value="Name..." name="name" id="name" onfocus="clickclear(this, 'Name...')" onblur="clickrecall(this, 'Name...')" /></td>
						</tr>
						<tr>
							<td><span class="red">*</span> E-Mail:</td>
							<td><input type="text" value="Email..." name="email" id="email" onfocus="clickclear(this, 'Email...')" onblur="clickrecall(this, 'Email...')" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							<select name="">
								<option value="Milk" selected>Option 1</option>
								<option value="Cheese">Option 2</option>
							</select>
							</td>
						</tr>
						<tr>
							<td class="message"><span class="red">*</span> Message:</td>
							<td><textarea name="comments" class="textarea" id="comments" onfocus="clickclear(this, 'Message...')" onblur="clickrecall(this, 'Message...')"></textarea></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><br />*CAPTCHA Goes Here*<br /><br /></td>
						</tr>
						<tr>
							<td><span class="red">*</span> required</td>
							<td><div class="clearform">clear form X</div><div class="submitform">submit</div></td>
						</tr>
					</table>
			
				</div>

				


				<div class="box-footer"></div>
			</div>
			<? } ?>
			
			
			<? if ($artist_videos) { ?>
			<div class="videos">
				<!--<div class="box-header"></div>-->
				<h1><? write_row_buttons(); ?>Videos</h1>
				<div id="app_cntrl" class="application_control">
					<div id="vid_lib" class="video_library">
						<!-- -->
						<div id="playlist_0_hldr" class="playlist_row_master">
							<ul id="playlist_0" class="playlist_row">
								<div class="nav-arrows">
								<div class="left-arrow"><span></span></div>
								<div class="active right-arrow"><span>2</span></div>
								</div>
								<?=$artist_videos;?>
								<div class="clear"></div>
							</ul>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<!--<div class="box-footer"></div>-->
			</div>
			<? } ?>

			<!-- VIDEO PLAYER -->
			<div id="close_btn" class="close_button">&raquo; CLOSE VIDEO PLAYER &laquo;</div>
			<div id="player_hldr" class="player_holder"></div> 
			<div id="player_bg"></div> 
			<!-- /VIDEO PLAYER -->
			
			
			<? if ($paypalEmail != "") { ?>
			<div class="store">
				<div class="box-header"></div>
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
				<div class="box-footer"></div>
			</div>
			<? } ?>
			
		
			
			<div class="footerfade">
				<div class="logo_img"><a href="<?=trueSiteUrl();?>/artists.php" /></a></div>
				<div id="theSearchBox"></div>
			</div> 

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
				speed: 20
			});		
		}, 500);
	});
})(jQuery);
</script>

<!-- Tracking code Starts --> 
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://www.carlosmariomejia.com/webstats/" : "http://www.carlosmariomejia.com/webstats/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 6);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://www.carlosmariomejia.com/webstats/piwik.php?idsite=6" style="border:0" alt="" /></p></noscript>
<!-- Tracking code Ends -->
</body>
</html>
<? } ?>