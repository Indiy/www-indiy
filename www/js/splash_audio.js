

var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;
var IS_IPHONE = navigator.userAgent.match(/iPhone/i) != null;
var IS_IPOD = navigator.userAgent.match(/iPod/i) != null;
var IS_IOS = IS_IPAD || IS_IPHONE || IS_IPOD;

var IS_ANDROID = navigator.userAgent.match(/Android/i) != null;
var IS_ANDROID_PHONE = navigator.userAgent.match(/Android.*Mobile/i) != null;
var IS_ANDROID_TABLET = IS_ANDROID && !IS_ANDROID_PHONE;

var IS_PHONE = IS_IPOD || IS_IPHONE || IS_ANDROID_PHONE;
var IS_TABLET = IS_ANDROID_TABLET || IS_IPAD;

var IS_WINDOWS = navigator.userAgent.match(/Windows/i) != null;
var IS_CHROME = navigator.userAgent.match(/Chrome/i) != null;
var IS_MOBILE = navigator.userAgent.match(/Mobile/i) != null;
var IS_DESKTOP = !IS_MOBILE;

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
var PHONE_REGEX = new RegExp('^[0-9]{3}-[0-9]{3}-[0-9]{4}$');

var g_updateInterval = false;
var g_videoPlayer = false;
var g_videoPlaying = false;

var g_loadTime = false;

var g_backgroundList = [];
var g_musicIndex = 0;
var g_isPlaying = false;
var g_touchDevice = false;

function splashReady()
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

    g_loadTime = new Date();

    if( IS_CHROME )
        $('body').addClass('chrome');
    if( IS_IPAD )
        $('body').addClass('ipad');
    if( IS_IPHONE )
        $('body').addClass('iphone');
    if( IS_IOS )
        $('body').addClass('ios');
    if( IS_MOBILE )
        $('body').addClass('mobile');
    if( IS_DESKTOP )
        $('body').addClass('desktop');

    g_backgroundList = g_musicList;

    var opts = {
        panelCount: g_backgroundList.length,
        resizeCallback: backgroundResizeBackgrounds,
        onPanelChange: backgroundPanelChange,
        onPanelVisible: backgroundPanelVisible,
        onReady: backgroundSwipeReady
    };
    $('#splash_bg').swipe(opts);
    backgroundPreloadImages();

    g_updateInterval = window.setInterval(updateCountdown,250);
    updateCountdown();
    
    $('#jquery_jplayer').jPlayer({
                                 ready: jplayerReady,
                                 solution: "html, flash",
                                 supplied: "mp3, oga",
                                 swfPath: g_swfBase,
                                 verticalVolume: true,
                                 wmode: "window",
                                 volume: 0.8
                                 })
    .bind($.jPlayer.event.ended,jplayerEnded)
    .bind($.jPlayer.event.timeupdate,jplayerTimeUpdate)
    .bind($.jPlayer.event.play,jplayerPlay)
    .bind($.jPlayer.event.pause,jplayerPause)
    .bind($.jPlayer.event.volumechange,jplayerVolume);
    
    if( IS_MOBILE )
    {
        window.scrollTo(0,1);
    }

    if( !IS_PHONE )
    {
        if( g_touchDevice )
        {
            $(document).bind("touchstart",showControls);
            $(document).bind("touchend",timeoutControls);
        }
        else
        {
            $(document).mousemove(showAndTimeoutControls);
        }
        showAndTimeoutControls();
    }
}
$(document).ready(splashReady);

var g_controlsShown = true;
var g_hideControlsTimeout = false;
var HIDE_TIMEOUT = 5*1000;
var ANIMATE_DURATION = "fast";

function showControls()
{
    if( !g_controlsShown )
    {
        g_controlsShown = true;
        $('.idle_hide').animate({ height: '96px' },ANIMATE_DURATION);
    }
    else if( !$('.idle_hide').is(':animated') )
    {
        $('.idle_hide').stop(true,false);
        $('.idle_hide').css({ height: '96px' });
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
    g_hideControlsTimeout = window.setTimeout(hideControls,HIDE_TIMEOUT);
}
function hideControls()
{
    g_controlsShown = false;
    $('.idle_hide').animate({ height: '0px' },ANIMATE_DURATION);
}

function backgroundPanelChange(index)
{
    backgroundUpdateToIndex(index);
}

function backgroundPanelVisible(index)
{
    var background = g_backgroundList[index];
    backgroundLoadImage(background,index);
}

function backgroundSwipeReady()
{
}
function backgroundChangeIndex( index )
{
    $('#splash_bg').swipe("scrollto",index);
}
function backgroundUpdateToIndex(index)
{
    g_currentBackgroundIndex = index;
    var background = g_backgroundList[index];
    
    backgroundLoadImage(background,index);
}
function backgroundNext()
{
    var index = g_currentBackgroundIndex + 1;
    if( index == g_backgroundList.length )
        index = 0;
    
    backgroundChangeIndex(index);
}
function backgroundPrevious()
{
    var index = g_currentBackgroundIndex - 1;
    if( index < 0 )
        index = g_backgroundList.length - 1;
    
    backgroundChangeIndex(index);
}
function backgroundPreloadImages()
{
    for( var i = 0 ; i < g_backgroundList.length ; ++i  )
    {
        var background = g_backgroundList[i];
        backgroundLoadImage(background,i);
    }
}
function backgroundLoadImage(background,index)
{
    imageLoadItem(background,index,'#splash_bg');
}
function backgroundResizeBackgrounds()
{
    imageResizeBackgrounds(g_backgroundList,'#splash_bg');
}

function jplayerReady()
{
    setupCurrentMedia();
    if( !IS_MOBILE )
    {
        mediaPlay();
    }
}
function jplayerEnded()
{
    mediaNext();
}
function jplayerPlay()
{
    
}
function jplayerPause()
{
    
}
function jplayerVolume()
{
}
function jplayerTimeUpdate(event)
{
    var curr_time = event.jPlayer.status.currentTime;
    var total_time = event.jPlayer.status.duration;
    
    var mins = Math.floor(curr_time / 60);
    var secs = Math.floor(curr_time - mins * 60);
    
    updateTime(mins,secs);
}
function updateTime(mins,secs)
{
    var html = getDigitHtml(mins) + ":" + getDigitHtml(secs);
    $('#song_time').html(html);
}

function setupCurrentMedia()
{
    $('#jquery_jplayer').jPlayer("pause");
    
    var song = g_musicList[g_musicIndex];
    
    var media = {
        mp3: song.mp3
    };
    if( song.audio_extra && song.audio_extra.alts && song.audio_extra.alts.ogg )
    {
        media.oga = g_artistFileBaseUrl + song.audio_extra.alts.ogg;
    }
    $('#jquery_jplayer').jPlayer("setMedia", media);
    
    $('#song_title').html(song.name);
    updateTime(0,0);
    backgroundChangeIndex(g_musicIndex);
}
function mediaPlay()
{
    $('#jquery_jplayer').jPlayer("play");
    g_isPlaying = true;
    $('#footer .audio_player .controls .play_pause').addClass('playing');
}
function mediaPause()
{
    $('#jquery_jplayer').jPlayer("pause");
    g_isPlaying = false;
    $('#footer .audio_player .controls .play_pause').removeClass('playing');
}
function mediaTogglePlay()
{
    if( g_isPlaying )
    {
        mediaPause();
    }
    else
    {
        mediaPlay();
    }
}
function mediaNext()
{
    var index = g_musicIndex + 1;
    if( index == g_musicList.length )
        index = 0;
    g_musicIndex = index;
    setupCurrentMedia();
    mediaPlay();
}
function mediaPrevious()
{
    var index = g_musicIndex - 1;
    if( index < 0 )
        index = g_musicList.length - 1;
    g_musicIndex = index;
    setupCurrentMedia();
    mediaPlay();
}

function secsUntilEvent()
{
    var now = new Date();
    
    var time_left = g_eventDate - now;
    
    if( time_left < 0.0 )
    {
        if( time_left > -10*60*1000 )
        {
            maybeReload();
        }
        return 0;
    }
    
    return Math.floor(time_left/1000);
}

function maybeReload()
{
    var now = new Date();
    
    var time_since_load = now - g_loadTime;
    
    if( time_since_load > 30000 )
    {
        window.location.reload(true);
    }
}

function updateCountdown()
{
    var secs_left = secsUntilEvent();

    var seconds = Math.floor(secs_left % 60);
    var minutes = Math.floor( (secs_left / 60) % 60 );
    var hours = Math.floor( (secs_left / (60*60)) % 24 );
    var days = Math.floor( (secs_left / (24*60*60)) );
    
    $('#top_container .top_line .days .time').html(getDigitHtml(days));
    $('#top_container .top_line .hours .time').html(getDigitHtml(hours));
    $('#top_container .top_line .minutes .time').html(getDigitHtml(minutes));
    $('#top_container .top_line .seconds .time').html(getDigitHtml(seconds));
}

function getDigitHtml(value)
{
    var tens = Math.floor(value / 10);
    var ones = value % 10;
    
    var html = "<div>{0}</div><div>{1}</div>".format(tens,ones);
    return html;
}


