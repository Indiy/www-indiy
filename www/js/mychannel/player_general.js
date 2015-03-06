

var HIDE_CONTROLS_NORMAL_TIMEOUT = 5000;
var HIDE_CONTROLS_OPEN_TIMEOUT = 15000;

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
var IS_FIREFOX = navigator.userAgent.match(/Firefox/i) != null;
var IS_DESKTOP = !IS_MOBILE;

var IS_RETINA = window.devicePixelRatio > 1;
var IS_EMBED = window.location.href.match(/embed/i) != null;

var IS_IE = false;
var IS_OLD_IE = false;
var IS_VERY_OLD_IE = false;
var IS_UNSUPPORTED_BROWSER = false;

(function() {
    var ie_match = navigator.userAgent.match(/IE ([^;]*);/);
    if( ie_match != null && ie_match.length > 1 )
    {
        IS_IE = true;
        var ie_version = parseFloat(ie_match[1]);
        if( ie_version < 10.0 )
        {
            IS_OLD_IE = true;
        }
        if( ie_version < 9.0 )
        {
            IS_VERY_OLD_IE = true;
        }
        if( ie_version < 8.0 )
        {
            IS_UNSUPPORTED_BROWSER = true;
        }
    }
 
    if( navigator.userAgent.match(/Trident/i) != null )
    {
        IS_IE = true;
    }
})();

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

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
var g_stored_hash = "";

function find_playlist_item(playlist,playlist_id,playlist_item_id)
{
    if( playlist.playlist_id == playlist_id )
    {
        for( var i = 0 ; i < playlist.items.length ; ++i )
        {
            var playlist_item = playlist.items[i];
            if( playlist_item.playlist_item_id == playlist_item_id )
            {
                return i;
            }
        }
    }
    return false;
}
var g_startPlaylistIndex = 0;
var g_startChildPlaylistIndex = 0;
var g_startPlaylistItemIndex = false;

$(document).ready(generalOnReady);
function generalOnReady()
{
    if( IS_UNSUPPORTED_BROWSER )
    {
        window.location.href = g_trueSiteUrl + "/unsupported_browser.php";
    }

    if( IS_IE )
    {
        $('body').addClass('ie');
    }
    if( IS_FIREFOX )
    {
        $('body').addClass('firefox');
    }
    if( IS_WINDOWS )
    {
        $('body').addClass('windows');
    }
    
    if( !('ontouchstart' in document) )
    {
        g_touchDevice = false;
        $('body').addClass('no_touch');
    }
    else
    {
        g_touchDevice = true;
    }
    
    if( g_playlistList.length > 0 && g_playlistList[0].type == 'DIR' )
    {
        g_startPlaylistItemIndex = 0;
    }
}


function playerProgress(curr_time,total_time)
{
}
function playerPhotoInfo(name,location,views)
{
    $('.track_title').html(name);
}

function playerUpdateTotalViewCount(count)
{
}

function playerTrackInfo(track_name,views)
{
    $('.track_title').html(track_name);
}
function playerUpdateElementViews(views)
{
}
function playerVolumeSetLevel(vol_ratio)
{
}

function playerSetPaused()
{
    $('.track_play_pause_control').addClass('paused');
    $('.track_play_pause_control').removeClass('playing');
}
function playerSetPlaying()
{
    $('.track_play_pause_control').addClass('playing');
    $('.track_play_pause_control').removeClass('paused');
}


function getAnchorMap()
{
    var anchor_map = {};
    var anchor = self.document.location.hash.substring(1);
    var anchor_elements = anchor.split('&');
    var g_anchor_map = {};
    for( var i = 0 ; i < anchor_elements.length ; i++ )
    {
        var e = anchor_elements[i];
        var k_v = e.split('=');
        
        k = unescape(k_v[0]);
        if( k_v.length > 1 )
            anchor_map[k] = unescape(k_v[1]);
        else
            anchor_map[k] = true;
    }
    return anchor_map;
}
function updateAnchor(map)
{
}
function maybeHashChanged()
{
    if( g_storedHash != window.location.hash )
    {
        g_storedHash = window.location.hash;
        hashChanged(g_storedHash);
    }
}
function hashChanged()
{
    var anchor_map = getAnchorMap();
    
    if( 'product_id' in anchor_map )
    {
        var product_id = anchor_map['product_id'];
        showProductById(product_id);
    }
}
