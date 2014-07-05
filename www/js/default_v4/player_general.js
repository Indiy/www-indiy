

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

    $('#volume_slider .bar').click(clickVolume);
    var opts = {
        'axis': "y",
        'containment': "#volume_slider .bar",
        'drag': volumeDrag,
        'dragstop': volumeDragStop
    };
    
    $('#volume_slider .bar .handle').draggable(opts);
    
    $('#media_seek_bar').click(clickSeekBar);
    
    var show_social = true;
    
    var anchor_map = getAnchorMap();
    if( 'product_id' in anchor_map )
    {
        var product_id = anchor_map['product_id'];
        showStore(product_id);
        show_social = false;
    }
    if( g_playlistList.length > 0 && g_playlistList[0].type == 'DIR' )
    {
        g_startPlaylistItemIndex = 0;
    }
    
    if( ( 'playlist_id' in anchor_map ) && ( 'playlist_item_id' in anchor_map ) )
    {
        var playlist_id = anchor_map.playlist_id;
        var playlist_item_id = anchor_map.playlist_item_id;
        
        for( var i = 0 ; i < g_playlistList.length ; ++i )
        {
            var playlist = g_playlistList[i];
            if( playlist.type == 'DIR' )
            {
                for( var j = 0 ; j < playlist.items.length ; ++j )
                {
                    var child_playlist = playlist.items[j];
                    var index = find_playlist_item(child_playlist,playlist_id,playlist_item_id);
                    if( index !== false )
                    {
                        g_startPlaylistIndex = i;
                        g_startChildPlaylistIndex = j;
                        g_startPlaylistItemIndex = index;
                    }
                }
            }
            else
            {
                var index = find_playlist_item(playlist,playlist_id,playlist_item_id);
                if( index !== false )
                {
                    g_startPlaylistIndex = i;
                    g_startChildPlaylistIndex = index;
                    g_startPlaylistItemIndex = false;
                }
            }
        }
    }
    defaultReady(show_social);
    
    if( g_touchDevice )
    {
        $(document).bind("touchstart",showControls);
        $(document).bind("touchend",timeoutControls);
    }
    else
    {
        $(document).mousemove(showAndTimeoutControls);
    }
    
    g_storedHash = window.location.hash;
    if( "onhashchange" in window )
    {
        window.onhashchange = maybeHashChanged;
    }
    else
    {
        window.setInterval(maybeHashChanged, 100);
    }
}

function showControls()
{
    if( !g_controlsShown )
    {
        g_controlsShown = true;
        $('.idle_fade_out').fadeIn(400,maybeShowMoreTabsButton);
    }
    else if( !$('.idle_fade_out').is(':animated') )
    {
        $('.idle_fade_out').show();
        $('.idle_fade_out').css("opacity",1.0);
        maybeShowMoreTabsButton();
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
    if( g_hideBottomTimeout !== false )
    {
        window.clearTimeout(g_hideBottomTimeout);
        g_hideBottomTimeout = false;
    }
}
function timeoutControls()
{
    clearTimeoutControls();
    
    if( !g_showingContentPage && !g_searchOpen )
    {
        var timeout = HIDE_CONTROLS_NORMAL_TIMEOUT;
        if( g_bottomOpen )
            timeout = HIDE_CONTROLS_OPEN_TIMEOUT;
        
        g_hideControlsTimeout = window.setTimeout(hideControls,timeout);
    }
    //g_hideBottomTimeout = window.setTimeout(closeBottom,HIDE_BOTTOM_TIMEOUT);
}
function hideControls()
{
    if( !g_showingContentPage && !g_searchOpen )
    {
        g_controlsShown = false;
        $('.idle_fade_out').fadeOut();
        closeBottom(false);

    }
}


function toggleBottom()
{
    if( g_bottomOpen )
        closeBottom();
    else
        openBottom();
}

function openBottom()
{
    hideVolume();
    hideVideoBitrate();
    hideContentPage();
    hideAllTabs();
    hideSocialFeed();
    g_bottomOpen = true;
    $('#bottom_container').stop();
    $('#bottom_container').animate({ height: '275px' });
}

function closeBottom(animate)
{
    hideVolume();
    hideVideoBitrate();
    g_bottomOpen = false;
    if( animate === false )
    {
        $('#bottom_container').css('height','55px');
    }
    else
    {
        $('#bottom_container').stop();
        $('#bottom_container').animate({ height: '55px' });
    }
}

function changeSocialContainer()
{
    openBottom();
}

function toggleSearchBox()
{
    if( g_searchOpen )
    {
        g_searchOpen = false;
        closeSearch();
    }
    else
    {
        g_searchOpen = true;
        hideTab();
        openSearch();
    }
}

function maybeAskForEmail()
{
    
}

function clickMusicIcon()
{
    clickMediaIcon("music",clickMusicMediaButton);
}
function clickVideoIcon()
{
    clickMediaIcon("video",clickVideoMediaButton);
}
function clickPhotoIcon()
{
    clickMediaIcon("photo",clickPhotoMediaButton);
}

function clickMediaIcon(name,callback)
{
    if( g_bottomOpen && g_mediaContent == name )
    {
        closeBottom();
    }
    else 
    {
        if( !g_bottomOpen )
            openBottom();
        
        callback();
    }
}

function preMediaShow()
{
    $('#media_content_lists .media_list').hide();
}
function preSocialItemShow()
{
    $('#media_social_boxes .social_item').hide();
}
function clickPhotoMediaButton()
{
    preMediaShow();
    $('#photo_list').show();
    g_mediaContent = "photo";
    scrollPhotoListToIndex();
}
function clickMusicMediaButton()
{
    preMediaShow();
    $('#music_list').show();
    g_mediaContent = "music";
}
function clickVideoMediaButton()
{
    preMediaShow();
    $('#video_list').show();
    g_mediaContent = "video";
    scrollVideoListToIndex();
}

function clickShareButton()
{
    preSocialItemShow();
    $('#social_share').show();
    g_socialContent = "share";
}
function clickEmailButton()
{
    preSocialItemShow();
    $('#social_email').show();
    g_socialContent = "email";
}
function clickTwitterButton()
{
    preSocialItemShow();
    $('#social_twitter').show();
    g_socialContent = "twitter";
}
function clickFacebookButton()
{
    preSocialItemShow();
    $('#social_facebook').show();
    g_socialContent = "twitter";
}

function toggleContentPage()
{
    if( g_showingContentPage )
    {
        hideContentPage();
        return false;
    }
    else
    {
        showContentPage();
        return true;
    }
}

function showContentPage()
{
    g_showingContentPage = true;
    closeBottom();
    hideSocialFeed();
}
function hideContentPage()
{
    g_showingContentPage = false;
}

function setPlayerMode(mode)
{
    g_playerMode = mode;
    if( g_playerMode == "music" )
    {
        videoHide();
        photoHide();
        musicShow();
    }
    else if( g_playerMode == "video" )
    {
        musicHide();
        photoHide();
        videoShow();
    }
    else if( g_playerMode == "photo" )
    {
        musicHide();
        videoHide();
        photoShow();
    }
}

function hideVolume()
{
    $('#volume_slider').hide();
    g_volumeShown = false;
}

function playerVolume()
{
    if( g_volumeShown )
    {
        hideVolume();
    }
    else
    {
        var offset = $('#tracker_bar .media_controls .volume').offset();
        var top = offset.top - 103;
        var left = offset.left + 10;
        $('#volume_slider').css({ top: top, left: left });
        $('#volume_slider').show();
        g_volumeShown = true;
    }
}

var g_videoBitrateShown = false;
function videoBitrate()
{
    if( g_videoBitrateShown )
    {
        hideVideoBitrate();
    }
    else
    {
        var offset = $('#tracker_bar .media_controls .progress_bar .progress_play_times .bitrate').offset();
        var bottom = $(window).height() - offset.top + 12;
        var left = offset.left - 10;
        $('#quality_popup').css({ bottom: bottom, left: left });
        $('#quality_popup').show();
        g_videoBitrateShown = true;
    }
}
function hideVideoBitrate()
{
    $('#quality_popup').hide();
    g_videoBitrateShown = false;
}

function clickVolume(event)
{
    var y = event.pageY - $('#volume_slider .bar').offset().top;
    var height = $('#volume_slider .bar').height();
    var vol_ratio = 1 - y / height;

    volumeSetLevel(vol_ratio);

    volumeChange(vol_ratio);
}

function volumeChange(vol_ratio)
{
    playlistSetVolume(vol_ratio);
}

function volumeSetLevel(vol_ratio)
{
    var height = $('#volume_slider .bar').height();

    var curr_height = height * vol_ratio;
    var handle_top = height - curr_height;
    $('#volume_slider .bar .current').css({ height: curr_height });
    $('#volume_slider .bar .handle').css({ top: handle_top });
}

function volumeDrag(event,ui)
{
    var click_top = $('#volume_slider .bar .handle').offset().top;
    var bar_top = $('#volume_slider .bar').offset().top;
    
    var y = click_top - bar_top;
    var height = $('#volume_slider .bar').height();
    var vol_ratio = 1 - y / height;
    
    var curr_height = height * vol_ratio;
    
    $('#volume_slider .bar .current').css({ height: curr_height });
    volumeChange(vol_ratio);
}
function volumeDragStop(event,ui)
{
    volumeDrag(event,ui);
}

function clickSeekBar(event)
{
    var x = event.pageX - $('#media_seek_bar').offset().left;
    var width = $('#media_seek_bar').width();
    var seek_ratio = x / width;
    
    playlistSeek(seek_ratio);
}

function formatMinSeconds(seconds)
{
    seconds = Math.floor(seconds);
    var mins = Math.floor(seconds / 60);
    var seconds = seconds % 60;
    var seconds_string = '';
    if( seconds < 10 )
        seconds_string += "0";
    seconds_string += seconds;
    return mins + ":" + seconds_string;
}

function playerProgress(curr_time,total_time)
{
    var time = formatMinSeconds(curr_time);
    if( total_time > 0 )
        time += " / " + formatMinSeconds(total_time);

    if( total_time <= 0 )
        total_time = 5*60;
    var percent = curr_time / total_time * 100.0;

    $('#track_progress').html(time);
    $('#track_current_bar').css('width',percent + "%");
}
function playerPhotoInfo(name,location,views)
{
    $('#media_controls').hide();
    $('#big_play_icon').hide();
    $('#photo_info').show();
    $('#photo_info .name').html(name);
    $('#photo_info .location').html(location);
}

function playerUpdateTotalViewCount(count)
{
    g_totalPageViews = count;
    $('#total_view_count').html(g_totalPageViews);
}

function playerTrackInfo(track_name,views)
{
    $('#photo_info').hide();
    $('#media_controls').show();
    $('#big_play_icon').show();

    $('#track_name').html(track_name);
    playerUpdateElementViews(views);
}
function playerUpdateElementViews(views)
{
     $('#track_play_count').html(views);
}

function playerSetPaused()
{
    $('#track_play_pause_button').removeClass('playing');
    $('#big_play_icon').removeClass('playing');
}
function playerSetPlaying()
{
    $('#track_play_pause_button').addClass('playing');
    $('#big_play_icon').addClass('playing');
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
    var anchor_map = getAnchorMap();
    
    jQuery.extend(anchor_map,map);
    
    var anchor = "";
    for( var key in anchor_map )
    {
        var val = anchor_map[key];
        
        if( val.length > 0 )
        {
            if( anchor.length > 0 )
                anchor += "&";
            anchor += "{0}={1}".format(key,val);
        }
    }

    // inhibit hashChanged if we do it
    g_storedHash = "#" + anchor;
    window.location.hash = anchor;
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
        showStore(product_id);
    }
}
