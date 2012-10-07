<?php

    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    $list_html = "";
    
    $artists_q = mq("SELECT * FROM mydna_musicplayer WHERE preview_key = '' LIMIT 300");
    while( $artist = mf($artists_q) )
    {
        $logo = $artist['logo'];
        $logo_path = "artists/files/$logo";
        
        if( !$logo || !file_exists($logo_path) )
        {
            $logo_path = "manage/images/NoPhoto.jpg";
        }
        
        $artist_url = $artist['url'];
        
        $url = str_replace("http://www.","http://$artist_url.",trueSiteUrl());
        
        $name = $artist['artist'];
        
        $html = "";
        $html .= "<a href='$url'>";
        $html .= " <li>";
        $html .= "  <img src='/$logo_path' />";
        $html .= "  <span>$name</span>";
        $html .= " </li>";
        $html .= "</a>";
        
        $list_html .= $html;
    }

    include_once "header.php";
?>

<section id="wrapper">
<section id="content">
	
	<div id="artists">
        <div class="heading">
            <h2>ALL ARTISTS</h2>
        </div>
	
        <div id="artistshome">
            <ul id="list" class="image-grid">
                <?=$list_html;?>
            </ul>
        </div>
    </div>
    
    <div class="signup">
        <h3>Be heard, be seen, Get started now!</h3>
        <div class="button"><a href="#" onclick="showSignup();">SIGN UP NOW</a></div>
    </div>

</section>
</section>

<?php include_once "footer.php"; ?>
