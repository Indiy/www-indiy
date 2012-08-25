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

    function make_q_html($sql,$class='')
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
                $html .= "<div class='graph_row $class'>";
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
            $html .= "<div class='graph_row $class'>";
            $html .= $frag_html;
            $html .= "</div>";
            
            $video_plays_html .= $html;
            $frag_html = "";
        }
        return $video_plays_html;
    }

    $sql = "SELECT id,name,views FROM mydna_musicplayer_content WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $tab_views_html = make_q_html($sql,'yellow');
    
    $sql = "SELECT id,name,views FROM mydna_musicplayer_audio WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $song_plays_html = make_q_html($sql);

    $sql = "SELECT id,name,views FROM mydna_musicplayer_video WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $video_plays_html = make_q_html($sql);

    $sql = "SELECT id,name,views FROM photos WHERE artist_id='$id' ORDER BY `order` ASC, `id` DESC";
    $photo_views_html = make_q_html($sql);

    $sql = "SELECT id,name,loves AS views FROM mydna_musicplayer_audio WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $song_loves_html = make_q_html($sql);

    $sql = "SELECT id,name,loves AS views FROM mydna_musicplayer_video WHERE artistid='$id' ORDER BY `order` ASC, `id` DESC";
    $video_loves_html = make_q_html($sql);

    $sql = "SELECT id,name,loves AS views FROM photos WHERE artist_id='$id' ORDER BY `order` ASC, `id` DESC";
    $photo_loves_html = make_q_html($sql);

    $sql = "SELECT id,name,download AS views FROM mydna_musicplayer_audio WHERE artistid='$id' AND download > 0 ORDER BY `order` ASC, `id` DESC";
    $song_downloads_html = make_q_html($sql);


    $include_order = FALSE;
    $include_editor = FALSE;
    $include_stats = TRUE;

    $artist_edit_url = "/manage/artist_management.php?userId=$artist_id";

	include_once "templates/stats.html";

?>