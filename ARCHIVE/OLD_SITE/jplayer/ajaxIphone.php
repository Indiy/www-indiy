<?

	include('../includes/config.php');
	include('../includes/functions.php');	

	
	if ($_REQUEST["vote"] != "") {
		$artist = $_REQUEST["vartist"];
		$audio = $_REQUEST["vtrack"];
		$result = $_REQUEST["vote"];
		insert("[p]musicplayer_votes","artistid|audioid|vote|ip","{$artist}|{$audio}|{$result}|{$ip}");
		echo "Thank you for your vote!";
	}
	
	
	
	
	if ($_REQUEST["track"] != "") {
		$track = $_REQUEST["track"];
		$artist = $_REQUEST["artist"];
		// Build Music
		$loadmusic = mq("select * from `[p]musicplayer_audio` where `artistid`='{$artist}' order by `order` asc, `id` desc");
		$m=0;
		while ($music = mf($loadmusic)) {
			$music_id = $music["id"];
			$music_name = stripslashes($music["name"]);
			if ($m == $track) {
				$track_name = $music_name;
				$track_id = $music_id;
			}
			++$m;
		}
		
		// trackViews($artist_id);
		
		echo '
		<script>
		$(document).ready(function(){
			$(".vote").click(function(event) {
				var voteBody = $(this).text();
				var voteData = "&vartist='.$artist.'&vtrack='.$track_id.'&vote=" + voteBody;
				$.post("jplayer/ajax.php", voteData, function(voteResultsNow) {
					$("#results").html(voteResultsNow);
					$("#results").fadeIn();
					setTimeout(function(){ 
						$("#results").fadeOut();
					}, 2000);
				});
			});
		});
		</script>
		'."
		<div style='float: left; padding-right: 10px;'>{$track_name}</div> <div class='vote'>1</div><div class='vote nay'>0</div><div class='clear'></div>";
	}
	
	if ($_REQUEST["imageid"] != "") {
		
		$track = $_REQUEST["imageid"];
		$artist = $_REQUEST["artist"];
		// Build Music

		$loadmusic = mq("select * from `[p]musicplayer_audio` where `artistid`='{$artist}' order by `order` asc, `id` desc");
		$m=0;
		while ($music = mf($loadmusic)) {
			$music_image = $music["image"];
			if ($m == $track) {
				$track_image = $music_image;
				$music_id = $music["id"];
			}
			++$m;
		}
		trackViews($music_id);
		echo $track_image;
		
		//echo "Track = {$track} & Artist = {$artist}";
	}	
	
	if ($_REQUEST["page"] != "") {
		$page = $_REQUEST["page"];
		$artist = $_REQUEST["artist"];
		
		// Build Pages
		$pages = mf(mq("select * from `[p]musicplayer_content` where `id`='{$page}' and `artistid`='{$artist}' limit 1"));

		$page_name = stripslashes($pages["name"]);
		if ($pages["body"] != "") { 
			$page_body = '<p>'.nohtml($pages["body"]).'</p>';
		} else { 
			$page_body = '';
		}
		if ($pages["video"] != "") {
			$breakvideo = explode(".com/", $pages["video"]);
			if ($breakvideo[0] == "http://www.youtube") {
				$breakmore = explode("&", str_replace("watch?v=", "", $breakvideo[1]));
				$videourl = "http://www.youtube.com/embed/".$breakmore[0];
			} else if ($breakvideo[0] == "http://vimeo") {
				$videourl = "http://player.vimeo.com/video/".$breakvideo[1];
			} else {
				
			}
			if ($videourl != "") {
				$page_video = '<div class="video jp-pause"><iframe width="260" height="230" src="'.$videourl.'" frameborder="0" allowfullscreen></iframe></div>';
			}
		} else { 
			$page_video = '';
		}
		if ($pages["image"] != "") { 
			$page_image = '<img class="image" src="http://www.myartistdna.com/myaudioplayer/artists/images/'.$pages["image"].'" border="0" style="width: 260px !important;" />';
		} else { 
			$page_image = '';
		}

		$page_content = "<h1>{$page_name}</h1>{$page_image}{$page_video}{$page_body}<div class='clear'></div>";
		pageViews($page);
		echo $page_content;
	}

?>