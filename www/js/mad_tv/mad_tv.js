
if( !Date.now )
{
    Date.now = function now()
    {
        return new Date().getTime();
    };
}

var PROGRESS_BAR_WIDTH = 534 - 2;
var PROGRESS_ROUND_LENGTH = PROGRESS_BAR_WIDTH - 6;

var MAX_BROWSER_TIME_DELTA_MS = 10*1000;

var MAX_SEEK_FREQUENCY = 2*1000;
var MIN_SEEK_MS = 10*1000;

var LOOP_MS = 7*24*60*60*1000;
var MAX_HISTORY_LEN = 10;

var g_videoHistoryList = [];
var g_genreList = false;
var g_controlsShown = false;
var g_hideControlsTimeout = false;
var g_touchDevice = false;
var g_genreHistory = false;
var g_currentVideo = false;
var g_backgroundList = [];
var g_currentPlaylistIndex = 0;
var g_currentPlaylist = false;
var g_updateOnFirstTick = false;

var g_noPlayerUI = false;

function setupVideoPlayer()
{
    g_currentPlaylist = g_playlistList[g_currentPlaylistIndex];

    if( !('ontouchstart' in document) )
    {
        $('body').addClass('no_touch');
        g_touchDevice = false;
    }
    else
    {
        g_touchDevice = true;
    }
    if( g_touchDevice && IS_PHONE )
    {
        g_noPlayerUI = true;
    }

    //g_touchDevice = true;
    //$(document).mousemove(showAndTimeoutControls)

    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(m,k,v){vars[k] = v;});
    
    if( 'genre_id' in vars )
    {
        g_genre_id = vars['genre_id'];
    }

    $(window).resize(onWindowResize);

    createVideoTag();

    if( g_templateParams.splash_bg )
    {
        g_backgroundList = [ g_templateParams.splash_bg ];

        imageLoadItem(g_backgroundList[0],0,'#splash_bg');
        splashResize();
        $(window).resize(splashResize);
    }
}
$(document).ready(setupVideoPlayer);

function splashResize()
{
    imageResizeBackgrounds(g_backgroundList,'#splash_bg');
}

function choosePlaylist(i)
{
    if( g_touchDevice )
    {
        g_videoPlayer.play();
        if( i != g_currentPlaylistIndex )
        {
            g_videoHistoryList = [];
            g_currentPlaylist = g_playlistList[i];
            g_updateOnFirstTick = true;
        }
    }
    else
    {
        g_videoHistoryList = [];
        g_currentPlaylist = g_playlistList[i];
        updateVideoElement(true);
    }

    if( !g_noPlayerUI )
    {
        $('.splash_item').hide();
        $('.player_item').show();
        if( g_touchDevice )
        {
            $(document).on("touchstart",showControls);
            $(document).on("touchend",timeoutControls);
        }
        else
        {
            $(document).mousemove(showAndTimeoutControls);
        }
        showControls();
    }

    g_currentPlaylistIndex = i;
}

function showControls()
{
    if( !g_noPlayerUI )
    {
        if( !g_controlsShown )
        {
            g_controlsShown = true;
            $(".idle_hide").fadeIn();
        }
        clearTimeoutControls();
    }
}
function showAndTimeoutControls()
{
    if( !g_noPlayerUI )
    {
        showControls();
        timeoutControls();
    }
}
function clearTimeoutControls()
{
    if( !g_noPlayerUI )
    {
        if( g_hideControlsTimeout !== false )
        {
            window.clearTimeout(g_hideControlsTimeout);
            g_hideControlsTimeout = false;
        }
    }
}
function timeoutControls()
{
    if( !g_noPlayerUI )
    {
        clearTimeoutControls();
        if( g_playing )
        {
            g_hideControlsTimeout = window.setTimeout(hideControls,2000);
        }
    }
}
function hideControls()
{
    if( !g_noPlayerUI )
    {
        g_controlsShown = false;
        $(".idle_hide").fadeOut();
    }
}

function mins_secs(secs)
{
    var mins = Math.floor(secs / 60);
    secs -= mins * 60;
    return sprintf("%02d:%02d",mins,secs); 
}


var g_playing = false;
function playerToggle()
{
    if( g_playing )
        playerPause();
    else
        playerPlay();
}
function playerPlay()
{
    g_playing = true;
    try 
    {
        if( g_touchDevice )
        {
            g_videoPlayer.play();
        }
        else
        {
            updateVideoElement();
        }
    }
    catch(e) {}
    $('#player .play').removeClass('paused');
}
function playerPause()
{
    g_playing = false;
    try 
    {
        g_videoPlayer.pause();
    }
    catch(e) {}
    showControls();
    $('#player .play').addClass('paused');
}
var g_historyShown = false;
function toggleHistory()
{
    if( g_historyShown )
    {
        hideHistory();
    }
    else
    {
        showHistory();
    }
}
function showHistory()
{
    hideGenrePicker();
    g_historyShown = true;
    $('#history').fadeIn();
    updateHistory();
}
function hideHistory()
{
    g_historyShown = false;
    $('#history').fadeOut();
}
function updateHistory()
{
    var video_list = getPreviousVideoList();

    $('#history .content').empty();
    var html = "";
    for( var i = 0 ; i < video_list.length ; ++i )
    {
        var video = video_list[i];
        var title = video.title;
        var duration = mins_secs(video.durationSec);

        var love = loveIsLoved(title) ? "love" : "";
        
        var img = "<img onerror='$(this).hide();' src='{0}'>".format(video.logo);

        html += "<div class='row'>";
        html += " <div class='icon'>{0}</div>".format(img);
        html += " <div class='title'>" + title + "</div>";
        html += " <div class='length'>" + duration + "</div>";
        html += " <div id='history_loved_" + i + "' class='loved " + love + "'>";
        html += "  <div class='love_icon' onclick='toggleLoveHistory(this,{0},{1});'>".format(i,title);
        html += "   <div class='tooltip love_tip'>";
        html += "    <div class='carrot'></div>";
        html += "    LOVE";
        html += "   </div>";
        html += "   <div class='tooltip unlove_tip'>";
        html += "    <div class='carrot'></div>";
        html += "    UNLOVE";
        html += "   </div>";
        html += "  </div>";
        html += " </div>";
        html += "</div>";
    }
    $('#history .content').html(html);
}
function showAddVideo()
{
    hideGenrePicker();
    $('#add_video').fadeIn();
}
function hideAddVideo()
{
    $('#add_video').fadeOut();
}

var g_videoPlayer = false;
function createVideoTag()
{
    updateVideoDisplay();

    var h = $('#video_container').height();
    var w = $('#video_container').width();
    
    var video = getCurrentVideo();
    g_currentVideo = video;
    var url = video.video_file;
    var url_ogv = false;
    if( video.video_extra && video.video_extra.alts && video.video_extra.alts.ogv )
    {
        url_ogv = g_artistFileBaseUrl + video.video_extra.alts.ogv;
    }

    var html = "";
    if( g_touchDevice )
    {
        html += "<video id='madtv_player' width='{0}' height='{1}' src='{2}' preload='metadata'>".format(w,h,url);
    }
    else
    {
        html += "<video id='madtv_player' width='{0}' height='{1}' class='video-js vjs-default-skin' preload='auto'>".format(w,h);
        html += "<source src='{0}' type='video/mp4' />".format(url);
        if( url_ogv )
        {
            html += "<source src='{0}' type='video/ogg' />".format(url_ogv);
        }
    }
    html += "</video>";
    
    $('#video_container').empty();
    $('#video_container').html(html);
    if( g_touchDevice )
    {
        g_videoPlayer = $("video")[0];
        $("video").ready(onVideoReadyTouch);
    }
    else
    {
        g_videoPlayer = _V_('madtv_player');
        g_videoPlayer.ready(onVideoReadyVideoJS);
    }
}

function updateVideoDisplay()
{
    var video = getCurrentVideo();
    var title = video.title;
    $('#track_title').text(title);
    if( loveIsLoved(title) )
    {
        $('#player .heart').addClass('love');
    }
    else
    {
        $('#player .heart').removeClass('love');
    }
}
function updateVideoElement(no_inhibit_seek)
{
    var video = getCurrentVideo();
    g_currentVideo = video;
    debugLog("updateVideoElement: now:",Date.now(),"new video:",video);

    var url = video.video_file;
    var url_ogv = false;
    if( video.video_extra && video.video_extra.alts && video.video_extra.alts.ogv )
    {
        url_ogv = g_artistFileBaseUrl + video.video_extra.alts.ogv;
    }

    updateVideoDisplay();

    if( !no_inhibit_seek )
    {
        inhibitSeek();
    }
    if( g_touchDevice )
    {
        g_videoPlayer.src = url;
        g_videoPlayer.play();
    }
    else
    {
        var media = [ { type: "video/mp4", src: url } ];
        if( url_ogv )
        {
            media.push({ type: "video/ogg", src: url_ogv });
        }
        g_videoPlayer.src(media);
        g_videoPlayer.play();
    }
}

var g_seekOnPlay = false;
function onVideoReadyTouch()
{
    var vp = $("video");

    vp.on("loadstart",videoLoadStart);
    vp.on("play",videoPlayStarted);
    vp.on("timeupdate",videoTimeUpdate);
    vp.on("ended",videoEnded);
    vp.on("durationchange",videoDurationChange);
    vp.on("progress",videoDownloadProgress);
}
function onVideoReadyVideoJS()
{
    g_videoPlayer.addEvent("loadstart",videoLoadStart);
    g_videoPlayer.addEvent("play",videoPlayStarted);
    g_videoPlayer.addEvent("timeupdate",videoTimeUpdate);
    g_videoPlayer.addEvent("ended",videoEnded);
    g_videoPlayer.addEvent("durationchange",videoDurationChange);
    g_videoPlayer.addEvent("progress",videoDownloadProgress);
    
    //g_videoPlayer.play();
}
function videoLoadStart()
{
    //seekVideo();
}
function videoDownloadProgress()
{
    //seekVideo();
}
function videoTimeUpdate()
{
    maybeSeekVideo();
    videoProgress();
    maybeChangeVideo();
}
function videoDurationChange()
{
    maybeSeekVideo();
    videoProgress();
    maybeChangeVideo();
}
function videoPlayStarted()
{
    g_playing = true;
    $('#player .play').removeClass('paused');
    timeoutControls();
    maybeChangeVideo();
}
function videoProgress()
{
    var curr_pos = getCurrentTime();
    var duration = getDuration();
    
    var s = mins_secs(curr_pos) 
    if( duration )
    {
        s += " - " + mins_secs(duration);
    }
    if( $('#track_duration').text() != s )
    {
        $('#track_duration').text(s);
    }
    var percent = 0;
    if( duration > 0 )
    {
        percent = curr_pos/duration;
    }
    var width = percent * PROGRESS_BAR_WIDTH;
    $('#player .progress .bar').width(width);
    if( width >= PROGRESS_ROUND_LENGTH )
    {
        $('#player .progress .bar').css('border-radius','6px 6px 6px 6px');
    }
    else
    {
        $('#player .progress .bar').css('border-radius','6px 0px 0px 6px');
    }
}
function videoEnded()
{
    debugLog("videoEnded");
    updateVideoElement();
}


var g_lastSeek = 0;
function maybeSeekVideo()
{
    var video = getCurrentVideo();
    if( g_currentVideo == video )
    {
        var time_ms = getCorrectTime();

        var pos_ms = getCurrentTime() * 1000;

        var video_time_ms = video.startTimeMS + pos_ms;

        var video_delta_ms = time_ms - video_time_ms;
        if( Math.abs(video_delta_ms) > MIN_SEEK_MS )
        {
            var now = Date.now();
            var seek_delta = now - g_lastSeek;

            if( seek_delta < MAX_SEEK_FREQUENCY )
            {
                debugLog("would have seeked, but waiting for MAX_SEEK_FREQENCY");
            }
            else
            {
                g_lastSeek = now;
                var seek_secs = (video_delta_ms + pos_ms) / 1000;
                if( seek_secs > video.durationSec )
                {
                    seek_secs = video.durationSec - 2;
                }
                else if( seek_secs < 0 )
                {
                    seek_secs = 0;
                }
                debugLog("seek to secs:",seek_secs,
                    "pos_ms:",pos_ms,
                    "video.startTimeMS:",video.startTimeMS,
                    "video_delta_ms:",video_delta_ms);
                setCurrentTime(seek_secs);
            }
        }
    }
}
function maybeChangeVideo()
{
    if( g_updateOnFirstTick )
    {
        g_updateOnFirstTick = false;
        updateVideoElement(true);
    }
}

function inhibitSeek()
{
    g_lastSeek = Date.now();
}

function setCurrentTime(new_time)
{
    if( g_touchDevice )
    {
        g_videoPlayer.currentTime = new_time;
    }
    else
    {
        g_videoPlayer.currentTime(new_time);
    }
}
function getCurrentTime()
{
    if( g_touchDevice )
    {
        return g_videoPlayer.currentTime;
    }
    else
    {
        return g_videoPlayer.currentTime();
    }
}
function getDuration()
{
    if( g_touchDevice )
    {
        return g_videoPlayer.duration
    }
    else
    {
        return g_videoPlayer.duration();
    }
}

function onWindowResize()
{
    if( g_videoPlayer )
    {
        var h = $('#video_container').height();
        var w = $('#video_container').width();
        if( g_touchDevice )
        {
            g_videoPlayer.width = w;
            g_videoPlayer.height = h;
        }
        else
        {
            g_videoPlayer.size(w,h);
        }
    }
}


function getCorrectTime()
{
    var delta = g_browserTimeMS - g_serverTimeMS;
    if( Math.abs(delta) < MAX_BROWSER_TIME_DELTA_MS )
    {
        delta = 0;
    }
    return Date.now() - delta;
}

function getCurrentVideo()
{
    var ret = null;
    calcVideoHistory();
    if( g_videoHistoryList.length > 0 )
    {
        ret = g_videoHistoryList[0]
    }
    return ret;
}

function getPreviousVideoList()
{
    calcVideoHistory();

    return g_videoHistoryList.slice(1,4);
}


function calcVideoHistory()
{
    var now_ms = getCorrectTime();
    var next_index = 0;
    var video_list = g_currentPlaylist.items;

    if( video_list.length == 0 )
    {
        return;
    }

    var next_start_ms = Math.floor(now_ms / LOOP_MS) * LOOP_MS;

    var log = function() {};
    if( g_videoHistoryList.length > 0 )
    {
        log = debugLog;
    }

    while(1)
    {
        if( g_videoHistoryList.length > 0 )
        {
            var video = g_videoHistoryList[0];
            if( video.endTimeMS > now_ms )
            {
                break;
            }
            next_index = video.index + 1;
            next_start_ms = video.endTimeMS;
        }

        if( next_index > video_list.length - 1 )
        {
            next_index = 0;
        }

        var next_video = video_list[next_index];
        var durationSec = Math.floor(next_video.media_length);
        var startTimeMS = next_start_ms;
        var endTimeMS = startTimeMS + durationSec * 1000 - 1000;
        next_start_ms = endTimeMS;

        var item = {
            startTimeMS: startTimeMS,
            endTimeMS: endTimeMS,
            index: next_index,
            title: next_video.name,
            durationSec: durationSec,
            image: next_video.image,
            image_extra: next_video.image_extra,
            video_file: next_video.video_file,
            video_extra: next_video.video_extra
        };
        g_videoHistoryList.unshift(item);
        log("added:",item);

        if( g_videoHistoryList.length > MAX_HISTORY_LEN )
        {
            g_videoHistoryList.length = MAX_HISTORY_LEN;
        }
    }
}

