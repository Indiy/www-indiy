
var PROGRESS_BAR_WIDTH = 468;
var PROGRESS_ROUND_LENGTH = 462;

var g_videoHistory = false;

$(document).ready(setupVideoPlayer)

function setupVideoPlayer()
{
    loadSteamInfo(startVideoInProgress);

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

function loadSteamInfo(callback)
{
    jQuery.ajax(
    {
        type: 'GET',
        url: "http://www.myartistdna.tv/test/data/stream_info.php",
        dataType: 'json',
        success: function(data) 
        {
            g_videoHistory = data;
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

function mins_secs(secs)
{
    var mins = Math.floor(secs / 60);
    secs -= mins * 60;
    return sprintf("%02d:%02d",mins,secs); 
}

function videoProgress(a)
{
    var curr_pos = g_videoPlayer.currentTime();
    var duration = g_videoPlayer.duration();

    var s = mins_secs(curr_pos) 
    if( duration )
        s += " - " + mins_secs(duration);
    if( $('#track_duration').text() != s )
        $('#track_duration').text(s);

    var percent = 0;
    if( duration > 0 )
        var percent = curr_pos/duration;
    var width = percent * PROGRESS_BAR_WIDTH;
    $('#player .progress .bar').css(width);
    if( width >= PROGRESS_ROUND_LENGTH )
        $('#player .progress .bar').css('border-radius','6px 6px 6px 6px');
    else
        $('#player .progress .bar').css('border-radius','6px 0px 0px 6px');
    
}
function videoEnded()
{
    
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
        loadSteamInfo(startVideoInProgress);
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
        g_videoPlayer.pause();
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
function startVideo(in_progress)
{
    var h = $('#video_container').height();
    var w = $('#video_container').width();

    var video = g_videoHistory[0];
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");

    $('#track_title').text(video.title);
    
    var w_h = " width='" + w + "' height='" + h + "' ";
    
    var html = '';
    html += '<video id="my_video_1" ' + w_h + ' class="video-js vjs-default-skin" preload="auto">';
    html += '<source src="' + url + '" type="video/mp4" />';
    html += '<source src="' + url_ogv + '" type="video/ogg" />';
    html += '</video>';

    $('#video_container').empty();
    $('#video_container').html(html);
    g_videoPlayer = _V_("my_video_1");
    var seek = 0;
    if( in_progress )
    {
        var time_progress = Math.floor((new Date().getTime())/1000 - video.start_time);
        
        if( time_progress > video.duration * 0.9 )
            time_progress = Math.floor(video.duration * 0.9);
            
        seek = time_progress;
    }
    g_videoPlayer.ready(function() { onVideoReady(seek); } );
}

function onVideoReady(seek)
{
    g_videoPlayer.addEvent("timeupdate",videoProgress);
    g_videoPlayer.addEvent("ended",videoEnded);
    g_videoPlayer.addEvent("durationchange",videoProgress);

    g_videoPlayer.currentTime(seek);
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




