
var g_backgroundListIndex = 0;
var g_currentBackgroundIndex = 0;

var g_backgroundChangeToIndex = false;
var g_backgroundReady = false;

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
    $('#home_bg').bind('contextmenu', function(e) { return false; });
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
    g_backgroundReady = true;
    if( g_backgroundChangeToIndex !== false )
        backgroundChangeIndex(g_backgroundChangeToIndex);
    g_backgroundChangeToIndex = false;
}

function backgroundChangeId( background_id )
{
    for( var i = 0 ; i < g_backgroundList.length ; ++i )
    {
        var background = g_backgroundList[i];
        if( background.id == background_id )
        {
            backgroundChangeIndex(i);
            return;
        }
    }
}

function backgroundChangeIndex( index )
{
    if( !g_backgroundReady )
    {
        g_backgroundChangeToIndex = index;
        return;
    }
    
    setPlayerMode("background");
    
    $('#home_bg').swipe("scrollto",index);
}
function backgroundUpdateToIndex(index)
{
    g_currentBackgroundIndex = index;
    var background = g_backgroundList[index];
    
    backgroundLoadImage(background,index);
    
    backgroundUpdateViews(background.id,index);
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
