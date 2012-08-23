
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
    
    var anchor_map = getAnchorMap();
    if( 'song_id' in anchor_map )
    {
        var song_id = anchor_map['song_id'];
        musicChangeId(song_id);
    }
    else if( 'video_id' in anchor_map )
    {
        var video_id = anchor_map['video_id'];
        videoChangeId(video_id);
    }
    else if( 'photo_id' in anchor_map )
    {
        var photo_id = anchor_map['photo_id'];
        photoChangeId(photo_id);
    }
    else if( g_startMediaType == 'MUSIC' )
    {
        musicChange(0);
    }
    else if( g_startMediaType == 'PHOTO' )
    {
        photoChangeIndex(0);
    }
    else if( g_startMediaType == 'VIDEO' )
    {
        videoPlayIndex(0);
    }
    else
    {
        if( g_musicList.length > 0 )
            musicChange(0);
        else if( g_photoList.length > 0 )
            photoChangeIndex(0);
        else if( g_videoList.length > 0 )
            videoPlayIndex(0);
    }
    
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
        $('#big_play_icon').show();
        videoHide();
        photoHide();
        musicShow();
        $('#tracker_bar .buttons .music.button').addClass('active');
    }
    else if( g_playerMode == "video" )
    {
        $('#big_play_icon').show();
        musicHide();
        photoHide();
        videoShow();
        $('#tracker_bar .buttons .video.button').addClass('active');
    }
    else if( g_playerMode == "photo" )
    {
        $('#big_play_icon').hide();
        musicHide();
        videoHide();
        photoShow();
        $('#tracker_bar .buttons .photos.button').addClass('active');
    }
}

function playerPhotoInfo(name,location,listens)
{
}
function playerTrackInfo(track_name,listens)
{
}
function playerSetPlaying()
{
    $('#big_play_icon').removeClass('paused');
    $('#big_play_icon').addClass('playing');
}
function playerSetPaused()
{
    $('#big_play_icon').removeClass('playing');
    $('#big_play_icon').addClass('paused');
}
function playerPlay()
{
    if( g_playerMode == "music" )
        musicPlay();
    else if( g_playerMode == "video" )
        videoPlay();
}


function clickPhotoIcon()
{
    photoChangeIndex(0);
}
function clickMusicIcon()
{
    musicChangeIndex(0,false);
    musicPlay();
}
function clickVideoIcon()
{
    videoChangeIndex(0,false);
}


function getAnchorMap()
{
    var anchor_map = {};
    var anchor = self.document.location.hash.substring(1);
    var anchor_elements = anchor.split('&');
    var g_anchor_map = {};
    for( var k in anchor_elements )
    {
        var e = anchor_elements[k];
        var k_v = e.split('=');
        
        k = unescape(k_v[0]);
        if( k_v.length > 1 )
            anchor_map[k] = unescape(k_v[1]);
        else
            anchor_map[k] = true;
    }
    return anchor_map;
}
