
var g_updateInterval = false;
var g_videoPlayer = false;
var g_videoPlaying = false;

var g_loadTime = false;

var g_backgroundList = [];
var g_musicIndex = 0;
var g_isPlaying = false;

function splashReady()
{
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

var g_inhibitControlsHide = false;

function showControls()
{
    if( !g_controlsShown )
    {
        g_controlsShown = true;
        $('.idle_hide').fadeOut();
    }
    else if( !$('.idle_hide').is(':animated') )
    {
        $('.idle_hide').stop(true,false);
        $('.idle_hide').fadeIn();
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
    if( !g_inhibitControlsHide )
    {
        g_controlsShown = false;
        $('.idle_hide').fadeOut();
    }
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

function getDigitHtml(value)
{
    var tens = Math.floor(value / 10);
    var ones = value % 10;
    
    var html = "<div>{0}</div><div>{1}</div>".format(tens,ones);
    return html;
}


