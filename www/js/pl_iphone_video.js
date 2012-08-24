
var g_videoLeftIndex = 0;
var g_videoPlayer = false;
var g_videoVolRatio = 1.0;
var g_videoReady = false;
var g_videoPlayIndexOnReady = false;

$(document).ready(videoOnReady);
function videoOnReady()
{
    if( g_videoList.length > 0 )
    {
        var opts = {
            panelCount: g_videoList.length,
            resizeCallback: videoResizeBackgrounds,
            onPanelChange: videoPanelChange,
            onPanelVisible: videoPanelVisible,
            onReady: videoSwipeReady
        };
        $('#video_bg').swipe(opts);
    }
}

function videoSwipeReady()
{
    videoCreateTag();
}

function videoPanelVisible(index)
{
    var video = g_videoList[index];
    imageLoadItem(video,index,'#video_bg');
}

function videoPanelChange(index)
{
    var video = g_videoList[index];
    imageLoadItem(video,index,'#video_bg');
    
    g_videoCurrentIndex = index;
    var video = g_videoList[index];
    window.location.hash = '#video_id=' + video.id;
    
    playerTrackInfo(video.name,video.views);
}

function videoResizeBackgrounds()
{
    imageResizeBackgrounds(g_videoList,'#video_bg');
}

function videoHide()
{
    $('#big_play_button').hide();
    $('#video_container').hide();
    $('#video_bg').hide();
}
function videoShow()
{
    $('#big_play_button').show();
    $('#video_container').show();
    $('#video_bg').show();
}
function videoPlay()
{
    var video = g_videoList[g_videoCurrentIndex];

    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");
    
    var media = [
                 { type: "video/mp4", src: url },
                 { type: "video/ogg", src: url_ogv }
                 ];

    g_videoPlayer.src(media);
    g_videoPlayer.play();
    videoUpdateViews(video.id,g_videoCurrentIndex);
}


var g_videoCurrentIndex = 0;

function videoChangeId(video_id)
{
    for( var i = 0 ; i < g_videoList.length ; ++i )
    {
        var video = g_videoList[i];
        if( video.id == video_id )
        {
            videoChangeIndex(i);
            return;
        }
    }
}

function videoChangeIndex(index,animate)
{
    if( animate !== false )
        animate = true;

    if( !g_videoReady )
    {
        g_videoPlayIndexOnReady = index;
        return;
    }
    g_videoPlayIndexOnReady = false;
    
    setPlayerMode("video");

    $('#video_bg').swipe('scrollto',index,animate);
}
function videoNext()
{
    var next = g_videoCurrentIndex + 1;
    if( next >= g_videoList.length )
        next = 0;
    videoChangeIndex(next);
}
function videoPrevious()
{
    var next = g_videoCurrentIndex - 1;
    if( next < 0 )
        next = g_videoList.length  - 1;
    videoChangeIndex(next);
}
function videoVolume(vol_ratio)
{
}

function onVideoReady()
{
    g_videoPlayer.addEvent("loadstart",videoLoadStart);
    g_videoPlayer.addEvent("play",videoPlayStarted);
    g_videoPlayer.addEvent("timeupdate",videoTimeUpdate);
    g_videoPlayer.addEvent("ended",videoEnded);
    g_videoPlayer.addEvent("durationchange",videoDurationChange);
    g_videoPlayer.addEvent("progress",videoDownloadProgress);
    
    g_videoReady = true;
    if( g_videoPlayIndexOnReady !== false )
    {
        videoChangeIndex(g_videoPlayIndexOnReady);
    }
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
    videoProgress();
}
function videoDurationChange()
{
    videoProgress();
}
function videoPlayStarted()
{
    //playerSetPlaying();
}
function videoProgress()
{
    var curr_pos = g_videoPlayer.currentTime();
    var total_time = g_videoPlayer.duration();

    //playerProgress(curr_pos,total_time);
}
function videoEnded()
{
    //playerSetPaused();
    videoNext();
}

function videoCreateTag()
{
    var h = $('#video_container').height();
    var w = $('#video_container').width();
    
    var video = g_videoList[0];
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");
    var image = video.image;
    
    var w_h = " width='" + w + "' height='" + h + "' ";
    
    var html = "";
    html += "<video id='video_player' " + w_h + " class='video-js vjs-default-skin' preload='auto' poster='" + image + "'>";
    html += "<source src='" + url + "' type='video/mp4' />";
    html += "<source src='" + url_ogv + "' type='video/ogg' />";
    html += "</video>";
    
    $('#video_container').empty();
    $('#video_container').html(html);
    g_videoPlayer = _V_('video_player');
    g_videoPlayer.ready(onVideoReady);
}
