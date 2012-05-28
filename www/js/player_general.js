

var HIDE_CONTROLS_TIMEOUT = 4000;
var HIDE_BOTTOM_TIMEOUT = 6000;

var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;

var IS_IE = false;
var IS_OLD_IE = false;
(function() {
    var ie_match = navigator.userAgent.match(/IE ([^;]*);/);
    if( ie_match != null && ie_match.length > 1 )
    {
        IS_IE = true;
        var ie_version = parseFloat(ie_match[1]);
        if( ie_version < 9.0 )
            IS_OLD_IE = true;
    }
})();


var g_bottomOpen = false;
var g_mediaContent = "music";
var g_playerMode = "music";
var g_controlsShown = true;
var g_hideControlsTimeout = false;
var g_hideBottomTimeout = false;

$(document).ready(generalOnReady);
function generalOnReady()
{
    $(document).mousemove(showAndTimeoutControls);
}

function showControls()
{
    if( !g_controlsShown )
    {
        g_controlsShown = true;
        $('.idle_fade_out').fadeIn();
    }
    else if( !$('.idle_fade_out').is(':animated') )
    {
        $('.idle_fade_out').show();
        $('.idle_fade_out').css("opacity",1.0);
    }
    clearTimeoutControls();
}
function showAndTimeoutControls()
{
    showControls();
    timeoutControls();
}
function clearTimeoutControls()
{
    if( g_hideControlsTimeout !== false )
    {
        window.clearTimeout(g_hideControlsTimeout);
        g_hideControlsTimeout = false;
    }
    if( g_hideBottomTimeout !== false )
    {
        window.clearTimeout(g_hideBottomTimeout);
        g_hideBottomTimeout = false;
    }
}
function timeoutControls()
{
    clearTimeoutControls();
    g_hideControlsTimeout = window.setTimeout(hideControls,HIDE_CONTROLS_TIMEOUT);
    if( g_bottomOpen )
        g_hideBottomTimeout = window.setTimeout(closeBottom,HIDE_BOTTOM_TIMEOUT);
}
function hideControls()
{
    g_controlsShown = false;
    $('.idle_fade_out').fadeOut();
}


function toggleBottom()
{
    if( g_bottomOpen )
        closeBottom();
    else
        openBottom();
}

function openBottom()
{
    g_bottomOpen = true;
    $('#bottom_container').stop();
    $('#bottom_container').animate({ height: '275px' });
}

function closeBottom()
{
    g_bottomOpen = false;
    $('#bottom_container').stop();
    $('#bottom_container').animate({ height: '55px' });
}

function maybeAskForEmail()
{
    
}

function clickMusicIcon()
{
    clickBottomIcon("music",clickMusicMediaButton);
}
function clickBottomIcon(name,callback)
{
    if( g_bottomOpen && g_mediaContent == name )
    {
        closeBottom();
    }
    else 
    {
        if( !g_bottomOpen )
            openBottom();
        
        callback();
    }
}

function clickMusicMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#music_list').show();
    g_mediaContent = "music";
}
function clickVideoMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#video_list').show();
    g_mediaContent = "video";
}

function setPlayerMode(mode)
{
    g_playerMode = mode;
    if( g_playerMode == "music" )
    {
        musicShow();
        videoHide();
    }
    else if( g_playerMode == "video")
    {
        videoShow();
        musicHide();
    }
}

function playerPlayPause()
{
    if( g_playerMode == "music" )
        musicPlayPause();
    else if( g_playerMode == "video" )
        videoPlayPause();
}

function formatMinSeconds(seconds)
{
    seconds = Math.floor(seconds);
    var mins = Math.floor(seconds / 60);
    var seconds = seconds % 60;
    var seconds_string = '';
    if( seconds < 10 )
        seconds_string += "0";
    seconds_string += seconds;
    return mins + ":" + seconds_string;
}

function playerProgress(curr_time,total_time)
{
    var percent = curr_time / total_time * 100.0;
    var time = formatMinSeconds(curr_time) + " / " + formatMinSeconds(total_time);
    $('#track_progress').html(time);
    $('#track_current_bar').css('width',percent + "%");
}

function playerUpdateTotalViewCount()
{
    $('#total_view_count').html(g_totalPageViews);
}

function playerTrackInfo(track_name,listens)
{
    if( track_name )
        $('#track_name').html(track_name);
    $('#track_play_count').html(listens);
}
function playerSetPaused()
{
    $('#track_play_pause_button').removeClass('playing');
}
function playerSetPlaying()
{
    $('#track_play_pause_button').addClass('playing');
}
