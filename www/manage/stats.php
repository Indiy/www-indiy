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
	
	$total_player_views = artist_get_total_views($id);
	
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

    function make_q_html($sql)
    {
        $max_views = 1;
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

    $sql = "SELECT id,name,views FROM mydna_musicplayer_content WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $tab_views_html = make_q_html($sql);
    
    $sql = "SELECT id,name,views FROM mydna_musicplayer_video WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $video_plays_html = make_q_html($sql);

    $sql = "SELECT id,name,views FROM mydna_musicplayer_audio WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $song_plays_html = make_q_html($sql);

    $sql = "SELECT id,name,views FROM photos WHERE artist_id='$id' ORDER BY `order` ASC, `id` DESC";
    $photo_views_html = make_q_html($sql);

	
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