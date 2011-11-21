<?
//	include('../includes/config.php');
//	include('../includes/functions.php');	

	$artist_url = $GLOBALS["artist_url"];
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
	
	// Build Music
	if ($fan) {
		$mQuery = "`user`='{$artist_id}'";
		$downQ = "&user={$artist_id}";
	} else {
		$mQuery = "`artistid`='{$artist_id}' and `type`='0'";	
	}
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
			$musicList .= '{id:'.$music_id.',name:"<small>'.$music_artist.' - '.$music_name.'</small>",mp3:"'.trueSiteUrl().'/artists/audio/'.$music_audio.'",download:"'.$music_download.'",image:"'.$music_image.'",bgcolor:"'.$music_bgcolor.'",bgrepeat:"'.$music_bgrepeat.'",bgposition:"'.$music_bgposition.'"}';
		} else {
			$musicList .= '{id:'.$music_id.',name:"'.$music_name.'",mp3:"'.trueSiteUrl().'/artists/audio/'.$music_audio.'",download:"'.$music_download.'",image:"'.$music_image.'",bgcolor:"'.$music_bgcolor.'",bgrepeat:"'.$music_bgrepeat.'",bgposition:"'.$music_bgposition.'",plus:"",amazon:"'.$music_amazon.'",itunes:"'.$music_itunes.'"}'; //,plus:"<a href=\'http://www.google.com\' target=\'_blank\' class=\'plus\' onclick=\'javascript: void(0);\'>Test</a>
		}
		++$m;
		$total_listens = $total_listens + $music_listens;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>MyArtistDNA.fm<?=$artist_name?></title>
<link href="/jplayer/iphone.css" rel="stylesheet" type="text/css" /> 

<link media="only screen and (max-device-width: 480px)" href="<?=trueSiteUrl();?>/jplayer/iphone.css" type="text/css" rel="stylesheet" />

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="apple-touch-icon" href="<?=trueSiteUrl();?>/artists/images/<?=$artist_logo;?>" />
<link rel="apple-touch-startup-image" href="<?=trueSiteUrl();?>/includes/images/apple-loading-screen.jpg" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/jplayer/js/jquery.simplyscroll-1.0.4.js"></script>

<script type="text/javascript" src="/jplayer/jquery.jplayer.min.js"></script> 
<script type="text/javascript" src="/jplayer/demos.common.js"></script> 
	<script>
	
        var g_totalListens = <?=$total_listens?>;
	
		$(document).ready(function(){

			//$('#image').hide();
			
			$(".bottom").click(function(event){
				var logoclass = $(this).parent("#logo").attr("class");
				if (logoclass == "openlogo") {
					$(this).parent("#logo").animate({
						marginTop: "-175px"
					}, 500);
				} else {
					$(this).parent("#logo").animate({
						marginTop: "0px"
					}, 500);
				}
				$(this).parent("#logo").toggleClass("openlogo");
			});	
			

		});
		
	</script>	

<body>

<script type="text/javascript"> 
<!--

var playItem = 0;

var g_myPlayList = [ <?=$musicList;?> ];

var anchor = self.document.location.hash.substring(1);
var anchor_elements = anchor.split('&');
var g_anchor_map = {};
for( var k in anchor_elements )
{
    var e = anchor_elements[k];
    var k_v = e.split('=');
    
    k = unescape(k_v[0]);
    if( k_v.length > 1 )
        g_anchor_map[k] = unescape(k_v[1]);
    else
        g_anchor_map[k] = true;
}
if( 'song_id' in g_anchor_map )
{
    var song_id = g_anchor_map['song_id'];
    for( var k in g_myPlayList )
    {
        var song = g_myPlayList[k];
        if( song['id'] == song_id )
        {
            playItem = Number(k);
            break;
        }
    }
}

$(document).ready(function()
{
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
		for (i=0; i < g_myPlayList.length; i++) {
			var listItem = (i == g_myPlayList.length-1) ? "<li class='jplayer_playlist_item_last'>" : "<li>";
			listItem += g_myPlayList[i].plus + "<a href='#' id='jplayer_playlist_item_" + i + "' tabindex='1'><span class='thisisthetrackname'>" + g_myPlayList[i].name + "</span><span class='songimage' style='display: none;'>" + g_myPlayList[i].image + "</span><span class='sellitunes' style='display: none;'>" + g_myPlayList[i].itunes + "</span><span class='sellamazon' style='display: none;'>" + g_myPlayList[i].amazon + "</span><div class='songbgcolor' style='display: none;'>" + g_myPlayList[i].bgcolor + "</div><div class='songbgposition' style='display: none;'>" + g_myPlayList[i].bgposition + "</div><div class='songbgrepeat' style='display: none;'>" + g_myPlayList[i].bgrepeat + "</div></a>" + g_myPlayList[i].download;
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
		$("#jquery_jplayer").jPlayer("setFile", g_myPlayList[playItem].mp3);
		
		
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

            var src_arg = "/artists/images/" + image;
            var img_url = "/timthumb.php?src=" + src_arg + "&w=" + getWindowWidth() + "&h="+ getWindowHeight() + "&zc=0&q=100";
			//$('#image').html("<img src='" + img_url + "' style='vertical-align:middle; margin-top:-" + (getWindowHeight()/2) + "px; margin-left:-" + (getWindowWidth()/2) + "px;' />");
			$('#image').html("<img src='" + img_url + "' style='margin-top:55px;' />");

			// Function that gets window width
			function getWindowWidth() {
				screenMinWidth = 350; // Minimum screen width
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
				screenMinHeight =  (480-55); // Minimum screen height
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
			
			/*if (repeat == "stretch") {
				$('#image').css({
					backgroundImage: "url('<?=trueSiteUrl();?>/artists/images/')", backgroundRepeat: repeat, backgroundPosition: position
				});
				$('#image').html("<img src='<?=trueSiteUrl();?>/artists/images/"+image+"' width='100%' style='vertical-align:middle;' />");
			} else {
				$('#image').html("");	
				$('#image').css({
					backgroundImage: "url('<?=trueSiteUrl();?>/artists/images/" + image + "')", backgroundRepeat: repeat, backgroundPosition: position
				});
			}*/
			
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
			$.post('jplayer/ajaxIphone.php', track, function(data) {
					$('.current-track').html(data);
			});
			
            g_totalListens++;
            //$('#total_listens').text(g_totalListens);
            //updateListens(image);
			
			setTimeout(function(){ 
				$('#loader').hide();
			}, 1500);
		
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
	
	/* Show & Hide Playlist Button */
	$(".playlist-visibility").toggle(function(){
		$(".playlist-visibility .show").show(); $(".playlist-visibility .hide").hide();},
		function(){
		$(".playlist-visibility .show").hide(); $(".playlist-visibility .hide").show();}
	);
	$(".playlist-visibility .show").click(function(){
		$('#jplayer_playlist').fadeIn(); });
	$(".playlist-visibility .hide").click(function(){
		$('#jplayer_playlist').fadeOut(); });


});
-->
</script> 

			<div id="iphonetopbg"></div>
			<div id="results"></div>
			
			

			<div id="image"></div>
			<div id="loader"><img src="<?=trueSiteUrl();?>/jplayer/images/ajax-loader.gif" /></div>

					
					<div class="mighthide">
						<div class="buynow"></div>
						<!-- <a href="#" class="buynow mystore"></a> -->
						<span class="showamazon"></span>
						<span class="showitunes"></span>
						<div class="clear"></div>
					</div>
					
			<div id="progressbg"></div>
			<div id="jquery_jplayer"></div> 
			<div class="top-bg"></div>
			<div class="listens">
				<span style='<? if( !$show_listens ) echo "display: none;"; ?>'>Total Listens: </span><span id='current_track_listens'><?=$total_listens?></span>
			</div>
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
					
					<div id="jplayer_playlist" class="jp-playlist" style="display: none;"> 
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
			<a class="jp-play-fake"></a>
			<a class="jp-pause-fake"></a>
			
<!--
			<div id="getsocial">
			<? if ($artist_twitter != "") { ?> <a target="_blank" href="http://www.twitter.com/<?=$artist_twitter;?>" class="twitter">Twitter.com/<?=$artist_twitter;?></a> <? } ?>
			<? if ($artist_facebook != "") { ?> <a target="_blank" href="http://www.facebook.com/<?=$artist_facebook;?>" class="facebook">Facebook.com/<?=$artist_facebook;?></a> <? } ?>
			<? if ($artist_website != "") { ?> <a target="_blank" href="http://<?=$artist_website;?>" class="www"><?=$artist_website;?></a> <? } ?>
			<div class="clear"></div>
			</div>
-->
			<div id="iphonebottombg">
				<div class="logo_img">
					<a target="_blank" href="http://www.myartistdna.fm/artists.php"></a>
				</div>
				<div class="social-media">
					<!--<div class="copyright">&copy; Copyright 2011 MyArtistDNA.fm</div>-->
					<? if ($artist_twitter != "") { ?> <a target="_blank" href="http://www.twitter.com/<?=$artist_twitter;?>" class="twitter">Twitter.com/<?=$artist_twitter;?></a> <? } ?>
					<? if ($artist_facebook != "") { ?> <a target="_blank" href="http://www.facebook.com/<?=$artist_facebook;?>" class="facebook">Facebook.com/<?=$artist_facebook;?></a> <? } ?>
					<? if ($artist_website != "") { ?> <a target="_blank" href="http://<?=$artist_website;?>" class="www"><?=$artist_website;?></a> <? } ?>
					<!--<div class="clear"></div>-->
				</div>
				<span class="playlist-visibility"><span class="hide">Hide Playlist</span><span class="show">Show Playlist</span></span>
			</div>
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
TEST
</body>
</html>