

var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;
var IS_IPHONE = navigator.userAgent.match(/iPhone/i) != null;
var IS_IOS = IS_IPAD || IS_IPHONE;

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

var g_currentBackgroundIndex = 0;

var g_backgroundList = [
    {
        image: "/images/home_bg01.jpg",
        bg_color: "000000",
        bg_style: "STRETCH",
        loaded: false,
        image_data: { width:1267, height:800 }
    },
    {
        image: "/images/home_bg02.jpg",
        bg_color: "000000",
        bg_style: "STRETCH",
        loaded: false,
        image_data: { width:1267, height:800 }
    },
    {
        image: "/images/home_bg03.jpg",
        bg_color: "000000",
        bg_style: "STRETCH",
        loaded: false,
        image_data: { width:1267, height:800 }
    },
    {
        image: "/images/home_bg04.jpg",
        bg_color: "000000",
        bg_style: "STRETCH",
        loaded: false,
        image_data: { width:1267, height:800 }
    },
];

var ROTATE_MS = 1000;

var rotateTimeout = false;

$(document).ready(backgroundOnReady);
function backgroundOnReady()
{
    var opts = {
        panelCount: g_backgroundList.length,
        resizeCallback: backgroundResizeBackgrounds,
        onPanelChange: backgroundPanelChange,
        onPanelVisible: backgroundPanelVisible,
        onReady: backgroundSwipeReady
    };
    $('#home_bg').swipe(opts);
    backgroundPreloadImages();
    rotateTimeout = window.setTimeout(rotateBackground,ROTATE_MS);
}

function rotateBackground()
{
    backgroundNext();
    window.setTimeout(rotateBackground,ROTATE_MS);
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
    $('#home_bg').swipe("scrollto",index);
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
    imageLoadItem(background,index,'#home_bg');
}

function backgroundResizeBackgrounds()
{
    imageResizeBackgrounds(g_backgroundList,'#home_bg');
}
