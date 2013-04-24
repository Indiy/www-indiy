

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

function splashReady()
{
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
    
    if( IS_MOBILE )
    {
        window.scrollTo(0,1);
    }
}
$(document).ready(splashReady);

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
    $('#home_bg').swipe("scrollto",index);
}
function backgroundUpdateToIndex(index)
{
    g_currentBackgroundIndex = index;
    var background = g_backgroundList[index];
    
    backgroundLoadImage(background,index);
    $('#body_content_info').html(background.content_info_html);
    
    if( g_rotateTimeout !== false )
        window.clearTimeout(g_rotateTimeout);
    
    g_rotateTimeout = window.setTimeout(rotateBackground,ROTATE_MS);
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
    imageLoadItem(background,index,'#home_bg');
}

function backgroundResizeBackgrounds()
{
    imageResizeBackgrounds(g_backgroundList,'#home_bg');
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


