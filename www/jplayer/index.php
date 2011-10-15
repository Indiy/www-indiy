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
				$(".videos").fadeOut();
				$(".page").hide();
				$(".page").html("<div class=\'pageload\'><img src=\''.trueSiteUrl().'/jplayer/images/page-loader.gif\' border=\'0\' /></div>");
				
				setTimeout(function(){ $(".page").fadeIn(); $(".aClose").fadeIn(); }, 450);
				
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
        if( !isset($first_track_listens))
            $first_track_listens = $music['views'];
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
	}
	
	$total_q = mf(mq("SELECT SUM(views) FROM `[p]musicplayer_audio` WHERE `artistid`='{$music_artistid}'"));
	$total_listens = intval($total_q[0]);
    
	$loadvideo = mq("select `id`,`name`,`image`,`artistid`,`order` from `[p]musicplayer_video` where {$mQuery} order by `order` asc, `id` desc");
	$cv = 0;
	/* Video Overlay Pagination Code Begins */
	$row_counter = 0; // Counts the number of video pages left to right
	$videos_per_row = 3;
	while ($video = mf($loadvideo)) { // Run only while there are videos to display
	
		$video_id = $video["id"];
		$video_image = $video["image"];
		
		if( !($cv % $videos_per_row) ) { // If it has listed 3 videos (or is the first row), start a new row
			++$row_counter;
			if( $row_counter != 1 ) // End previous row, unless it's the first
				$artist_videos .= '</div>';
			$artist_videos .= '<div class="video-row video-row-' . $row_counter . '" id="' . $row_counter . '">'; // Start new row with $row_counter as class
		} 
		
		$artist_videos .= '<li id="video_'.$cv.'" class="playlist_video_master'; // Create <li> entry for video
		if( !(($cv+1) % $videos_per_row) ) $artist_videos .= ' last'; // Adds a CSS tag for the last video in the row
							
		$artist_videos .= '"><div class="playlist_video"><img src="artists/images/'.$video_image.'" border="0" /></div></li>'."\n"; // Display video thumb
		++$cv;
	}	
	if( $artist_videos ) $artist_videos .= '</div>'; // Closes last pagination row, as long as artist has videos

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
<link href="jplayer/style.css" rel="stylesheet" type="text/css" /> 
<!--<link rel="stylesheet" media="all and (orientation:portrait)" href="<?=trueSiteUrl();?>/jplayer/portrait.css">-->
<link rel="stylesheet" href="jplayer/css/supersized.core.css" type="text/css" media="screen" />

<link media="only screen and (max-device-width: 480px)" href="<?=trueSiteUrl();?>/jplayer/iphone.css" type= "text/css" rel="stylesheet" />

<link href="css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />

<!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
<script src="js/swfobject.js"  type="text/javascript"></script>
<script src="jplayer/js/supersized.3.1.3.core.min.js" type="text/javascript"></script>
<script src="jplayer/js/jquery.simplyscroll-1.0.4.js" type="text/javascript"></script>
<script src="jplayer/jquery.jplayer.js" type="text/javascript"></script> 

<script src="jplayer/js/ra_controls.js" type="text/javascript"></script>
<script src="jplayer/js/index.js" type="text/javascript"></script>
<script src="js/application.php?id=<?php echo $artist_id;?>"  type="text/javascript"></script>
<script src="jplayer/demos.common.js" type="text/javascript"></script> 

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/jquery.easing.1.3.js" type="text/javascript"></script>
<script src="js/jquery.mousewheel.min.js" type="text/javascript"></script>

<script src="js/artist_home.js" type="text/javascript"></script>
<script src="js/artist_home_ui.js" type="test/javascript"></script>

	<script type="text/javascript">

        var g_totalListens = <?=$total_listens?>;
        var g_logoOpen = false;

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
                var cart = "&paypal=<?=$paypalEmail;?>&cart=true&artist=<?=$artist_id;?>";
                $.post("jplayer/ajax.php", cart, function(items) {
                      $(".cart").html(items);
                      });
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
                $('#song_buy_popup').hide();
			});
			
			// Shopping Cart Functionality
			$("div.addtocart").click(function(event){
				var pro = $(this).text();
				var cart = "&paypal=<?=$paypalEmail;?>&cart=true&artist=<?=$artist_id;?>&product="+pro;
				$.post("jplayer/ajax.php", cart, function(items) {
                    $(".cart").html(items);
                    showCart(false);
				});
			});
			
			$("div.showstore").click(function(event){
                showProducts(true);
			});
			$("div.showcart").click(function(event){
                showCart(true);
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
			
			$(".bottom").click(function(event)
            {
				if(!g_logoOpen) 
                {
					$('#makeroomforlogo').animate({ height: "160px" }, 300);
				} 
                else 
                {
					$('#makeroomforlogo').animate({ height: "0px" }, 300);
				}
                g_logoOpen = !g_logoOpen;
                $('#logo').toggleClass('openlogo');
			});	
			
			$(".submitNewsletter").click(function(event){
			
				var loader = "Message pending...";
				$("#successMessage").slideToggle(100);
				$("#successMessage").html(loader);
				
				// Grab the current data
				var artist = "<?=$artist_id;?>";
				var name = $('#emailName').val();
				var email = $('#emailEmail').val();
				var submited = "&newsletter=true&artist=" + artist + "&name=" + name + "&email=" + email;
				
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

var g_myPlayList = [
    <?=$musicList;?>
];


function clickSongBuy(i)
{
    var id = '#song_buy_icon_' + i;
    var pos = $(id).offset();
    var top = pos.top - 38;
    var left = pos.left;
    
    var song = g_myPlayList[i];
    if( song.itunes )
    {
        $('#song_buy_popup_itunes').show();
        $('#song_buy_popup_itunes').attr('href',song.itunes);
    }
    else
    {
        $('#song_buy_popup_itunes').hide();
    }
    if( song.amazon )
    {
        $('#song_buy_popup_amazon').show();
        $('#song_buy_popup_amazon').attr('href',song.amazon);
    }
    else
    {
        $('#song_buy_popup_amazon').hide();
    }
    
    $('#song_buy_popup').css('top',top);
    $('#song_buy_popup').css('left',left);
    $('#song_buy_popup').show();
}

function sendContactForm()
{
    $('#contact_table').hide();
    $('#contact_thanks').show();
    
    // Grab the current data
    var artist_id = "<?=$artist_id;?>";
    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var phone = $('#contact_phone').val();
    var comments = $('#contact_comments').val();
    var submit = "&form=send&artist_id=" + artist_id + "&name=" + name + "&email=" + email + "&phone=" + phone + "&comments=" + comments;
    
    //submit the data for processing
    $.post("jplayer/ajax.php", submit, function(response) { });
}

$(document).ready(function(){

	var playItem = 0;
 
 
	// Local copy of jQuery selectors, for performance.
	var jpPlayTime = $("#jplayer_play_time");
	var jpTotalTime = $("#jplayer_total_time");
	var jpStatus = $("#demo_status"); // For displaying information about jPlayer's status in the demo page
 
	$("#jquery_jplayer").jPlayer({
		ready: function() {
			displayPlayList();
			playListInit(true); // Parameter is a boolean for autoplay.
		},
        solution: "html, flash",
        supplied: "mp3, oga",
        swfPath: "/js/Jplayer2.swf",
        verticalVolume: true,
        wmode: "window"
	})
	.bind($.jPlayer.event.ended, function() {
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
		for( var i = 0 ; i < g_myPlayList.length ; ++i ) 
        {
            var song = g_myPlayList[i];
			var listItem = (i == g_myPlayList.length-1) ? "<li class='jplayer_playlist_item_last'>" : "<li>";
			listItem += song.plus;
            listItem += "<a href='#' id='jplayer_playlist_item_" + i + "' tabindex='1'>";
            listItem += "<span class='thisisthetrackname'>" + song.name + "</span>";
            listItem += "<span class='songimage' style='display: none;'>" + song.image + "</span>";
            listItem += "<span class='sellitunes' style='display: none;'>" + song.itunes + "</span>";
            listItem += "<span class='sellamazon' style='display: none;'>" + song.amazon + "</span>";
            listItem += "<div class='songbgcolor' style='display: none;'>" + song.bgcolor + "</div>";
            listItem += "<div class='songbgposition' style='display: none;'>" + song.bgposition + "</div>";
            listItem += "<div class='songbgrepeat' style='display: none;'>" + song.bgrepeat + "</div>";
            listItem += "</a>";
            if( song.download )
            {
                listItem += song.download;
            }
            else if( song.amazon || song.itunes )
            {
                listItem += "<span id='song_buy_icon_" + i + "' class='song_buy_icon' onclick='clickSongBuy(" + i + ");'>";
                listItem += "<img src='/images/buy_icon.png'/>";
                listItem += "</span>";
            }
			listItem += "<div class='clear'></div>";
			listItem += "<div class='metadata'>This is a test</div>";
			listItem += "<div class='clear'></div>";
            listItem += "</li>";
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
        var media = {
            mp3: g_myPlayList[playItem].mp3,
            oga: g_myPlayList[playItem].mp3.replace(".mp3",".ogg")
        };
		$("#jquery_jplayer").jPlayer("setMedia", media);
		
		
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

            $('#image').css("background-color", "#"+color);
            var src_arg = "/artists/images/" + image;
            if( repeat == "no-repeat" || repeat == "stretch" )
            {
                var img_url = "http://www.myartistdna.com/timthumb.php?src=" + src_arg + "&w=" + getWindowWidth() + "&h="+ getWindowHeight() + "&zc=0&q=100";
                //$('#image').html("<img src='" + img_url + "' style='vertical-align:middle; margin-top:-" + (getWindowHeight()/2) + "px; margin-left:-" + (getWindowWidth()/2) + "px;' />");
                var style = "width: 100%; height: 100%;";
                var html = "<img src='" + img_url + "' style='" + style + "'/>";
                $('#image').html(html);
            }
            else
            {
                $('#image').html("");
                $('#image').css("background-image: url('" + src_arg +"'); background-repeat: repeat;");
            }
            $('#image').fadeIn();
			
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
            /*
			var track = "&track="+image;
			$.post('jplayer/ajax.php', track, function(data) {
					$('.current-track').html(data);
			});
			*/
            
            $('#current_track_name').text(trackname);
            
            $(".vote").click(function(event) 
                {
                   var voteBody = $(this).text();
                   var voteData = "&vartist=<?=$artist?>";
                   voteData += "&vtrack=" + image;
                   voteData += "&vote=" + voteBody;
                   
                   $.post("jplayer/ajax.php", voteData, function(voteResultsNow) {
                          $("#results").html(voteResultsNow);
                          $("#results").fadeIn();
                          setTimeout(function(){ 
                                     $("#results").fadeOut();
                                     }, 2000);
                          });
                });
            
            g_totalListens++;
            //$('#total_listens').text(g_totalListens);
            updateListens(image);
            
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
		var index = (playItem+1 < g_myPlayList.length) ? playItem+1 : 0;
		playListChange( index );
	}
 
	function playListPrev() {
		var index = (playItem-1 >= 0) ? playItem-1 : g_myPlayList.length-1;
		playListChange( index );
	}
	
});
	
-->
</script> 

			<div id="results"></div>
            <div id="shop_results"></div>

			
			<? if (!$fan) { ?>
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
			<div id="loader"><img src="/jplayer/images/ajax-loader.gif" /></div>
			
			<? if (!$fan) { ?>
			<div id="logo">
                <button id="login_signup_button" onclick='showLogin();'>Log in | Sign Up</button>
				<div id="makeroomforlogo">
				<? if ($artist_logo) { ?><img src="http://www.myartistdna.com/timthumb.php?src=/artists/images/<?=$artist_logo;?>&q=100&h=145&w=145" /><? } ?>
				</div>
				<div id="makeroomfordetails">
					<div class="clear"></div>
					
					
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
					
				</ul>
				<div class="clear"></div>
			</div>
			
			<div id="jquery_jplayer" class="jp-jplayer"></div> 

			<div class="top-bg"></div>
			<div id="playlister">
				<div class="playlist-main"></div>
				<div class="playlist-bottom"></div>
			</div>			
			
			<div class="jp-audio">
				<div class="jp-playlist-player">
				
					<div id="jp_container_1" class="jp-interface">
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

						<div class="current-track">
                            <div style='float: left; padding-right: 10px;'>
                                <span style='color: #555 !important;'>Artist:</span> 
                                <span id='current_track_artist_name'><?=$artist_name;?></span> 
                                <span style='color: #555 !important;'>// Track:</span>
                                <span id='current_track_name'></span>
                                <span style='color: #555 !important; <? if( !$show_listens ) echo "display: none;"; ?>'>// Listens:</span>
                                <span id='current_track_listens'><?=$first_track_listens;?></span>
                            </div>
                            <div class='vote'>1</div>
                            <div class='vote nay'>0</div>
                            <div class='clear'></div>
                        </div>
						
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
					<h2>Coming Soon</h2>
					<ul>
						<li><span>Attn:</span> </li>
						<li><span>ph:</span> </li>
						<li><span>fx:</span> </li>
						<li><span>email:</span> </li>
					</ul>
					
					<br />
					
					<h1><span class="slashes">//</span> Booking</h1>
					<h2>Coming Soon</h2>
					<ul>
						<li><span>Attn:</span> </li>
						<li><span>ph:</span></li>
						<li><span>fx:</span></li>
						<li><span>email:</span></li>
					</ul>
					
				</div>
				
				<div class="left">
					<h1>CONTACT <span class="slashes">//</span> <?=$artist_name;?></h1>
					<table id="contact_table">
						<tr>
							<td><span class="red">*</span> Name:</td>
							<td><input type="text" value="Name..." name="name" id="contact_name" onfocus="clickclear(this, 'Name...')" onblur="clickrecall(this, 'Name...')" /></td>
						</tr>
						<tr>
							<td><span class="red">*</span> E-Mail:</td>
							<td><input type="text" value="Email..." name="email" id="contact_email" onfocus="clickclear(this, 'Email...')" onblur="clickrecall(this, 'Email...')" /></td>
						</tr>
						<tr>
							<td class="message"><span class="red">*</span> Message:</td>
							<td><textarea name="comments" class="textarea" id="contact_comments"></textarea></td>
						</tr>
						<tr>
							<td><span class="red">*</span> required</td>
							<td>
                                <button id="contact_submit" onclick="sendContactForm();">submit</button>
                                <button id="contact_clear" onclick="clearContactForm();">clear form</button>
                            </td>
						</tr>
					</table>
                    <div id="contact_thanks" style="height: 180px; display: none;">Thank you for your message.</div>
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
									<div class="active right-arrow"><span></span></div>
								</div>
								<?= $artist_videos; ?>
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
				<div class="showstore" style="display:none;">Store Front</div>
				<div class="showcart">View Cart</div>
				<div class="clear"></div>
				</div>
			
				<h1>Store</h1>
				
				<div class="clear"></div>
				<div class="cart" style="display:none;"></div>
				
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
			</div>
        </div>
    </div>
			
			

<script type="text/javascript">
(function($) {
	$(function() { //on DOM ready
		setTimeout(function(){ 
			$("#playlist").simplyScroll({
				className: 'vert',
				horizontal: false,
                frameRate: 30,
				speed: 5
			});		
		}, 500);
	});
})(jQuery);
</script>

    <div id='song_buy_popup'>
        <a id='song_buy_popup_amazon' href='#' class='store_icon amazon'></a>
        <a id='song_buy_popup_itunes' href='#' class='store_icon itunes'></a>
    </div>

    <!-- SIGNUP FORM -->
    <div id="signup_dialog" class="window">
    <div id="popup">
        <div class="topbox">
        <h3>SIGN UP FOR MYARTISTDNA</h3>
        <div class="close"><a href="#" onclick='closeSignup();'>CLOSE</a></div>
        </div>
        
       <!-- <div class="offer">
        <h2><span>You selected:</span> <br> Basic Package</h2>
        <h3>FREE</h3>
        </div> -->
        
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
                <li><input name="agree" id="agree" type="checkbox" value="agree"> <span>I agree to the Terms &amp; Conditions of MyArtistDNA</span></li>
            </ul>
            <div class="button"><a href="#" class="submit">Complete Signup</a></div>
            </fieldset>
        </form>
        </article>
        </div> 
    </div><!-- pop up -->
    </div>
    <!-- END SIGNUP FORM -->

    <!-- LOGIN FORM -->
    <div id="login_dialog" class="window">
        <div id="popup">
            <div class="topbox">
                <h3>LOG IN TO MYARTISTDNA</h3>
                <div class="close"><a href="#" onclick='closeLogin();'>CLOSE</a></div>
            </div>

            <div class="loginpop">
                <div id="validate-login"></div>
                <form action="" name="loginPopup" method="post">
                <fieldset>
                <ul>
                <li><label>Email Address</label> <input id='login_username' name="username" type="text" class="input" value="" /></li>
                <li><label>Password</label> <input id='login_password' name="password" type="text" class="input" value="" /></li>
                </ul>
                <p class="password"><a href="/?p=index&forgot=true">Forgot your password?</a></p>
                <div class="button"><a href="#-1" onclick="tryLogin();">LOGIN</a></div>
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
                <div class="buttonsignup"><a href="#" onclick='showSignup();'>SIGN UP</a></div>
            </div>
            </div>
        </div><!-- pop up -->
    </div> 
    <!-- END LOGIN FORM -->

    <!-- Mask to cover the whole screen --> 
    <div id="mask"></div> 
    </div>


<!-- Custom scrollbar Starts -->
<script>
$(window).load(function() {
               //$("#mcs_container").mCustomScrollbar("vertical",400,"easeOutCirc",1.05,"auto","yes","yes",10);
               });
</script>
<script src="js/jquery.mCustomScrollbar.js" type="text/javascript"></script>
<!-- Custom scrollbar Ends -->

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