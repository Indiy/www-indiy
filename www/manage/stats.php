<?
	require_once('../includes/config.php');
	include_once('../includes/functions.php');	

	if (isLoggedIn() != "true") {
		if (isAdmin()) {
			
		} else {
			die("You must be logged in");
		}
	}

	$id = $_REQUEST["userId"];
	
	// Player views
	$stats_player = mf(mq("select `views` from `[p]musicplayer` where `id`='{$id}'"));
	$total_player_views = $stats_player["views"];
	
	// Build Page Graph
	$loadpages = mq("select `views` from `[p]musicplayer_content` where `artistid`='{$id}' order by `order` asc, `id` desc");
	while ($pa = mf($loadpages)) {
		$buildmax .= $pa["views"].",";
	}
	$size = 100 / max(num($loadpages),1);
	//$size = num($loadpages) / 100;
	$buildmax = explode(",", $buildmax);
	$max = max($buildmax);
    if( $max == 0 )
        $max = 1;

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
	}

	
	// Build Song Graph
	$loadmuse = mq("select `views` from `[p]musicplayer_audio` where `artistid`='{$id}' and `type`='0' order by `order` asc, `id` desc");
	while ($mu = mf($loadmuse)) {
		$buildmaxe .= $mu["views"].",";
	}
	$msize = 100 / max(num($loadmuse),1);
    $msize = max($msize,100.0/6);
	$sizee = round(($msize) / 2, 2);
	$buildmaxe = explode(",", $buildmaxe);
	$maxe = max($buildmaxe);
    
    $c=0;
    $songListHtml = '';
    $songList = '';
    $songNames = '';

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
        if( $c == 6 )
        {
            $songListHtml .= '<div class="sep"></div>';
            $songListHtml .= '<div class="graph">';
            $songListHtml .= '<div style="height: 10px;">&nbsp;</div>';
            $songListHtml .= $songList;
            $songListHtml .= '<div class="clear"></div>';
            $songListHtml .= '</div>';
            $songListHtml .= '<div class="names">';
            $songListHtml .= $songNames;
            $songListHtml .= '<div class="clear"></div>';
            $songListHtml .= '</div>';
            
            $songList = '';
            $songNames = '';
            $c = 0;
        }
	}

    if( strlen($songList) > 0 )
    {
        $songListHtml .= '<div class="sep"></div>';
        $songListHtml .= '<div class="graph">';
        $songListHtml .= '<div style="height: 10px;">&nbsp;</div>';
        $songListHtml .= $songList;
        $songListHtml .= '<div class="clear"></div>';
        $songListHtml .= '</div>';
        $songListHtml .= '<div class="names">';
        $songListHtml .= $songNames;
        $songListHtml .= '<div class="clear"></div>';
        $songListHtml .= '</div>';
    }
    

    function make_q_html($sql)
    {

        $max_views = 0;
        $video_list = array();
        $video_q = mq($sql);
        while( $video = mf($video_q) )
        {
            $views = intval($video['views']);
            $max_views = max($max_views,$views);
            $video_list[] = $video;
        }
        $video_plays_html = "";

        $frag_html = "";
        $i = 0;
        foreach( $video_list as $video )
        {
            $id = $video['id'];
            $name = $video['name'];
            $views = intval($video['views']);
            $percent = number_format($views / $max_views * 100.0,4);
            
            $html = "";
            $html .= "<div class='item'>";
            $html .= " <div class='number'>$views</div>";
            $html .= " <div class='bar_holder'>";
            $html .= "  <div class='bar' style='height: $percent%;'></div>";
            $html .= " </div>";
            $html .= " <div class='line'></div>";
            $html .= " <div class='name'>$name</div>";
            $html .= "</div>";
            
            $frag_html .= $html;
        
            if( $i % 6 == 5 )
            {
                $html = "";
                $html .= "<div class='graph_row'>";
                $html .= $frag_html;
                $html .= "</div>";
                
                $video_plays_html .= $html;
                $frag_html = "";
            }
        
            $i++;
        }
        
        if( strlen($frag_html) > 0 )
        {
            $html = "";
            $html .= "<div class='graph_row'>";
            $html .= $frag_html;
            $html .= "</div>";
            
            $video_plays_html .= $html;
            $frag_html = "";
        }
        return $video_plays_html;
    }
    
    $sql = "SELECT id,name,views FROM mydna_musicplayer_video WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $video_plays_html = make_q_html($sql);
	
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
	
	include_once "templates/stats.html";

?>