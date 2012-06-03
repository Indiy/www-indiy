

var HIDE_CONTROLS_NORMAL_TIMEOUT = 5000;
var HIDE_CONTROLS_OPEN_TIMEOUT = 15000;

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

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');


var g_bottomOpen = false;
var g_mediaContent = "music";
var g_playerMode = "music";
var g_controlsShown = true;
var g_hideControlsTimeout = false;
var g_hideBottomTimeout = false;
var g_socialContent = "share";
var g_showingContentPage = false;

$(document).ready(generalOnReady);
function generalOnReady()
{
    clickMusicMediaButton();
    clickShareButton();

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
    
    if( !g_showingContentPage )
    {
        var timeout = HIDE_CONTROLS_NORMAL_TIMEOUT;
        if( g_bottomOpen )
            timeout = HIDE_CONTROLS_OPEN_TIMEOUT;
        
        g_hideControlsTimeout = window.setTimeout(hideControls,timeout);
    }
    //g_hideBottomTimeout = window.setTimeout(closeBottom,HIDE_BOTTOM_TIMEOUT);
}
function hideControls()
{
    g_controlsShown = false;
    $('.idle_fade_out').fadeOut();
    closeBottom(false);
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
    hideContentPage();
    hideAllTabs();
    g_bottomOpen = true;
    $('#bottom_container').stop();
    $('#bottom_container').animate({ height: '275px' });
}

function closeBottom(animate)
{
    g_bottomOpen = false;
    if( animate === false )
    {
        $('#bottom_container').css('height','55px');
    }
    else
    {
        $('#bottom_container').stop();
        $('#bottom_container').animate({ height: '55px' });
    }
}

function changeSocialContainer()
{
    openBottom();
}

function maybeAskForEmail()
{
    
}

function clickMusicIcon()
{
    clickMediaIcon("music",clickMusicMediaButton);
}
function clickVideoIcon()
{
    clickMediaIcon("video",clickVideoMediaButton);
}
function clickPhotoIcon()
{
    clickMediaIcon("photo",clickPhotoMediaButton);
}

function clickMediaIcon(name,callback)
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

function clickPhotoMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#photo_list').show();
    g_mediaContent = "photo";
    scrollPhotoToIndex();
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
    scrollVideoToIndex();
}

function clickShareButton()
{
    $('#social_content .social_item').hide();
    $('#social_share').show();
    g_socialContent = "share";
}
function clickEmailButton()
{
    $('#social_content .social_item').hide();
    $('#social_email').show();
    g_socialContent = "email";
}
function clickTwitterButton()
{
    $('#social_content .social_item').hide();
    $('#social_twitter').show();
    g_socialContent = "twitter";
}
function clickFacebookButton()
{
    $('#social_content .social_item').hide();
    $('#social_facebook').show();
    g_socialContent = "twitter";
}

function toggleContentPage()
{
    if( g_showingContentPage )
    {
        hideContentPage();
        return false;
    }
    else
    {
        showContentPage();
        return true;
    }
}

function showContentPage()
{
    g_showingContentPage = true;
    closeBottom();
}
function hideContentPage()
{
    g_showingContentPage = false;
}

function setPlayerMode(mode)
{
    g_playerMode = mode;
    if( g_playerMode == "music" )
    {
        videoHide();
        photoHide();
        musicShow();
    }
    else if( g_playerMode == "video" )
    {
        musicHide();
        photoHide();
        videoShow();
    }
    else if( g_playerMode == "photo" )
    {
        musicHide();
        videoHide();
        photoShow();
    }
}

function playerPlayPause()
{
    if( g_playerMode == "music" )
        musicPlayPause();
    else if( g_playerMode == "video" )
        videoPlayPause();
}
function playerPrevious()
{
    if( g_playerMode == "music" )
        musicPrevious();
    else if( g_playerMode == "video" )
        videoPrevious();
    else if( g_playerMode == "photo" )
        photoPrevious();
}
function playerNext()
{
    if( g_playerMode == "music" )
        musicNext();
    else if( g_playerMode == "video" )
        videoNext();
    else if( g_playerMode == "photo" )
        photoNext();
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
