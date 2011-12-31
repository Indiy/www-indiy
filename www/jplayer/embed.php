<?
//	include('../includes/config.php');
//	include('../includes/functions.php');	

	$artist_url = $GLOBALS["artist_url"];
	$row = mf(mq("select * from `[p]musicplayer` where `url`='{$artist_url}' limit 1"));
	$artist_id = $row["id"];
	$artist_name = stripslashes($row["artist"]);
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
				$(".page").hide();
				$(".page").html("<div class=\'pageload\'><img src=\''.trueSiteUrl().'/jplayer/images/page-loader.gif\' border=\'0\' /></div>").slideDown();
				$(".aClose").fadeIn();
				var body'.$page_id.' = "&artist='.$artist_id.'&page='.$page_id.'";
				$.post("jplayer/ajaxIphone.php", body'.$page_id.', function(data'.$page_id.') {
					$(".page").html(data'.$page_id.');
				});
			});
		'."\n";
	}
	
	// Build Music
	$loadmusic = mq("select * from `[p]musicplayer_audio` where `artistid`='{$artist_id}' order by `order` asc, `id` desc");
	$m=0;
	while ($music = mf($loadmusic)) {
		$music_id = $music["id"];
		$music_audio = $music["audio"];
		$music_name = stripslashes($music["name"]);
		$music_name = str_replace('"', '&quot;', $music_name);
		if ($music["download"] == "1") { 
			$music_download = '<a href=\'download.php?artist='.$artist_id.'&id='.$music_id.'\' title=\'Click here download '.$music_name.' for free\' class=\'download vtip\'>Download</a> '; 
		} else { 
			$music_download = ''; 
		}
		if ($m != "0") { $musicList .= ","; }
		$musicList .= '{name:"'.$music_name.'",mp3:"'.trueSiteUrl().'/artists/audio/'.$music_audio.'",download:"'.$music_download.'"}';
		++$m;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>MyArtistDNA - <?=$artist_name?></title>
<link href="<?=trueSiteUrl();?>/jplayer/iphone.css" rel="stylesheet" type="text/css" /> 

<link media="only screen and (max-device-width: 480px)" href="<?=trueSiteUrl();?>/jplayer/iphone.css" type="text/css" rel="stylesheet" />

<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="apple-touch-icon" href="<?=trueSiteUrl();?>/artists/images/<?=$artist_logo;?>" />
<link rel="apple-touch-startup-image" href="<?=trueSiteUrl();?>/includes/images/apple-loading-screen.jpg" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>

<script type="text/javascript" src="/jplayer/jquery.jplayer.min.js"></script> 
<script type="text/javascript" src="/jplayer/demos.common.js"></script> 
	<script>
		$(document).ready(function(){

			$('#image').hide();
			$('.page').hide();
			$('.comments').hide();
			$('.aClose').hide();
			
			/* Close */
			$('.aClose').click(function() {
				$('.aClose').fadeOut();
				$('.comments').fadeOut();
				$('.page').slideUp();
			});
			
			<?=$pagesJava;?>
			
			/* Comment */
			$('.aComment').click(function() {
				$('.page').fadeOut();
				setTimeout(function(){ 
					$('.comments').slideDown();
					$('.aClose').fadeIn();
				}, 450);
			});
			
			$('#playlist').hide();
			setTimeout(function(){ 
				$('#playlist').slideDown();
			}, 1000);
			$('#playTitle').click(function() {
				$('#playlist').slideToggle();
			});
			
			// Facebook
			
			$(".facebookstreamhide").hide();
			$(".facebookstreamcont").hide();
			
			$(".facebookstreamhide").click(function(event){
				$(".facebookstreamhide").hide();
				$(".facebookstreamshow").show();
				$(".facebookstreamcont").slideUp();
			});
			
			$(".facebookstreamshow").click(function(event){
				$(".facebookstreamshow").hide();
				$(".facebookstreamhide").show();
				$(".facebookstreamcont").slideDown();
			});
			
			// Twitter
			
			$(".twitterstreamhide").hide();
			$(".twitterstreamcont").hide();
			
			$(".twitterstreamhide").click(function(event){
				$(".twitterstreamhide").hide();
				$(".twitterstreamshow").show();
				$(".twitterstreamcont").slideUp();
			});
			
			$(".twitterstreamshow").click(function(event){
				$(".twitterstreamshow").hide();
				$(".twitterstreamhide").show();
				$(".twitterstreamcont").slideDown();
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
			listItem += myPlayList[i].download + "<a href='#' id='jplayer_playlist_item_" + i + "' tabindex='1'>" + myPlayList[i].name + "</a>";
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
			var getimg = "&artist=<?=$artist_id;?>&imageid=" + playItem;
			$.post('jplayer/ajaxIphone.php', getimg, function(newimage) {
				// alert(imageurl);
				// $('#image').css("background-image", "url('<?=trueSiteUrl();?>/artists/images/" + newimage + "')");
				$('#image').html("<img src='<?=trueSiteUrl();?>/artists/images/" + newimage + "' width='100%' border='0' />");
				$('#image').fadeIn();
			});
			
			// Display Current Track Title
			var track = "&artist=<?=$artist_id;?>&track=" + playItem;
			$.post('jplayer/ajaxIphone.php', track, function(data) {
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
		
});
-->
</script> 


			<div id="results"></div>
			
			

			<div id="image"></div>
			<div id="loader"><img src="<?=trueSiteUrl();?>/jplayer/images/ajax-loader.gif" /></div>
			<div id="logo">
				<?=$artist_name;?>
			</div>


			
			<div id="jquery_jplayer"></div> 

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
					
					<div id="jplayer_playlist" class="jp-playlist">
						<ul id="playlist">
							<li></li>
						</ul>
						<p id="playTitle">Playlist</p>
					</div>
				</div>
			</div>
			

			<div id="getsocial">
			<? if ($artist_twitter != "") { ?> <a href="http://www.twitter.com/<?=$artist_twitter;?>" class="twitter">Twitter.com/<?=$artist_twitter;?></a> <? } ?>
			<? if ($artist_facebook != "") { ?> <a href="http://www.facebook.com/<?=$artist_facebook;?>" class="facebook">Facebook.com/<?=$artist_facebook;?></a> <? } ?>
			<div class="clear"></div>
			</div>
						
			<a href="http://myartistdna.com/apps">
			<div class="footer">
				<div>&copy <?=date("Y");?> MyArtistDNA.fm</div>
			</div>
			</a>
			
			<div id="black"></div>

</body>
</html>