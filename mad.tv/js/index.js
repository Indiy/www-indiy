
var PROGRESS_BAR_WIDTH = 534 - 2;
var PROGRESS_ROUND_LENGTH = PROGRESS_BAR_WIDTH - 6;

var g_videoHistory = false;
var g_genreList = false;
var g_controlsShown = false;
var g_hideControlsTimeout = false;
var g_touchDevice = false;
var g_genreHistory = false;

function setupVideoPlayer()
{
    if( !('ontouchstart' in document) )
    {
        $('body').addClass('no_touch');
        g_touchDevice = false;
    }
    else
    {
        g_touchDevice = true;
    }
    //g_touchDevice = true;
    //$(document).mousemove(showAndTimeoutControls)

    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(m,k,v){vars[k] = v;});
    
    if( 'genre_id' in vars )
        g_genre_id = vars['genre_id'];

    $(window).resize(onWindowResize);
    loadSteamInfo(startVideoInProgress);
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
$(document).ready(setupVideoPlayer);

function showControls()
{
    if( !g_controlsShown )
    {
        g_controlsShown = true;
        $("#overlay_container").fadeIn();
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
}
function timeoutControls()
{
    clearTimeoutControls();
    if( g_playing )
        g_hideControlsTimeout = window.setTimeout(hideControls,2000);
}
function hideControls()
{
    g_controlsShown = false;
    $("#overlay_container").fadeOut();
}

function loadSteamInfo(callback)
{
    jQuery.ajax(
    {
        type: 'GET',
        url: "/data/stream_info.php?genre_id=" + g_genre_id,
        dataType: 'json',
        success: function(data) 
        {
            g_videoHistory = data.history[g_genre_id];
            g_genreList = data.genre_list;
            g_genreHistory = data.history;
            callback();
        },
        error: function()
        {
            //window.alert("Error!");
        }
    });
}
function startVideoInProgress()
{
    createVideoTag();
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
            g_videoPlayer[0].play();
        }
        else
        {
            loadSteamInfo(updateVideoElementInProgress);
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
        if( g_touchDevice )
        {
            g_videoPlayer[0].pause();
        }
        else
        {
            g_videoPlayer.pause();
        }
    }
    catch(e) {}
    showControls();
    $('#player .play').addClass('paused');
}
var g_historyShown = false;
function toggleHistory()
{
    if( g_historyShown )
        hideHistory();
    else
        showHistory();
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
    if( !g_videoHistory )
        return;

    $('#history .content').empty();
    for( var i = 1 ; i < Math.min(g_videoHistory.length,4) ; ++i )
    {
        var track = g_videoHistory[i];
        var title = track.title;
        var duration = mins_secs(track.duration);
        var love = "";
        if( title in g_loveMap )
            love = "love";
        
        var img = "<img onerror='$(this).hide();' src='{0}'>".format(track.logo);
        var html = "";
        html += "<div class='row'>";
        html += " <div class='icon'>{0}</div>".format(img);
        html += " <div class='title'>" + title + "</div>";
        html += " <div class='length'>" + duration + "</div>";
        html += " <div id='history_loved_" + i + "' class='loved " + love + "'>";
        html += "  <div class='love_icon' onclick='toggleLoveHistory(this," + i + ");'>";
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
        $('#history .content').append(html);
    }
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
    updateHistory();
    updateVideoDisplay();

    calcVideoProgress();
    

    if( g_touchDevice )
    {
        createVideoTagForTouch();
    }
    else
    {
        createVideoTagVideoJS();
    }
}
function calcVideoProgress()
{
    var video = g_videoHistory[0];
    var time_progress = Math.floor((new Date().getTime())/1000 - video.start_time);
    if( time_progress > video.duration * 0.9 )
        time_progress = Math.floor(video.duration * 0.9);
    g_seekOnPlay = time_progress;
}
function createVideoTagVideoJS()
{
    var h = $('#video_container').height();
    var w = $('#video_container').width();
    
    var video = g_videoHistory[0];
    var title = video.title;
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");
    
    $('#track_title').text(title);
    if( title in g_loveMap )
        $('#player .heart').addClass('love');
    else
        $('#player .heart').removeClass('love');
    
    var w_h = " width='" + w + "' height='" + h + "' ";
    
    var html = '';
    html += '<video id="madtv_player" ' + w_h + ' class="video-js vjs-default-skin" preload="auto">';
    html += '<source src="' + url + '" type="video/mp4" />';
    html += '<source src="' + url_ogv + '" type="video/ogg" />';
    html += '</video>';
    
    $('#video_container').empty();
    $('#video_container').html(html);
    g_videoPlayer = _V_('madtv_player');
    g_videoPlayer.ready(onVideoReadyVideoJS);

}
function createVideoTagForTouch()
{
    var h = $('#video_container').height();
    var w = $('#video_container').width();
    
    var video = g_videoHistory[0];
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");
    
    var html = '';
    html += "<video id='madtv_player' width='{0}' height='{1}' src='{2}' preload='metadata' >"
    .format(w,h,url);
    html += "</video>";
    
    $('#video_container').empty();
    $('#video_container').html(html);    
    g_videoPlayer = $("video");
    g_videoPlayer.ready(onVideoReadyTouch);
}

function updateVideoDisplay()
{
    var video = g_videoHistory[0];
    var title = video.title;
    $('#track_title').text(title);
    if( title in g_loveMap )
        $('#player .heart').addClass('love');
    else
        $('#player .heart').removeClass('love');
}
function updateVideoElementInProgress()
{
    updateVideoElement(true);
    calcVideoProgress();
}
function updateVideoElement(delay_play)
{
    g_videoHistory = g_genreHistory[g_genre_id];
    var video = g_videoHistory[0];
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");

    updateVideoDisplay();
    if( g_touchDevice )
    {
        g_videoPlayer.attr('src',url);
        g_videoPlayer[0].play();
    }
    else
    {
        var media = [
             { type: "video/mp4", src: url },
             { type: "video/ogg", src: url_ogv }
        ];
        g_videoPlayer.src(media);
        g_videoPlayer.play();
    }
}

var g_seekOnPlay = false;
function onVideoReadyTouch()
{
    g_videoPlayer.on("loadstart",videoLoadStart);
    g_videoPlayer.on("play",videoPlayStarted);
    g_videoPlayer.on("timeupdate",videoTimeUpdate);
    g_videoPlayer.on("ended",videoEnded);
    g_videoPlayer.on("durationchange",videoDurationChange);
    g_videoPlayer.on("progress",videoDownloadProgress);
}
function onVideoReadyVideoJS()
{
    g_videoPlayer.addEvent("loadstart",videoLoadStart);
    g_videoPlayer.addEvent("play",videoPlayStarted);
    g_videoPlayer.addEvent("timeupdate",videoTimeUpdate);
    g_videoPlayer.addEvent("ended",videoEnded);
    g_videoPlayer.addEvent("durationchange",videoDurationChange);
    g_videoPlayer.addEvent("progress",videoDownloadProgress);
    
    g_videoPlayer.play();
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
    seekVideo();
    videoProgress();
}
function videoDurationChange()
{
    seekVideo();
    videoProgress();
}
function videoPlayStarted()
{
    g_playing = true;
    $('#player .play').removeClass('paused');
    timeoutControls();
}
function videoProgress()
{
    var curr_pos = getCurrentTime();
    var duration = getDuration();
    
    var s = mins_secs(curr_pos) 
    if( duration )
        s += " - " + mins_secs(duration);
    if( $('#track_duration').text() != s )
        $('#track_duration').text(s);
    
    var percent = 0;
    if( duration > 0 )
        var percent = curr_pos/duration;
    var width = percent * PROGRESS_BAR_WIDTH;
    $('#player .progress .bar').width(width);
    if( width >= PROGRESS_ROUND_LENGTH )
        $('#player .progress .bar').css('border-radius','6px 6px 6px 6px');
    else
        $('#player .progress .bar').css('border-radius','6px 0px 0px 6px');
    
}
function videoEnded()
{
    loadSteamInfo(updateVideoElement);
}

var g_lastSeek = 0;
function seekVideo()
{
    if( g_seekOnPlay !== false )
    {
        var curr_time = new Date().getTime();
        var delta = curr_time - g_lastSeek;
        if( delta < 100 )
        {
            window.setTimeout(seekVideo,100);
            return;
        }
        g_lastSeek = curr_time;
    
        var pos = getCurrentTime() + 1;
        if( pos >= g_seekOnPlay )
            g_seekOnPlay = false;
        else
            setCurrentTime(g_seekOnPlay);
    }
}

function setCurrentTime(new_time)
{
    if( g_touchDevice )
        g_videoPlayer[0].currentTime = new_time;
    else
        g_videoPlayer.currentTime(new_time);
}
function getCurrentTime()
{
    if( g_touchDevice )
        return g_videoPlayer[0].currentTime;
    else
        return g_videoPlayer.currentTime();
}
function getDuration()
{
    if( g_touchDevice )
        return g_videoPlayer[0].duration
    else
        return g_videoPlayer.duration();
}

function onWindowResize()
{
    if( g_videoPlayer )
    {
        var h = $('#video_container').height();
        var w = $('#video_container').width();
        if( g_touchDevice )
        {
            g_videoPlayer.width(w);
            g_videoPlayer.height(h);
        }
        else
        {
            g_videoPlayer.size(w,h);
        }
    }
}




