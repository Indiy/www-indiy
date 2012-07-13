

var HIDE_CONTROLS_NORMAL_TIMEOUT = 5000;
var HIDE_CONTROLS_OPEN_TIMEOUT = 15000;

var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;

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

$(document).ready(generalOnReady);
function generalOnReady()
{
    clickMusicMediaButton();
    clickShareButton();

    $(document).mousemove(showAndTimeoutControls);

    $('#volume_slider .bar').click(clickVolume);
    var opts = {
        'axis': "y",
        'containment': "#volume_slider .bar",
        'drag': volumeDrag,
        'dragstop': volumeDragStop
    };
    
    $('#volume_slider .bar .handle').draggable(opts);
    
    var anchor_map = getAnchorMap();
    
    if( 'song_id' in anchor_map )
    {
        var song_id = anchor_map['song_id'];
        musicChangeId(song_id);
    }
    else if( 'video_id' in anchor_map )
    {
        var video_id = anchor_map['video_id'];
        videoChangeId(video_id);
    }
    else if( 'photo_id' in anchor_map )
    {
        var photo_id = anchor_map['photo_id'];
        photoChangeId(photo_id);
    }
    else
    {
        if( g_musicList.length > 0 )
            musicChange(0);
        else if( g_videoList.length > 0 )
            videoPlayIndex(0);
        else if( g_photoList.length > 0 )
            photoShowIndex(0);
    }
}

function showControls()
{
    if( !g_controlsShown )
    {
        g_controlsShown = true;
        $('.idle_fade_out').fadeIn();
    }
    else if( !$('.idle_fade_out').is(':animated') )
    {
        $('.idle_fade_out').show();
        $('.idle_fade_out').css("opacity",1.0);
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
        hideTooltip();
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
    hideTooltip();
    hideVolume();
    hideContentPage();
    hideAllTabs();
    g_bottomOpen = true;
    $('#bottom_container').stop();
    $('#bottom_container').animate({ height: '275px' });
}

function closeBottom(animate)
{
    hideTooltip();
    hideVolume();
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

function clickPhotoMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#photo_list').show();
    g_mediaContent = "photo";
    scrollPhotoListToIndex();
}
function clickMusicMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#music_list').show();
    g_mediaContent = "music";
}
function clickVideoMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#video_list').show();
    g_mediaContent = "video";
    scrollVideoToIndex();
}

function clickShareButton()
{
    $('#social_content .social_item').hide();
    $('#social_share').show();
    g_socialContent = "share";
}
function clickEmailButton()
{
    $('#social_content .social_item').hide();
    $('#social_email').show();
    g_socialContent = "email";
}
function clickTwitterButton()
{
    $('#social_content .social_item').hide();
    $('#social_twitter').show();
    g_socialContent = "twitter";
}
function clickFacebookButton()
{
    $('#social_content .social_item').hide();
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

function playerPlayPause()
{
    hideTooltip();
    if( g_playerMode == "music" )
        musicPlayPause();
    else if( g_playerMode == "video" )
        videoPlayPause();
}
function playerPrevious()
{
    if( g_playerMode == "music" )
        musicPrevious();
    else if( g_playerMode == "video" )
        videoPrevious();
    else if( g_playerMode == "photo" )
        photoPrevious();
}
function playerNext()
{
    if( g_playerMode == "music" )
        musicNext();
    else if( g_playerMode == "video" )
        videoNext();
    else if( g_playerMode == "photo" )
        photoNext();
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
    if( g_playerMode == "music" )
        musicVolume(vol_ratio);
    else if( g_playerMode == "video" )
        videoVolume(vol_ratio);
    else if( g_playerMode == "photo" )
        photoVolume(vol_ratio);
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

    console.log(vol_ratio);
}
function volumeDragStop(event,ui)
{
    volumeDrag(event,ui);
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
    var percent = curr_time / total_time * 100.0;
    var time = formatMinSeconds(curr_time) + " / " + formatMinSeconds(total_time);
    $('#track_progress').html(time);
    $('#track_current_bar').css('width',percent + "%");
}
function playerPhotoInfo(name,location,listens)
{
    $('#media_controls').hide();
    $('#photo_info').show();
    $('#photo_info .name').html(name);
    $('#photo_info .location').html(location);
}

function playerUpdateTotalViewCount()
{
    $('#total_view_count').html(g_totalPageViews);
}

function playerTrackInfo(track_name,listens)
{
    $('#photo_info').hide();
    $('#media_controls').show();

    if( track_name )
        $('#track_name').html(track_name);
    $('#track_play_count').html(listens);
}
function playerSetPaused()
{
    $('#track_play_pause_button').removeClass('playing');
}
function playerSetPlaying()
{
    $('#track_play_pause_button').addClass('playing');
}


function getAnchorMap()
{
    var anchor_map = {};
    var anchor = self.document.location.hash.substring(1);
    var anchor_elements = anchor.split('&');
    var g_anchor_map = {};
    for( var k in anchor_elements )
    {
        var e = anchor_elements[k];
        var k_v = e.split('=');
        
        k = unescape(k_v[0]);
        if( k_v.length > 1 )
            anchor_map[k] = unescape(k_v[1]);
        else
            anchor_map[k] = true;
    }
    return anchor_map;
}
