<?

session_start();
//error_reporting(0);
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
                      "name" => $pages['name'],
                      "image" => $image,
                      "content" => $pages['body'],
                      );
        $pages_list[] = $item;
    }
    $pages_list_json = json_encode($pages_list);

    // Build Music
    $loadmusic = mq("SELECT * FROM `[p]musicplayer_audio` WHERE `artistid`='{$artist_id}' AND `type`='0' ORDER BY `order` ASC, `id` DESC");
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
        $music_name = str_replace('"', '&quot;', $music_name);
        $music_artistid = $music["artistid"];
        $music_amazon = nohtml($music["amazon"]);
        $music_itunes = nohtml($music["itunes"]);
        $music_product_id = $music["product_id"];
        $music_download = $music["download"] != "0";
        if( !$music_product_id )
            $music_product_id = FALSE;

        $item = array("id" => $music_id,
                      "name" => $music_name,
                      "mp3" => '/artists/audio/' . $music_audio,
                      "download" => $music_download,
                      "image" => '/artists/images/' . $music_image,
                      "bgcolor" => $music_bgcolor,
                      "bg_style" => $bg_style,
                      "plus" => "",
                      "amazon" => $music_amazon,
                      "itunes" => $music_itunes,
                      "product_id" => $music_product_id,
                      );
        $music_list[] = $item;
    }
    $music_list_json = json_encode($music_list);

    $total_q = mf(mq("SELECT SUM(views) FROM `[p]musicplayer_audio` WHERE `artistid`='{$music_artistid}'"));
    $total_listens = intval($total_q[0]);
    
    $loadvideo = mq("SELECT * from `[p]musicplayer_video` WHERE {$mQuery} ORDER BY `order` ASC, `id` DESC");
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

        $artist_videos .= '"';
        $artist_videos .= " onclick='showVideo($video_id);' ";
        $artist_videos .= '><div class="playlist_video"><img src="artists/images/'.$video_image.'" border="0" /></div></li>'."\n"; // Display video thumb
        ++$cv;
        
        $video_list[$video_id] = array( 'video_file' => '/vid/' . $video['video'],
                                        'name' => $video['name'],
                                        'image_file' => '/artists/images/' . $video['image']);
    }
    if( $artist_videos ) 
        $artist_videos .= '</div>'; // Closes last pagination row, as long as artist has videos

    $video_list_json = json_encode($video_list);


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

<!DOCTYPE html>
<html>
<head>
<title><? echo siteTitle() . ' - ' . $artist_name; ?></title>
<meta name="description" content="MyArtistDNA - <?=$artist_name;?> - Home Page - Come here to connect with your favorite artist."/>

<link href="/css/video-js.css"rel="stylesheet" type="text/css" />
<link href="/css/lionbars.css" rel="stylesheet" type="text/css" />
<link href="/jplayer/style.css" rel="stylesheet" type="text/css" /> 
<link href="/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

<!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script type="text/javascript">

var g_siteUrl = "<?=trueSiteUrl();?>";
var g_videoList = <?=$video_list_json;?>;
var g_totalListens = <?=$total_listens;?>;
var g_artistId = <?=$artist_id;?>;
var g_paypalEmail = "<?=$paypalEmail;?>";
var g_songPlayList = <?=$music_list_json;?>;
var g_currentSongId = 0;
var g_pageList = <?=$pages_list_json;?>;

</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>

<script src="/js/swfobject.js"  type="text/javascript"></script>
<script src="/js/jquery.jplayer.js" type="text/javascript"></script> 

<script src="/js/jquery.easing.1.3.js" type="text/javascript"></script>
<script src="/js/jquery.mousewheel.min.js" type="text/javascript"></script>
<script src="/js/video.js" type="text/javascript"></script> 
<script src="/js/jquery.lionbars.0.3.min.js" type="text/javascript"></script> 

<script src="<?=trueSiteUrl();?>/js/logged_in.php" type="text/javascript"></script>

<script src="/js/artist_home.js" type="text/javascript"></script>
<script src="/js/artist_home_audio.js" type="text/javascript"></script>
<script src="/js/artist_home_video.js" type="text/javascript"></script>
<script src="/js/artist_home_tabs.js" type="text/javascript"></script>
<script src="/js/artist_home_socialize.js" type="text/javascript"></script>

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

var playItem = 0;

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
            playItem = Number(k);
            break;
        }
    }
}

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

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
            echo "<a href='/download.php?artist=$artist_id&id=$song_id' title='Free Song Download'>FREE</a>";
            echo "</div>";
        }
        elseif( $song['amazon'] || $song['itunes'] || $song['product_id'] )
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
        
        <div id='current_song_info'>
            <div class='label'>Artist:</div> 
            <div id='current_track_artist_name' class='value'><?=$artist_name;?></div> 
            <div class='slashes'>//</div>
            <div class='label'>Track:</div>
            <div id='current_track_name' class='value'></div>
            <? if( $show_listens ): ?>
                <div class='slashes'>//</div>
                <div class='label'>Listens:</div>
                <div id='current_track_listens' class='value'><?=$first_track_listens;?></div>
            <? endif; ?>
            <div class='space'></div>
            <div id='buynow_mad_store' class='buynow_icon madna'>
                <a href='#' title='Buy on MyArtistDNA Store'>
                    <img src='/images/buynow_madna.png'/>
                </a>
            </div>
            <div id='buynow_amazon' class='buynow_icon amazon'>
                <a href='#' title='Buy from Amazon'>
                    <img src='/images/buynow_amazon.png'/>
                </a>
            </div>
            <div id='buynow_itunes' class='buynow_icon itunes'>
                <a href='#' title='Download on iTunes'>
                    <img src='/images/buynow_itunes.jpg'/>
                </a>
            </div>
            <div id='buynow_free' class='buynow_text'>
                <a href='#' title='Download for Free'>
                    FREE DOWNLOAD
                </a>
            </div>
        </div>
        <div id='vote_buttons'>
            <button class='vote_up' title='Thumbs Up Song' onclick='songVote(1);'></button>
            <button class='vote_down'  title='Thumbs Down Song' onclick='songVote(0);'></button>
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
        <div id='right_box'>
            <div id="login_signup" class='login_signup'><button onclick='showLogin();'>Log in | Sign Up</button></div>
            <div class='logo_box'>
                <div class='logo_box_top_spacer'></div>
            <? if ($artist_logo) { ?>
                <img class='logo' src="/timthumb.php?src=/artists/images/<?=$artist_logo;?>&q=100&w=145" />
            <? } ?>
                <div class='logo_box_bottom_spacer'></div>
            </div>
            <div class='up_down_arrow' onclick='toggleRightBox();'></div>
        </div>

        <div id='shop_results'></div>
            
        <div id='socialize'>
            <div class="header">
                <div class="title" onclick='toggleSocialTab();'>SOCIALIZE</div>
                <div class='button_holder'>
                    <div class='share button' onclick='toggleSocialShare();'></div>
                </div>
                <div class='button_holder'>
                    <div class='email button' onclick='toggleSocialEmail();'></div>
                </div>
                <div id='socalize_fb_holder' class='button_holder' <?=$hide_tw;?> >
                    <div class='twitter button' onclick='toggleSocialTW();'></div>
                </div>
                <div id='socalize_tw_holder' class='button_holder' <?=$hide_tw;?> >
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
                        <div class='disclaimer'>By clicking on the submit button, you are confirming that you have read and agree with the terms of our <a href="">Privacy Policy</a>.</div>
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
                <div class="sub_title">SEND TO A FRIEND<span class='slashes'>//</span></div>
                <div class='content'>
                    <div id='send_friend_form'>
                        <div class='instructions'>Fill out the form below to send a copy of the message to your friend.</div>
                        <div class='disclaimer'>Please note that your friend will not be subscribed to any email list nor will his / her name or email address be permanently recorded.</div>
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
            
        <div id='image'></div>
        <div id='loader'><img src="/jplayer/images/ajax-loader.gif" /></div>
        <div id='navigation'>
            <ul>
                <?
                    foreach( $pages_list as $i => $page )
                    {
                        $name = $page['name'];
                        echo "<li><a onclick='showUserPage($i);'>$name</a></li>\n";
                    }
                ?>
                <? if ($artist_videos) { ?>
                    <li><a href="#" class="aVideos">Videos</a></li>
                <? } ?>             
                <?=$pagesList;?>
                <? if ($paypalEmail != "") { ?>
                    <li><a href="#" class="aStore">Store</a></li>
                <? } ?>
                <? if ($show_comments) { ?>
                    <li><a href="#" class="aComment">Comment</a></li>
                <? } ?>
                <? if ($artist_email) { ?>
                    <li><a href="#" class="aContact">Contact</a></li>
                <? } ?>
            </ul>
            <div class="clear"></div>
        </div>
        <div id='user_page_wrapper'>
            <div id='user_page'>
                <div class='close'></div>
                <div id='page_title' class='title'></div>
                <div id='page_image_holder' class='image_holder'>
                    <img id='page_image'/>
                </div>
                <div id='page_content' class='page_content'></div>
            </div>
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
                            <li class='jp-controls-to-hide'>
                                <a href="#" id="jplayer_play" class="jp-play" tabindex="1">play</a>
                            </li>
                            <li class='jp-controls-to-hide'>
                                <a href="#" id="jplayer_pause" class="jp-pause" tabindex="1">pause</a>
                            </li>
                            <li class='jp-controls-to-hide'>
                                <a href="#" id="jplayer_stop" class="jp-stop" tabindex="1">stop</a>
                            </li>

                            <li><a href="#" id="jplayer_previous" class="jp-previous vtip" tabindex="1">previous</a></li>
                            <li><a href="#" id="jplayer_next" class="jp-next vtip" tabindex="1">next</a></li>
                        </ul>

                        <div id="jplayer_volume_bar" class="jp-volume-bar">
                            <div id="jplayer_volume_bar_value" class="jp-volume-bar-value"></div>
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
                </div>
            </div>
            
            <div id="volumebg"></div>
            <div id="progressbg"></div>
            
            <div class="space"></div>
            <div class="aClose"></div>
            <div class="page">
                <div class="box-header"></div>
                
                <div id="mcs2_container">
                    <div class="customScrollBox">
                        <div class="container">
                            <div class="content">

                            </div>
                        </div>
                        <div class="dragger_container">
                            <div class="dragger">&#9618;</div>
                        </div>
                    </div>
                </div>
                
                <div class="box-footer"></div>
            </div>
            
            <? if ($show_comments) { ?>
            <div class="comments">
                <div class="box-header"></div>
                <h1>Comment</h1>
                <div id='fb_comments_container'>
                <div id="fb-root"></div>
                    <script>(function(d, s, id) {
                             var js, fjs = d.getElementsByTagName(s)[0];
                             if (d.getElementById(id)) {return;}
                             js = d.createElement(s); js.id = id;
                             js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                             fjs.parentNode.insertBefore(js, fjs);
                             }(document, 'script', 'facebook-jssdk'));
                    </script>
                    <div class="fb-comments" data-href="http://<?=$_SERVER['HTTP_HOST'];?>" data-num-posts="2" data-width="500"></div>
                </div>
                <div class="box-footer"></div>
            </div>
            <? } ?>
            
            <? if ($artist_email) { ?>
            <div class="contact">
                <div class="box-header"></div>
                
                
                <div class="right">
                    <h1>BOOKINGS&nbsp;</h1>
                    <table>
                        <tr>
                            <td><span class="red">*</span> Date of Event:</td>
                            <td><input id="booking_date" type="text" value="" /></td>
                        </tr>
                        <tr>
                            <td><span class="red">*</span> Location:</td>
                            <td><input id="booking_location" type="text" value="" /></td>
                        </tr>
                        <tr>
                            <td><span class="red">*</span> Budget:</td>
                            <td>
                                <select id='booking_budget'>
                                    <option>$0 - $500</option>
                                    <option>$500 - $1,000</option>
                                    <option>$1,000 - $5,000</option>
                                    <option>$5,000 - $10,000</option>
                                    <option>$10,000+</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="message"><span class="red">*</span> Message:</td>
                            <td><textarea name="comments" class="textarea" id="booking_comments"></textarea></td>
                        </tr>
                        <tr>
                            <td><span class="red">*</span> required</td>
                            <td>
                                <button id="contact_submit" onclick="sendBookingForm();">submit</button>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="left">
                    <h1>CONTACT <span class="slashes">//</span> <?=$artist_name;?></h1>
                    <table>
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

            
            <div class="store_Close"></div>
            <div class="contact_Close" onclick='closeContactTab();'></div>
            <? if ($paypalEmail != "") { ?>
            <div class="store">
                <div class="box-header"></div>
                
                <div class="cartnav">
                    <span class="showstore" id='store_tab_link'>Store</span>
                    <span class="showcart" id='store_cart_link'>Cart</span>
                </div>
                
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
            

    <div id='song_buy_popup'>
        <a id='song_buy_popup_mystore' class='store_icon mystore' title='Buy on MyArtistDNA Store'></a>
        <a id='song_buy_popup_amazon' href='#' class='store_icon amazon' title='Buy on Amazon'></a>
        <a id='song_buy_popup_itunes' href='#' class='store_icon itunes' title='Download on iTunes'></a>
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
        <span id='signup_error' class="error" style="display:none">Please fill up all required fields.</span>
        <span id='signup_success' style="display:none">Registration Successfull.</span>

        <article>
        <h5>Create Login</h5>    
         <form>
            <fieldset>
            <ul>
                <li><label>Name</label> <input name="name" id='signup_name' type="text" class="input" value="" /></li>          
                <li><label>Email Address</label> <input name="email" id='signup_email' type="text" class="input" value="" /></li>
                <li><label>Username</label> <input name="username" id='signup_username' type="text" class="input" value="" /></li>
                <li><label>Password</label> <input name="password" id='signup_password' type="password" class="input" value="" /></li>
                <li>
                    <input name="agree" id='signup_agree' type="checkbox" value="agree">
                    <span>I agree to the Terms &amp; Conditions of MyArtistDNA</span>
                </li>
            </ul>
            <div class="button"><a href="#" onclick='onSignupClick();'>Complete Signup</a></div>
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
                <li><label>Password</label> <input id='login_password' name="password" type="password" class="input" value="" /></li>
                </ul>
                <p class="password"><a href="/forgot_password.html">Forgot your password?</a></p>
                <div class="button"><a href="#-1" onclick='onLoginClick();'>LOGIN</a></div>
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
                <div class="buttonsignup"><a href="#" onclick="showSignup();">SIGN UP</a></div>
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
    mCustomScrollbars();
});

function mCustomScrollbars(){
    /* 
    malihu custom scrollbar function parameters: 
    1) scroll type (values: "vertical" or "horizontal")
    2) scroll easing amount (0 for no easing) 
    3) scroll easing type 
    4) extra bottom scrolling space for vertical scroll type only (minimum value: 1)
    5) scrollbar height/width adjustment (values: "auto" or "fixed")
    6) mouse-wheel support (values: "yes" or "no")
    7) scrolling via buttons support (values: "yes" or "no")
    8) buttons scrolling speed (values: 1-20, 1 being the slowest)
    */
    $("#mcs2_container").mCustomScrollbar("vertical",0,"easeOutCirc",1.05,"auto","yes","no",0); 
}

/* function to fix the -10000 pixel limit of jquery.animate */
$.fx.prototype.cur = function(){
    if ( this.elem[this.prop] != null && (!this.elem.style || this.elem.style[this.prop] == null) ) {
      return this.elem[ this.prop ];
    }
    var r = parseFloat( jQuery.css( this.elem, this.prop ) );
    return typeof r == 'undefined' ? 0 : r;
}

/* function to load new content dynamically */
function LoadNewContent(id,file){
    $("#"+id+" .customScrollBox .content").load(file,function(){
        mCustomScrollbars();
    });
}

</script>
<script src="js/jquery.mCustomScrollbar.js" type="text/javascript"></script>
<!-- Custom scrollbar Ends -->

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
</body>
</html>
<? } ?>