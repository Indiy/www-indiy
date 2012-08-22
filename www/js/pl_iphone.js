
var HIDE_CONTROLS_TIMEOUT = 3000;

var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;
var IS_IPHONE = navigator.userAgent.match(/iPhone/i) != null;
var IS_IOS = IS_IPAD || IS_IPHONE;

var IS_IE = false;
var IS_OLD_IE = false;

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

var IS_NARROW = false;

var g_bottomOpen = false;
var g_mediaContent = "music";
var g_playerMode = "music";
var g_controlsShown = true;
var g_hideControlsTimeout = false;
var g_hideBottomTimeout = false;
var g_socialContent = "share";
var g_showingContentPage = false;
var g_searchOpen = false;
var g_volumeShown = false;
var g_touchDevice = false;

function iphoneGeneralReady()
{
    if( !('ontouchstart' in document) )
    {
        g_touchDevice = false;
        $('body').addClass('no_touch');
    }
    else
    {
        g_touchDevice = true;
    }

    scrollToTop();
    $(window).resize(scrollToTop);
    
    photoChangeIndex(0);
    
    if( g_touchDevice )
    {
        $(document).bind("touchstart",showControls);
        $(document).bind("touchend",timeoutControls);
    }
    else
    {
        $(document).mousemove(showAndTimeoutControls);
    }
    timeoutControls();
}
$(document).ready(iphoneGeneralReady);

function scrollToTop()
{
    window.scrollTo(0,1);
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
        $('.idle_fade_out').stop();
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
}
function timeoutControls()
{
    clearTimeoutControls();
    
    if( !g_showingContentPage && !g_searchOpen )
    {
        g_hideControlsTimeout = window.setTimeout(hideControls,HIDE_CONTROLS_TIMEOUT);
    }
}
function hideControls()
{
    g_controlsShown = false;
    $('.idle_fade_out').fadeOut();
}


function setPlayerMode(mode)
{
    g_playerMode = mode;
    $('#tracker_bar .buttons .button').removeClass('active');
    if( g_playerMode == "music" )
    {
        //videoHide();
        photoHide();
        musicShow();
        $('#tracker_bar .buttons .music.button').addClass('active');
    }
    else if( g_playerMode == "video" )
    {
        musicHide();
        photoHide();
        //videoShow();
        $('#tracker_bar .buttons .video.button').addClass('active');
    }
    else if( g_playerMode == "photo" )
    {
        musicHide();
        //videoHide();
        photoShow();
        $('#tracker_bar .buttons .photos.button').addClass('active');
    }
}

function playerPhotoInfo(name,location,listens)
{
    $('#big_play_icon').hide();
}
function playerTrackInfo(track_name,listens)
{
    
}

function clickPhotoIcon()
{
    photoChangeIndex(0);
}
function clickMusicIcon()
{
    musicChangeIndex(0);
}
function clickVideoIcon()
{
    
}
