
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

var g_backgroundList = [];

function streamReady()
{

    if( IS_CHROME )
        $('body').addClass('chrome');
    if( IS_IPAD )
        $('body').addClass('ipad');
    if( IS_IPHONE )
        $('body').addClass('iphone');
    if( IS_IPOD )
        $('body').addClass('ipod');
    if( IS_IOS )
        $('body').addClass('ios');
    if( IS_MOBILE )
        $('body').addClass('mobile');
    if( IS_DESKTOP )
        $('body').addClass('desktop');


    if( g_templateParams.bg_file )
    {
        g_backgroundList = [ g_templateParams.bg_file ];

        imageLoadItem(g_backgroundList[0],0,'#splash_bg');
        splashResize();
        $(window).resize(splashResize);
    }

    var height = $('#mad_tw_timeline').height() - 10;
    
    var twitter_widget = g_templateParams['twitter_widget'];
    
    var html = false;
    if( twitter_widget && twitter_widget.length > 0 )
    {
        var re = new RegExp("<a[^<]*</a>");
        var m = re.exec(twitter_widget);
        
        if( m )
        {
            html = m[0];
            html = html.replace('<a ','<a height="{0}" '.format(height));
        }
    }
    if( html !== false )
    {
        $('#mad_tw_timeline').html(html);
    }
    else
    {
        $('#mad_tw_timeline').hide();
    }
    
    twitterWidgetLoad();
}
$(document).ready(streamReady);

function splashResize()
{
    imageResizeBackgrounds(g_backgroundList,'#splash_bg');
}

function twitterWidgetLoad()
{
    if( typeof twttr != 'undefined' )
    {
        twttr.widgets.load();
        
        if( !IS_PHONE && !IS_IPAD )
        {
            showTwitter();
        }
    }
    else
    {
        window.setTimeout(twitterWidgetLoad,300);
    }
}


function hideTwitter()
{
    if( IS_IPAD )
    {
        $('#video_bg').removeClass('min_video');
    }

    $('#mad_tw_timeline').hide();
    
    $('#overlay .top_bar .right .show_feed').show();
    $('#overlay .top_bar .right .hide_feed').hide();
}
function showTwitter()
{
    hideStore();
    if( IS_IPAD )
    {
        $('#video_bg').addClass('min_video');
    }

    $('#mad_tw_timeline').show();

    $('#overlay .top_bar .right .show_feed').hide();
    $('#overlay .top_bar .right .hide_feed').show();
}
function toggleShowTwitter()
{
    hideStore();
    $('#iphone_show_store_button .button').html('SHOW STORE');
    
    if( $('#mad_tw_timeline').is(":visible") )
    {
        $('#iphone_show_twitter_button .button').html('SHOW #MEEKLIVE');
        $('#mad_tw_timeline').hide();
    }
    else
    {
        $('#iphone_show_twitter_button .button').html('HIDE #MEEKLIVE');
        $('#mad_tw_timeline').show();
        $(document).scrollTop($('#iphone_show_twitter_button').position().top);
    }
}

function showContentPage()
{
}

function hideAllTabs()
{
    hideStore();
}

function updateAnchor()
{
    
}

function showStreamStore()
{
    if( IS_IPAD )
    {
        ipadToggleStore();
    }
    else
    {
        hideTwitter();
        showStore();
    }
}

function ipadToggleStore()
{
    if( $('#video_bg').hasClass('min_video') )
    {
        hideStore();
        $('#video_bg').removeClass('min_video');
        
        $('#overlay .shop_button .show_shop').html('+ SHOP');
    }
    else
    {
        hideTwitter();
        showStore();
        $('#video_bg').addClass('min_video');
        
        $('#overlay .shop_button .show_shop').html('- HIDE');
    }
}

function hideStreamStore()
{
    if( IS_IPAD )
    {
        ipadToggleStore();
    }
    else
    {
        hideStore();
    }
}
function toggleShowStore()
{
    $('#mad_tw_timeline').hide();
    $('#iphone_show_twitter_button .button').html('SHOW #MEEKLIVE');
    
    if( $('#store_tab').is(":visible") )
    {
        $('#iphone_show_store_button .button').html('SHOW STORE');
        hideStore();
    }
    else
    {
        $('#iphone_show_store_button .button').html('HIDE STORE');
        showStore();
        $(document).scrollTop($('#iphone_show_store_button').position().top);
    }
}

