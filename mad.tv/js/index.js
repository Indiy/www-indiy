
var g_currentVideoIndex = 0;

$(document).ready(setupVideoPlayer)

function setupVideoPlayer()
{
    //showVideo(0);
    $(window).resize(onWindowResize);
    
    //$(document).mousemove(showControls);
    //showControls();
    $("#overlay_container").fadeIn();
}
var g_controlsShown = false;
var g_hideControlsTimeout = false;
function showControls()
{
    if( !g_controlsShown )
    {
        g_controlsShown = true;
        $("#overlay_container").fadeIn();
    }
    if( g_hideControlsTimeout !== false )
    {
        window.clearTimeout(g_hideControlsTimeout);
        g_hideControlsTimeout = false;
    }
    g_hideControlsTimeout = window.setTimeout(hideControls,2000);
}
function hideControls()
{
    g_controlsShown = false;
    $("#overlay_container").fadeOut();
}

var g_playing = true;
function playerToggle()
{
    if( g_playing )
        playerPause();
    else
        playerPlay();
}
function playerPlay()
{
    g_playing = true;
    try 
    {
        g_videoPlayer.start();
    }
    catch(e) {}
    //g_intervalUpdateTrack = window.setInterval(updateTrackInfo,200);
    $('#player .play').removeClass('paused');
}
function playerPause()
{
    g_playing = false;
    try 
    {
        g_videoPlayer.stop();
    }
    catch(e) {}
    //window.clearInterval(g_intervalUpdateTrack);
    $('#player .play').addClass('paused');
}
var g_historyShown = false;
function toggleHistory()
{
    if( g_historyShown )
        hideHistory();
    else
        showHistory();
}
function showHistory()
{
    //hideGenrePicker();
    g_historyShown = true;
    $('#history').fadeIn();
    updateHistory();
}
function hideHistory()
{
    g_historyShown = false;
    $('#history').fadeOut();
}
function updateHistory()
{
    
}
function showAddVideo()
{
    //hideGenrePicker();
    $('#add_video').fadeIn();
}
function hideAddVideo()
{
    $('#add_video').fadeOut();
}



var g_videoPlayer = false;
function showVideo(n)
{
    g_currentVideoIndex = n;

    var h = $('#video_container').height();
    var w = $('#video_container').width();

    /*
    var video = g_videoList[n];
    var video_file = video.video_file;
    var video_file_ogv = video_file.replace(".mp4",".ogv");
    var poster = video.poster;

    $('#video_title').text(video.name);
    $('#artist_name').text(video.artist);
    $('#logo_img').attr('src',video.logo);
    */
    
    
    var w_h = " width='" + w + "' height='" + h + "' ";
    
    var html = '';
    html += '<video id="my_video_1" ' + w_h + ' class="video-js vjs-default-skin" preload="auto" poster="http://www.myartistdna.co/images/mad_poster.png">';
    html += '<source src="http://www.myartistdna.co/mad_030112b.webm" type="video/webm" />';
    html += '<source src="http://www.myartistdna.co/mad_030112b.mp4" type="video/mp4" />';
    html += '<source src="http://www.myartistdna.co/mad_030112b.iphone.mp4" type="video/mp4" />';
    html += '<source src="http://www.myartistdna.co/mad_030112b.ogv" type="video/ogg" />';
    html += '</video>';

    $('#video_container').empty();
    $('#video_container').html(html);
    g_videoPlayer = _V_("my_video_1");
    g_videoPlayer.ready(onVideoReady);
}

function onVideoReady()
{
    g_videoPlayer.play();
}

function onWindowResize()
{
    if( g_videoPlayer )
    {
        var h = $('#video_container').height();
        var w = $('#video_container').width();
        g_videoPlayer.size(w,h);
    }
}




