<?

if( !$_SESSION["cart"] )
{
    $_SESSION["cart"] = rand(1111111,9999999);
}

if( strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") !== FALSE
   || strpos($_SERVER['HTTP_USER_AGENT'],"Googlebot-Mobile") !== FALSE
   )
{
    $browser = 'iphone';
    include("iphone.php");
} 
else if( $_GET["embed"] == "true" )
{
    include("iphone.php");
} 
else 
{
    $meta_keywords = '';

    $row = mf(mq("SELECT * FROM `[p]musicplayer` WHERE `url`='$artist_url' LIMIT 1"));
    if( $row == FALSE )
    {
        header("HTTP/1.0 404 Not Found");
        die();
    }
    if ($row["type"] == "1") 
    {
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
    $artist_listens = $row["listens"];
    $show_comments = TRUE;
    append_tags($meta_keywords,$row["tags"]);
    
    $facebook_page = $row['fb_page_url'];
    
    $hide_fb = '';
    if( $row['fb_setting'] == 'DISABLED' )
        $hide_fb = 'style="display: none;"';
    if( $facebook_page == '' )
        $hide_fb = 'style="display: none;"';
    
    $hide_tw = '';
    if( $row['tw_setting'] == 'DISABLED' )
        $hide_tw = 'style="display: none;"';
    if( $row['twitter'] == '' )
        $hide_tw = 'style="display: none;"';
    
    if ($artist_listens == "1") { $show_listens = "true"; }

    playerViews($artist_id);

    // Build Pages
    $loadpages = mq("SELECT * FROM `[p]musicplayer_content` WHERE `artistid`='{$artist_id}' ORDER BY `order` ASC, `id` DESC");
    $pages_list = array();
    while( $pages = mf($loadpages) )
    {
        $image = FALSE;
        if( $pages['image'] != '' )
        {
            $image = '/artists/images/' . $pages['image'];
        }
        $item = array("id" => $pages['id'],
                      "title" => $pages['name'],
                      "image" => $image,
                      "content" => $pages['body'],
                      );
        $pages_list[] = $item;
    }
    $pages_list_json = json_encode($pages_list);


    $media_url = FALSE;
    // Build Music
    $loadmusic = mq("SELECT * FROM `[p]musicplayer_audio` WHERE `artistid`='{$artist_id}' ORDER BY `order` ASC, `id` DESC");
    $music_list = array();
    while ($music = mf($loadmusic)) 
    {
        if( !isset($first_track_listens))
            $first_track_listens = $music['views'];
        $music_id = $music["id"];
        $music_listens = $music["views"];
        $music_audio = $music["audio"];
        $music_image = $music["image"];
        $music_bgcolor = $music["bgcolor"];
        $bg_style = $music["bg_style"];
        $music_name = stripslashes($music["name"]);
        $music_artistid = $music["artistid"];
        $music_amazon = nohtml($music["amazon"]);
        $music_itunes = nohtml($music["itunes"]);
        $music_product_id = $music["product_id"];
        $music_download = $music["download"] != "0";
        if( !$music_product_id )
            $music_product_id = FALSE;
        
        append_tags($meta_keywords,$music["tags"]);
        
        $item = array("id" => $music_id,
                      "name" => $music_name,
                      "mp3" => '/artists/files/' . $music_audio,
                      "download" => $music_download,
                      "image" => '/artists/files/' . $music_image,
                      "bgcolor" => $music_bgcolor,
                      "bg_style" => $bg_style,
                      "plus" => "",
                      "amazon" => $music_amazon,
                      "itunes" => $music_itunes,
                      "product_id" => $music_product_id,
                      "loaded" => FALSE,
                      "listens" => $music_listens,
                      "image_data" => json_decode($music['image_data']),
                      );
        $music_list[] = $item;
        
        if( !$media_url )
            $media_url = "http://" . $_SERVER['HTTP_HOST'] . '/artists/images/' . $music_image;
    }
    $music_list_json = json_encode($music_list);

    $total_q = mf(mq("SELECT SUM(views) FROM `[p]musicplayer_audio` WHERE `artistid`='{$music_artistid}'"));
    $total_listens = intval($total_q[0]);
    
    $loadvideo = mq("SELECT * from `[p]musicplayer_video` WHERE `artistid`='$artist_id' AND LENGTH(video) > 0 ORDER BY `order` ASC, `id` DESC");
    $cv = 0;
    /* Video Overlay Pagination Code Begins */
    $row_counter = 0; // Counts the number of video pages left to right
    $videos_per_row = 3;
    while ($video = mf($loadvideo)) 
    {
        $vid_error = $video["error"];
        if( strlen($vid_error) > 0 )
            continue;
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

        $artist_videos .= '"';
        $artist_videos .= " onclick='showVideo($video_id);' ";
        $artist_videos .= '><div class="playlist_video"><img src="artists/images/'.$video_image.'" border="0" /></div></li>'."\n"; // Display video thumb
        ++$cv;
        
        append_tags($meta_keywords,$video["tags"]);
        
        $video_list[$video_id] = array( 'id' => $video[id],
                                        'video_file' => trueSiteUrl() . '/vid/' . $video['video'],
                                        'name' => $video['name'],
                                        'image_file' => trueSiteUrl() . '/artists/images/' . $video['image']);
    }
    if( $artist_videos ) 
        $artist_videos .= '</div>'; // Closes last pagination row, as long as artist has videos

    $video_list_json = json_encode($video_list);


    // Build Store
    $check = mf(mq("SELECT * FROM `[p]musicplayer_ecommerce` WHERE `userid`='{$artist_id}' LIMIT 1"));
    $paypalEmail = $check["paypal"];
    $store_enabled = $paypalEmail != '';
    $product_list = array();
    if( $store_enabled )
    {
        $loadpro = mq("select * from `[p]musicplayer_ecommerce_products` where `artistid`='{$artist_id}' order by `order` asc, `id` desc");
        while( $pro = mf($loadpro) ) 
        {
            $sizes = FALSE;
            if( $pro["size"] != "" )
                $sizes = explode(",", $pro["size"]);

            $colors = FALSE;
            if ($pro["color"] != "")
                $colors = explode(",", $pro["color"]);
            
            $product_image = $pro["image"];
            $image = '/images/default_product_image.jpg';
            if( $product_image != "" ) 
            {
                $path = "artists/products/$product_image";
                if( file_exists("$path") )
                    $image = "/$path";
            }
            $item = array("id" => $pro['id'],
                          "image" => $image,
                          "name" => stripslashes($pro['name']),
                          "description" => $product_desc = nohtml($pro['description']),
                          "price" => $pro['price'],
                          "sizes" => $sizes,
                          "colors" => $colors,
                          );
            $product_list[] = $item;
        }
    }
    $need_product_page_arrows = FALSE;
    if( count($product_list) > 3 )
        $need_product_page_arrows = TRUE;
    $product_list_json = json_encode($product_list);

    // Function that outputs the video overlay pagination buttons, dependant on the global $row_counter variable
    function write_row_buttons() {
        global $row_counter;
        echo "<div class='row-buttons'>";
        for( $i=1; $i <= $row_counter; $i++ ) {
            echo "<div class='row-button row-button-$i'><span>$i</span></div>";
        }
        echo "</div>";
    }
    
    $page_url = "http://" . $_SERVER['HTTP_HOST'];
    
    $page_viewer_email = '';
    if( isset($_COOKIE['PAGE_VIEWER_EMAIL']) )
    {
        $page_viewer_email = $_COOKIE['PAGE_VIEWER_EMAIL'];
    }

    
?>

<!DOCTYPE html>
<html>
<head>
<title><? echo siteTitle() . ' - ' . $artist_name; ?></title>
<meta name="description" content="MyArtistDNA - <?=$artist_name;?> - Home Page - Come here to connect with your favorite artist.">
<meta name="keywords" content="<?=$meta_keywords;?>">

<link href="/css/video-js.css"rel="stylesheet" type="text/css" />
<link href="/css/lionbars.css" rel="stylesheet" type="text/css" />
<link href="/jplayer/style.css" rel="stylesheet" type="text/css" /> 

<? if( strstr($_SERVER['HTTP_USER_AGENT'],'iPad') !== FALSE): ?>
    <link href="/jplayer/ipad.css" rel="stylesheet" type="text/css" />
<? endif; ?>

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

<!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script type="text/javascript">

var g_siteUrl = "<?=trueSiteUrl();?>";
var g_videoList = <?=$video_list_json;?>;
var g_totalListens = <?=$total_listens;?>;
var g_artistId = <?=$artist_id;?>;
var g_artistName = "<?=$artist_name;?>";
var g_paypalEmail = "<?=$paypalEmail;?>";
var g_songPlayList = <?=$music_list_json;?>;
var g_currentSongId = 0;
var g_pageList = <?=$pages_list_json;?>;
var g_productList = <?=$product_list_json;?>;
var g_pageViewerEmail = "<?=$page_viewer_email;?>";

</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>
<script src="/js/string.utils.js"  type="text/javascript"></script>

<script src="/js/jquery.jplayer.js" type="text/javascript"></script> 

<script src="/js/jquery.easing.1.3.js" type="text/javascript"></script>
<script src="/js/jquery.mousewheel.min.js" type="text/javascript"></script>
<script src="/js/video.js" type="text/javascript"></script> 
<script src="/js/jquery.lionbars.0.3.min.js" type="text/javascript"></script>
<script src="/js/swipe.js" type="text/javascript"></script>

<script src="<?=trueSiteUrl();?>/js/logged_in.php" type="text/javascript"></script>

<script src="/js/artist_home_audio.js" type="text/javascript"></script>
<script src="/js/artist_home_video.js" type="text/javascript"></script>
<script src="/js/artist_home_tabs.js" type="text/javascript"></script>
<script src="/js/artist_home_socialize.js" type="text/javascript"></script>
<script src="/js/artist_home_store.js" type="text/javascript"></script>

<script src="/js/login_signup.js" type="text/javascript"></script>

<script src="/jplayer/js/ra_controls.js" type="text/javascript"></script>
<script src="/jplayer/js/index.js" type="text/javascript"></script>

<script type="text/javascript">
   
function clickclear(thisfield, defaulttext) 
{
    if( thisfield.value == defaulttext ) 
    {
        thisfield.value = "";
    }
}   
function clickrecall(thisfield, defaulttext) 
{
    if( thisfield.value == "" )
    {
        thisfield.value = defaulttext;
    }
}       

var g_currentSongIndex = 0;

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
    for( var k in g_songPlayList )
    {
        var song = g_songPlayList[k];
        if( song['id'] == song_id )
        {
            g_currentSongIndex = Number(k);
            break;
        }
    }
}

var g_currentVideoId = false;
if( 'video_id' in g_anchor_map )
{
    var video_id = g_anchor_map['video_id'];
    g_currentVideoId = video_id;
}


</script> 
    </head>
    <body>
        <div id='playlist'>
            <div class='song_list'>
<?
    $first = ' first';
    $num = count($music_list);
    foreach( $music_list as $i => $song )
    {
        $song_name = $song['name'];
        $song_id = $song['id'];
        if( $i == $num -1 )
            $last = ' last';
        echo "<div id='song_list_item_$song_id' class='song_list_item$first$last'>";
        $first = '';
        if( $song['download'] )
        {
            echo "<div class='song_name_free' onclick='changeSong($i);'>";
            echo $song_name;
            echo "</div>";
            echo "<div class='song_free'>";
            echo "<a onclick='freeSongDownload($i);' title='Free Song Download'>FREE</a>";
            echo "</div>";
        }
        elseif( $song['amazon'] || $song['itunes'] || ( $song['product_id'] && $store_enabled ) )
        {
            echo "<div class='song_name_store' onclick='changeSong($i);'>";
            echo $song_name;
            echo "</div>";
            echo "<div class='song_store'>";
            echo "<a id='song_buy_icon_$i' title='Purchase Song' onclick='songBuyPopup($i);'>";
            echo "BUY";
            echo "</a>";
            echo "</div>";
        }
        else
        {
            echo "<div class='song_name_only' onclick='changeSong($i);'>";
            echo $song_name;
            echo "</div>";
        }
        echo "</div>\n";
    }
?>
            </div>
            <div class='scroll_label_bar'>
                <div class='scroll_up' onclick='playlistScrollUp();'></div>
                <div class='scroll_down' onclick='playlistScrollDown();'></div>
                <div class='playlist_label' onclick='togglePlaylistVisibility();'></div>
            </div>
        </div>
        <div id='vote_results'></div>
        <div id='video_player'>
            <div class='top_bar'>
                <div class='logo_title_artist'>
                    <div class='logo'>
                        <img src='/artists/images/<?=$artist_logo;?>'/>
                    </div>
                    <div class='title_artist'>
                        <div id='video_title' class='title'></div>
                        <div class='artist_label'>Artist:</div>
                        <div class='artist'><?=$artist_name;?></div>
                    </div>
                </div>
                <div class='close_button' onclick='closeVideo();'>
                    <div class='close_text'>CLOSE</div>
                    <div class='close_img'></div>
                </div>
            </div>
            <div id='player_body' class='player_body'></div>
        </div>
        

        <div id='shop_results'></div>
        
        <div id='social_container'>
        <div id='socialize'>
            <div class="header">
                <div class="title" onclick='toggleSocialTab();'>SOCIALIZE</div>
                <div class='button_holder'>
                    <div class='share button' onclick='toggleSocialShare();'></div>
                </div>
                <div class='button_holder'>
                    <div class='email button' onclick='toggleSocialEmail();'></div>
                </div>
                <div id='socalize_tw_holder' class='button_holder' <?=$hide_tw;?> >
                    <div class='twitter button' onclick='toggleSocialTW();'></div>
                </div>
                <div id='socalize_fb_holder' class='button_holder' <?=$hide_fb;?> >
                    <div class='facebook button' onclick='toggleSocialFB();'></div>
                </div>
            </div>
            
            <div id='facebook' class="tab">
                <div class="sub_title">FACEBOOK<span class='slashes'>//</span></div>
                <div class='content'>
                    <? if( !$hide_fb ) : ?>
                        <iframe src="http://www.facebook.com/plugins/likebox.php?href=<?=urlencode($facebook_page);?>&width=273&colorscheme=dark&show_faces=false&stream=true&header=false&height=300" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height: 320px;" allowTransparency="true"></iframe>
                    <? endif; ?>
                </div>
            </div>
                
            <div id='twitter' class="tab">
                <div class="sub_title">TWITTER<span class='slashes'>//</span></div>
                <div class='content'>
                    <? if( !$hide_tw ) : ?>
                        <script src="http://widgets.twimg.com/j/2/widget.js" type="text/javascript"></script>
                        <script type="text/javascript">
                        new TWTR.Widget(
                        {
                            version: 2,
                            type: 'profile',
                            rpp: 5,
                            interval: 6000,
                            width: 273,
                            height: 226,
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
                    <? endif; ?>
                </div>
            </div>
            
            <div id='email' class="tab">
                <div class="sub_title">MAILING LIST<span class='slashes'>//</span></div>
                <div class='content'>
                    <div id='news_success' class='news_success' style="display:none;">
                        Thank you for your submission.  Your name will be added to our newsletter list.
                    </div>
                    <div id='news_form'>
                        <div class='instructions'>
                            If you would like to keep right up to date with all the latest news, gigs, releases and competitions, then sign up to our mailing list.
                        </div>
                        <div class='disclaimer'>By clicking on the submit button, you are confirming that you have read and agree with the terms of our <a href="privacy.php">Privacy Policy</a>.</div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Name:</div>
                            <div class='input_container'>
                                <input id='news_name' />
                            </div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Email:</div>
                            <div class='input_container'>
                                <input id='news_email' />
                            </div>
                        </div>
                        <div class='submit_required'>
                            <button class='submit red' onclick='submitNewsletter();'>submit</button>
                            <div class='required_label'><span class='required'>*</span>required</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id='share' class="tab">
                <div class="sub_title">SHARE AROUND THE WEB<span class='slashes'>//</span></div>
                <div class='content'>
                    <div class='like_button_container'>
                        <div class='like_button facebook'>
                            <div class="fb-like" data-href="<?=$page_url;?>" data-send="false" data-layout="button_count" data-width="46" data-show-faces="false" data-font="lucida grande"></div>
                        </div>
                        <div class='like_button twitter'>
                            <a href="https://twitter.com/share" class="twitter-share-button" data-via="myartistdna" data-hashtags="myartistdna">Tweet</a>
                        </div>
                    </div>
                    <div class='like_button_container'>
                        <div class='like_button google'>
                            <div class="g-plusone" data-size="medium" data-href="<?=$page_url;?>"></div>
                        </div>
                        <div class='like_button pinterest'>
                            <a href="http://pinterest.com/pin/create/button/?url=<?=urlencode($page_url);?>&media=<?=urlencode($media_url);?>" class="pin-it-button" count-layout="horizontal">Pin It</a>
                        </div>
                    </div>
                
                    <div id='send_friend_form'>
                        <div class='instructions'>Fill out the form below to send a copy of the message to your friend.</div>
                        <!--
                        <div class='disclaimer'>Please note that your friend will not be subscribed to any email list nor will his / her name or email address be permanently recorded.</div>
                        -->
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>To:</div>
                            <div class='input_container'>
                                <input id='send_friend_to' />
                            </div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>From:</div>
                            <div class='input_container'>
                                <input id='send_friend_from' />
                            </div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Message:</div>
                            <div class='input_container'>
                                <textarea id='send_friend_message'></textarea>
                            </div>
                        </div>
                        <div class='submit_required'>
                            <button class='submit green' onclick='sendToFriend();'>send</button>
                            <div class='required_label'><span class='required'>*</span>required</div>
                        </div>
                    </div>
                    <div id='send_friend_success' class='send_friend_success' style="display:none;">
                        Your friend will be notified about this great artist!
                    </div>
                </div>
            </div>
        </div>
        </div>
            
        <div id='image'>
            <div id='image_slider'>
                <div class='container'>
                    <?
                        foreach( $music_list as $k => $song )
                        {
                            echo "<div id='image_holder_$k' class='image_holder'>";
                            echo "</div>\n";
                        }
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <div id='loader'><img src="/jplayer/images/ajax-loader.gif" /></div>
        
        
        <div id='user_page_wrapper'>
            <div id='user_page'>
                <div class='close' onclick='closeUserPage();'></div>
                <div class='scrollable_container'>
                    <div id='page_title' class='title'></div>
                    <div id='page_image_holder' class='image_holder'>
                        <img id='page_image'/>
                    </div>
                    <div id='page_content' class='page_content'></div>
                </div>
            </div>
        </div>
        
        <div id='download_email_wrapper'>
            <div id='download_email_popup'>
                <div class='close' onclick='hideDownloadEmailPopup();'></div>
                <div class='description'>Please enter your email address to download this file:</div>
                <div class='label_input'>
                    <div class='label'>Email:</div>
                    <div class='input_container'>
                        <input>
                    </div>
                </div>
                <div class='submit_container'>
                    <button onclick='submitDownloadEmailPopup();'>submit</button>
                </div>
            </div>
        </div>

        <div id='viewer_email_wrapper'>
            <div id='viewer_email_popup'>
                <div class='close' onclick='hideViewerEmailPopup();'></div>
                <div class='request_form'>
                    <div class='description'>Would you like to recieve updates from this artist?</div>
                    <div class='label_input'>
                        <div class='label'>Email:</div>
                        <div class='input_container'>
                            <input>
                        </div>
                    </div>
                    <div class='submit_nothanks'>
                        <div class='submit_container'>
                            <button onclick='submitViewerEmailPopup();'>submit</button>
                        </div>
                        <div class='nothanks_container'>
                            <button onclick='hideViewerEmailPopup();'>no thanks</button>
                        </div>
                    </div>
                </div>
                <div class='thanks'>
                    <div class='description'>Thank you for your email address.</div>
                    <div class='close_container'>
                        <button onclick='hideViewerEmailPopup();'>close</button>
                    </div>
                </div>
            </div>
        </div>

        
        <? if( $store_enabled ): ?>
            <div id='store_wrapper'>
                <div id='store'>
                    <div class='close' onclick='closeStore();'></div>
                    <div class='cart_nav'>
                        <button onclick='showProducts();'>Store</button>
                        <button onclick='showCart();'>Cart</button>
                    </div>
                    <div id='store_products'>
                        <?
                            if( $need_product_page_arrows )
                            {
                                echo "<div class='scroll_button right' onclick='scrollStoreRight();'></div>\n";
                                echo "<div class='scroll_button left' onclick='scrollStoreLeft();'></div>\n";
                            }
                        ?>
                        <div class='product_list'>
                            <? 
                                //echo "<ul id='product_slider_ul' class='product_slider'>\n";
                                foreach( $product_list as $i => $product )
                                {
                                    $name = $product['name'];
                                    $image = $product['image'];
                                    $description = $product['description'];
                                    $price = $product['price'];
                                    //echo "<li>";
                                    echo " <div class='product'>";
                                    echo "  <div class='image_holder'>";
                                    echo "   <img src='$image'>";
                                    echo "  </div>";
                                    echo "  <div class='name'>$name</div>";
                                    echo "  <div class='description'>$description</div>";
                                    echo "  <div class='price_cart'>";
                                    echo "   <div class='price'>\$$price</div>";
                                    echo "   <div class='add_to_card' onclick='addToCart($i);'>Buy Now</div>";
                                    echo "  </div>";
                                    echo " </div>";
                                    //echo "</li>\n";
                                }
                            ?>
                            </ul>
                        </div>
                    </div>
                    <div id='store_cart' class='cart'>
                        <div id='store_cart_form_holder'></div>
                        <div class='header'>
                            <div class='image_holder'>Image</div>
                            <div class='name'>Name</div>
                            <div class='price'>Price</div>
                            <div class='remove_holder'>Remove</div>
                        </div>
                        <div id='store_cart_body' class='body'>
                        </div>
                    </div>
                </div>
            </div>
        <? endif; ?>
        <? if( $show_comments ): ?>
            <div id='comments_wrapper'>
                <div id='comments'>
                    <div class='close' onclick='closeComments();'></div>
                    <div class='title'>COMMENT</div>
                    <div class='scrollable_container'>
                        <div id="fb-root"></div>
                        
                        <div class="fb-comments" data-href="http://<?=$_SERVER['HTTP_HOST'];?>" data-num-posts="2" data-width="500"></div>
                    </div>
                </div>
            </div>
        <? endif; ?>
        <? if( $artist_email ): ?>
            <div id='contact_wrapper'>
                <div id="contact">
                    <div class='close' onclick='closeContactTab();'></div>
                    
                    <div class="contact">
                        <div class='title'>CONTACT</div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Name:</div>
                            <div class='input_container'>
                                <input id='contact_name' />
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Email:</div>
                            <div class='input_container'>
                                <input id='contact_email' />
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Message:</div>
                            <div class='input_container'>
                                <textarea id='contact_message'></textarea>
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='submit_required'>
                            <button class='submit green' onclick='sendContactForm();'>send</button>
                            <div class='required_label'><span class='required'>*</span>required</div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='booking_link'>Are you interested in booking this artist? Click <a onclick='showBookingsForm();'>here</a>.</div>
                    </div>
                    
                    <div class="bookings">
                        <div class='title'>BOOKINGS</div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Name:</div>
                            <div class='input_container'>
                                <input id='booking_name' />
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Email:</div>
                            <div class='input_container'>
                                <input id='booking_email' />
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Event Date:</div>
                            <div class='input_container'>
                                <input id='booking_date' />
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Location:</div>
                            <div class='input_container'>
                                <input id='booking_location' />
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Budget:</div>
                            <div class='input_container'>
                                <select id='booking_budget'>
                                        <option>$0 - $500</option>
                                        <option>$500 - $1,000</option>
                                        <option>$1,000 - $5,000</option>
                                        <option>$5,000 - $10,000</option>
                                        <option>$10,000+</option>
                                </select>
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='label_input'>
                            <div class='label'><span class='required'>*</span>Message:</div>
                            <div class='input_container'>
                                <textarea id='booking_message'></textarea>
                            </div>
                            <div style='clear: both'></div>
                        </div>
                        <div class='submit_required'>
                            <button class='submit green' onclick='sendBookingForm();'>send</button>
                            <div class='required_label'><span class='required'>*</span>required</div>
                            <div style='clear: both'></div>
                        </div>
                    </div>
                    
                </div>
            </div>
        <? endif; ?>

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
        
        <div class="aClose"></div>


        <div id='nav_right_box'>
            <div id='navigation'>
                <div class='tab_bar_container'>
                    <div class='tab_bar'>
                        <div class='tab_spacer'></div>
                        <?
                            foreach( $pages_list as $i => $page )
                            {
                                $title = $page['title'];
                                echo "<div class='tab' onclick='showUserPage($i);'>$title</div>\n";
                            }
                        ?>
                        <? if( $artist_videos ): ?>
                            <div class='tab' onclick='showVideos();'>Videos</div>
                        <? endif; ?>             
                        <?=$pagesList;?>
                        <? if( $store_enabled != "" ): ?>
                            <div class='tab' onclick='showStore();'>Store</div>
                        <? endif; ?>
                        <? if( $show_comments ): ?>
                            <div class='tab' onclick='showComments();'>Comment</div>
                        <? endif; ?>
                        <? if( $artist_email ): ?>
                            <div class='tab' onclick='showContact();'>Contact</div>
                        <? endif; ?>
                        <div class='tab_spacer'></div>
                    </div>
                </div>
                <div class='artist_name_holder'>
                    <div class='artist_name' onclick='toggleNavigation();'><?=$artist_name;?></div>
                </div>
            </div>
            <div id='right_box'>
                <div id="login_signup" class='login_signup'>
                    <button onclick='showSignup();'>SIGN UP</button>
                     | 
                    <button onclick='showLogin();'>LOG IN</button>
                </div>
                <div id='back_to_admin' class='back_to_admin'>
                    <a href='<?=trueSiteUrl();?>/manage/artist_management.php'>MY PROFILE</a>
                </div>
                <div id='buynow_free' class='buynow_free'>
                    <div>
                        <a onclick='freeSongDownload(false);' title='Download for Free'>Free Download</a>
                    </div>
                </div>
                <div class='expand_box'>
                    <div class='login_sep'></div>
                    <div class='label_name'>
                        Title: <span id='current_track_name'></span>
                    </div>
                    <? if( $show_listens ): ?>
                        <div class='label_name'>
                            Views:
                            <span id='current_track_listens'><?=$first_track_listens;?></span>
                        </div>
                    <? endif; ?>
                    <div class='vote_buttons'>
                        <button class='vote_up' title='Thumbs Up Song' onclick='songVote(1);'></button>
                        <button class='vote_down'  title='Thumbs Down Song' onclick='songVote(0);'></button>
                    </div>
                    
                    <div id='buynow_mad_store' class='buynow_mad_store'>
                        <div>
                            <a title='Buy on MyArtistDNA Store'>+ Add to Cart</a>
                        </div>
                    </div>
                    <div class='amazon_itunes_buttons'>
                        <div id='buynow_amazon'>
                            <a  title='Buy from Amazon' target='_blank'>
                                <img src='/images/buynow_amazon.png'/>
                            </a>
                        </div>
                        <div id='buynow_itunes'>
                            <a title='Download on iTunes' target='_blank'>
                                <img src='/images/buynow_itunes.jpg'/>
                            </a>
                        </div>
                    </div>
                    <? if ($artist_logo): ?>
                        <div class='logo_container'>
                            <img class='logo' src="/timthumb.php?src=/artists/images/<?=$artist_logo;?>&q=100&w=145" />
                        </div>
                    <? endif; ?>
                    <? if( $show_listens ): ?>
                        <div class='total_listens'>
                            TOTAL VIEWS:
                            <span id='total_listens_val'><?=$total_listens;?></span>
                        </div>
                    <? endif; ?>
                </div>
                <div class='up_down_arrow' onclick='toggleRightBox();'></div>
            </div>
        </div>

        
        <div id="jquery_jplayer" class="jp-jplayer"></div>

        <div id='player'>
            <div class='album'>
                <img src='/timthumb.php?src=/artists/images/<?=$artist_logo;?>&q=100&w=50'></img>
            </div>
            <div class='seperator'></div>
            <div class='volume_icon'></div>
            <div class='volume'>
                <div class='current'></div>
            </div>
            <div class='seperator'></div>
            <div class='prev_track' onclick='playListPrev();'></div>
            <div class='play_pause' onclick='playerPlayPause();'></div>
            <div class='next_track' onclick='playListNext();'></div>
            <div class='seperator'></div>
            <div class='song_track'>
                <div class='artist_song'></div>
                <div class='seek_time'>
                    <div class='seek_bar'>
                        <div class='loaded'></div>
                        <div class='current'></div>
                    </div>
                    <div class='time'>0:00/0:00</div>
                </div>
            </div>
            <div class='open_close_button' onclick='playerToggle();'></div>
        </div>
        
        <div id='prev_track' onclick='playListPrev();'></div>
        <div id='next_track' onclick='playListNext();'></div>
            
        <div class="footerfade">
            <div class="logo_img"><a href="<?=trueSiteUrl();?>"></a></div>
            <div id="theSearchBox"></div>
        </div>

        <div id='song_buy_popup'>
            <a id='song_buy_popup_mystore' class='store_icon mystore' title='Buy on MyArtistDNA Store'></a>
            <a id='song_buy_popup_amazon' href='#' class='store_icon amazon' title='Buy on Amazon'></a>
            <a id='song_buy_popup_itunes' href='#' class='store_icon itunes' title='Download on iTunes'></a>
        </div>

        <? include_once "includes/login_signup.html"; ?>

        <!-- Mask to cover the whole screen --> 
        <div id="mask"></div> 
        </div>

<!-- Tracking code Starts --> 
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-15194524-1']);
  _gaq.push(['_setDomainName', '.myartistdna.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!-- Tracking code Ends -->
<script>
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=120524971363318";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<script>
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
</script>

<!-- Place this render call where appropriate -->
<script type="text/javascript">
(function() {
 var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
 po.src = 'https://apis.google.com/js/plusone.js';
 var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
 })();
</script>
<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>

</body>
</html>
<? } ?>