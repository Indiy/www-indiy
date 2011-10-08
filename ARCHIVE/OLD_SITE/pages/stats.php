<?

	if (isLoggedIn() != "true") {
		if (isAdmin()) {
			
		} else {
			die("You must be logged in");
		}
	}

	$id = $_GET["id"];
	
	// Player views
	$stats_player = mf(mq("select `views` from `[p]musicplayer` where `id`='{$id}'"));
	$total_player_views = $stats_player["views"];
	
	// Build Page Graph
	$loadpages = mq("select `views` from `[p]musicplayer_content` where `artistid`='{$id}' order by `order` asc, `id` desc");
	$c=1;
	while ($pa = mf($loadpages)) {
		$buildmax .= $pa["views"].",";
	}
	$size = 100 / num($loadpages);
	//$size = num($loadpages) / 100;
	$buildmax = explode(",", $buildmax);
	$max = max($buildmax);

	$loadpag = mq("select `id`,`name`,`artistid`,`order`,`views` from `[p]musicplayer_content` where `artistid`='{$id}' order by `order` asc, `id` desc");
	while ($pages = mf($loadpag)) {
		$page_id = $pages["id"];
		$page_views = $pages["views"];
		$page_percent = floor(($page_views / $max) * 100);
		$page_filler = floor(100 - $page_percent);
		$page_name = stripslashes($pages["name"]);
		$pageList .= '
			<div class="bar page" style="width: '.$size.'%; background-position: 0px '.$page_filler.'px !important;">'.$page_views.'</div>
		';
		$pageNames .= '
			<div style="width: '.$size.'%; float: left; text-align: center;">'.$page_name.'</div>
		';
		++$c;
	}

	
	// Build Song Graph
	$loadmuse = mq("select `views` from `[p]musicplayer_audio` where `artistid`='{$id}' and `type`='0' order by `order` asc, `id` desc");
	$c=1;
	while ($mu = mf($loadmuse)) {
		$buildmaxe .= $mu["views"].",";
	}
	$msize = 100 / num($loadmuse);
	$sizee = round(($msize) / 2, 2);
	$buildmaxe = explode(",", $buildmaxe);
	$maxe = max($buildmaxe);

	$loadmusic = mq("select * from `[p]musicplayer_audio` where `artistid`='{$id}' and `type`='0' order by `order` asc, `id` desc");
	while ($p = mf($loadmusic)) {
		$p_id = $p["id"];
		$p_views = $p["views"];
		$p_percent = floor(100 -($p_views / $maxe) * 100);
		
		$p_downloads = $p["download"];
		$p_dpercent = floor(100 - ($p_downloads / $maxe) * 100);
		
		$p_name = stripslashes($p["name"]);
		$songList .= '
			<div style="width: '.$msize.'%; height: 100px; float: left; text-align: center;">
				<div class="clear"></div>
				<div class="bar" style="width: 50%; background-position: 0px '.$p_percent.'px !important;">'.$p_views.'</div>
				<div class="bar download" style="width: 50%; background-position: 0px '.$p_dpercent.'px !important;">'.$p_downloads.'</div>
				<div class="clear"></div>
			</div>
		';
		$songNames .= '
			<div style="width: '.$msize.'%; float: left; text-align: center;">
				<small>'.$p_name.'</small>
			</div>
		';		
		++$c;
	}

	

	
	// Build Love Hate Stats
	$loadvotes = mq("select `id`,`name` from `[p]musicplayer_audio` where `artistid`='{$id}' order by `order` asc, `id` desc");
	
	while ($vote = mf($loadvotes)) {
	
		$v_id = $vote["id"];
		$v_name = stripslashes($vote["name"]);

		$loadvotestats = mq("select `vote` from `[p]musicplayer_votes` where `artistid`='{$id}' and `audioid`='{$v_id}'");
		$l=0;
		$h=0;
		while ($cv = mf($loadvotestats)) {
			if ($cv["vote"] == "1") { ++$l; }
			if ($cv["vote"] == "0") { ++$h; }
		}
		
		$lovehate .= '<div class="voteStats"><div class="voteName">'.$v_name.'</div><div class="voteResult">Love: '.$l.' / Hate: '.$h.'</div><div class="clear"></div></div>';

	}
	
	
?>
				
				
				<div id="content">
					<div class="post">
						<h2 class="title"><a href="#">Analytics</a></h2>
					
						<h2>Total Player Views: <?=$total_player_views;?></h2>
						
						<h4>Page Views</h4>
						
							<div class="graph">
								<div style="height: 10px;">&nbsp;</div>
								<?=$pageList;?>
								<div class="clear"></div>
							</div>
							<div class="names">
								<?=$pageNames;?>
								<div class="clear"></div>
							</div>
							
						<h4>Song Plays and Downloads</h4>
						
							<div class="graph">
								<div style="height: 10px;">&nbsp;</div>
								<?=$songList;?>
								<div class="clear"></div>
							</div>
							<div class="names">
								<?=$songNames;?>
								<div class="clear"></div>
							</div>
							
							
						<h4>Love / Hate</h4>
						
							<div class="lovehate">
								<?=$lovehate;?>
								<div class="clear"></div>
							</div>

					</div>
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				
				
				
				<div id="sidebar">

				</div>
				<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>