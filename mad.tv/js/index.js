
var PROGRESS_BAR_WIDTH = 534 - 2;
var PROGRESS_ROUND_LENGTH = PROGRESS_BAR_WIDTH - 6;

var g_videoHistory = false;
var g_genreList = ['rock'];
var g_controlsShown = false;
var g_hideControlsTimeout = false;
var g_touchDevice = false;

function setupVideoPlayer()
{
    if( !('ontouchstart' in document) )
    {
        $('body').addClass('no_touch');
        g_touchDevice = false;
    }
    else
    {
        g_touchDevice = true;
    }

    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(m,k,v){vars[k] = v;});
    
    if( 'genre' in vars )
        g_genre = vars['genre'];

    $(window).resize(onWindowResize);
    loadSteamInfo(startVideoInProgress);
    $(document).mousemove(showControls);
    showControls();
    $("#overlay_container").fadeIn();
}
$(document).ready(setupVideoPlayer);

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

function loadSteamInfo(callback)
{
    jQuery.ajax(
    {
        type: 'GET',
        url: "http://www.myartistdna.tv/test/data/stream_info.php?genre=" + g_genre,
        dataType: 'json',
        success: function(data) 
        {
            g_videoHistory = data.history;
            g_genreList = data.genre_list;
            callback();
        },
        error: function()
        {
            //window.alert("Error!");
        }
    });
}
function startVideoInProgress()
{
    startVideo(true);
}
function startVideoFromBegining()
{
    startVideo(false);
}

function mins_secs(secs)
{
    var mins = Math.floor(secs / 60);
    secs -= mins * 60;
    return sprintf("%02d:%02d",mins,secs); 
}


var g_playing = false;
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
        if( true || g_touchDevice )
        {
            g_videoPlayer[0].play();
        }
        else
        {
            loadSteamInfo(startVideoInProgress);
        }
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
        g_videoPlayer[0].pause();
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
    hideGenrePicker();
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
    if( !g_videoHistory )
        return;

    $('#history .content').empty();
    for( var i = 1 ; i < Math.min(g_videoHistory.length,4) ; ++i )
    {
        var track = g_videoHistory[i];
        var title = track.title;
        var duration = mins_secs(track.duration);
        var love = "";
        if( title in g_loveMap )
            love = "love";
        
        var html = "";
        html += "<div class='row'>";
        html += " <div class='icon'><img src=''></div>";
        html += " <div class='title'>" + title + "</div>";
        html += " <div class='length'>" + duration + "</div>";
        html += " <div id='history_loved_" + i + "' class='loved " + love + "'>";
        html += "  <div class='love_icon' onclick='toggleLoveHistory(this," + i + ");'>";
        html += "   <div class='tooltip love_tip'>";
        html += "    <div class='carrot'></div>";
        html += "    LOVE";
        html += "   </div>";
        html += "   <div class='tooltip unlove_tip'>";
        html += "    <div class='carrot'></div>";
        html += "    UNLOVE";
        html += "   </div>";
        html += "  </div>";
        html += " </div>";
        html += "</div>";
        $('#history .content').append(html);
    }
}
function showAddVideo()
{
    hideGenrePicker();
    $('#add_video').fadeIn();
}
function hideAddVideo()
{
    $('#add_video').fadeOut();
}


var g_videoPlayer = false;
var g_videoPlayerIndex = 0;
function startVideo(in_progress)
{
    updateHistory();

    var h = $('#video_container').height();
    var w = $('#video_container').width();

    var video = g_videoHistory[0];
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");

    updateVideoDisplay();
    
    if( g_videoPlayer !== false )
        g_videoPlayer.pause();
    
    var vid_name = "madtv_player_" + g_videoPlayerIndex;
    g_videoPlayerIndex++;
    var html = '';
    html += "<video id='{0}' width='{1}' height='{2}' src='{3}' class='video-js vjs-default-skin' preload='metadata' >"
        .format(vid_name,w,h,url);
    html += "</video>";

    $('#video_container').empty();
    $('#video_container').html(html);
    g_videoPlayer = $("video");
    if( in_progress )
    {
        var time_progress = Math.floor((new Date().getTime())/1000 - video.start_time);
        
        if( time_progress > video.duration * 0.9 )
            time_progress = Math.floor(video.duration * 0.9);
            
        g_seekOnPlay = time_progress;
    }
    else
    {
        g_seekOnPlay = false;
    }
    g_videoPlayer.ready(function() { onVideoReady(); } );
}
function updateVideoDisplay()
{
    var video = g_videoHistory[0];
    var title = video.title;
    $('#track_title').text(title);
    if( title in g_loveMap )
        $('#player .heart').addClass('love');
    else
        $('#player .heart').removeClass('love');
}
function updateVideoElement()
{
    var video = g_videoHistory[0];
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");

    updateVideoDisplay();
    var media = [
         { type: "video/mp4", src: url },
         { type: "video/ogg", src: url_ogv }        
    ];
    g_videoPlayer.attr('src',url);
    g_videoPlayer[0].play();
}

var g_seekOnPlay = false;
function onVideoReady()
{
    g_videoPlayer.on("loadstart",videoLoadStart);
    g_videoPlayer.on("play",videoPlayStarted);
    g_videoPlayer.on("timeupdate",videoTimeUpdate);
    g_videoPlayer.on("ended",videoEnded);
    g_videoPlayer.on("durationchange",videoDurationChange);
    g_videoPlayer.on("progress",videoDownloadProgress);

    //g_videoPlayer[0].play();
}
function videoLoadStart()
{
    //seekVideo();
}
function videoDownloadProgress()
{
    //seekVideo();
}
function videoTimeUpdate()
{
    seekVideo();
    videoProgress();
}
function videoDurationChange()
{
    seekVideo();
    videoProgress();
}
function videoPlayStarted()
{
    g_playing = true;
    $('#player .play').removeClass('paused');
}
function videoProgress(a)
{
    var curr_pos = g_videoPlayer[0].currentTime;
    var duration = g_videoPlayer[0].duration;
    
    var s = mins_secs(curr_pos) 
    if( duration )
        s += " - " + mins_secs(duration);
    if( $('#track_duration').text() != s )
        $('#track_duration').text(s);
    
    var percent = 0;
    if( duration > 0 )
        var percent = curr_pos/duration;
    var width = percent * PROGRESS_BAR_WIDTH;
    $('#player .progress .bar').width(width);
    if( width >= PROGRESS_ROUND_LENGTH )
        $('#player .progress .bar').css('border-radius','6px 6px 6px 6px');
    else
        $('#player .progress .bar').css('border-radius','6px 0px 0px 6px');
    
}
function videoEnded()
{
    loadSteamInfo(updateVideoElement);
}

var g_lastSeek = 0;
function seekVideo()
{
    if( g_seekOnPlay !== false )
    {
        var curr_time = new Date().getTime();
        var delta = curr_time - g_lastSeek;
        if( delta < 100 )
        {
            window.setTimeout(seekVideo,100);
            return;
        }
        g_lastSeek = curr_time;
    
        var pos = g_videoPlayer[0].currentTime + 1;
        if( pos >= g_seekOnPlay )
            g_seekOnPlay = false;
        else
            g_videoPlayer[0].currentTime = g_seekOnPlay;
    }
}

function onWindowResize()
{
    if( g_videoPlayer )
    {
        var h = $('#video_container').height();
        var w = $('#video_container').width();
        g_videoPlayer.width(w);
        g_videoPlayer.height(h);
    }
}




