
var HIDE_CONTROLS_NORMAL_TIMEOUT = 5000;
var HIDE_CONTROLS_OPEN_TIMEOUT = 15000;

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
var g_touchDevice = true;

function iphoneGeneralReady()
{
    scrollToTop();
    $(window).resize(scrollToTop);
    
    photoChangeIndex(0);
}
$(document).ready(iphoneGeneralReady);

function scrollToTop()
{
    window.scrollTo(0,1);
}

function setPlayerMode(mode)
{
    g_playerMode = mode;
    if( g_playerMode == "music" )
    {
        //videoHide();
        photoHide();
        //musicShow();
    }
    else if( g_playerMode == "video" )
    {
        //musicHide();
        photoHide();
        //videoShow();
    }
    else if( g_playerMode == "photo" )
    {
        //musicHide();
        //videoHide();
        photoShow();
    }
}
