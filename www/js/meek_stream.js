
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

    var height = $('#mad_tw_timeline').height() - 10;
    
    var html = '<a class="twitter-timeline" height="{0}" data-chrome="transparent" href="https://twitter.com/search?q=%23meeklive" data-widget-id="319675836225699842">Tweets about "#meeklive"</a>'.format(height);
    
    $('#mad_tw_timeline').html(html);
    twitterWidgetLoad();
}
$(document).ready(streamReady);

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

