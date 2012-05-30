
var g_videoLeftIndex = 0;
var g_videoPlayer = false;
var g_videoPlaying = false;

$(document).ready(videoOnReady);
function videoOnReady()
{
    if( g_videoList.length > 0 )
    {
        videoCreateTag();
        scrollVideoToIndex();
        
        $(window).resize(videoOnWindowResize);
        $(window).resize(scrollVideoToIndex);
    }
}

function clickVideoIcon()
{
    clickMediaIcon("video",clickVideoMediaButton);
    scrollVideoToIndex();
}

function videoHide()
{
    if( g_videoPlayer )
    {
        videoPause();
    }
    $('#video_container').hide();
}
function videoShow()
{
    $('#video_container').show();
}
function videoPlayPause()
{
    if( g_videoPlaying )
    {
        videoPause();
    }
    else
    {
        g_videoPlayer.play();
    }
}
function videoPause()
{
    g_videoPlaying = false;
    g_videoPlayer.pause();
    playerSetPaused();
}

function videoListScrollLeft()
{
    if( g_videoLeftIndex > 0 )
    {
        g_videoLeftIndex -= 3;
        if( g_videoLeftIndex < 0 )
            g_videoLeftIndex = 0;
        scrollVideoToIndex(true);
    }
}

function videoListScrollRight()
{
    var max_left = g_videoList.length - 3;
    
    if( g_videoLeftIndex <= max_left )
    {
        g_videoLeftIndex += 3;
        if( g_videoLeftIndex > max_left )
            g_videoLeftIndex = max_left;
        scrollVideoToIndex(true);
    }
}

function scrollVideoToIndex(animate)
{
    var img_w = $('#video_list .content .item .picture img').width();
    var img_h = img_w/1.4;
    $('#video_list .content .item .picture img').css('height',img_h + 'px');

    var content_height = $('#video_list .content').height();
    var max_h = 0;

    $('#video_list .content .item').each(function() 
    {
        var h = $(this).height();
        max_h = Math.max(h,max_h);
    });

    var margin = (content_height - max_h)/2 + 10;
    $('#video_list .content .item').css('margin-top',margin + "px");

    var sel = '#video_list .item:eq({0})'.format(g_videoLeftIndex);
    var curr_scroll = $('#video_list .content').scrollLeft();
    var dest = curr_scroll + $(sel).position().left;
    if( animate === true )
        $('#video_list .content').animate({scrollLeft: dest});
    else
        $('#video_list .content').scrollLeft(dest);
}

var g_videoCurrentIndex = 0;

function videoPlayIndex(index)
{
    g_videoCurrentIndex = index;
    setPlayerMode("video");
    
    var video = g_videoList[index];
    
    loveChangedVideo(video.id,video.name);
    
    playerTrackInfo(video.name,video.views);
    videoUpdateViews(video.id,index);
    
    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");

    var media = [
                 { type: "video/mp4", src: url },
                 { type: "video/ogg", src: url_ogv }
                 ];

    g_videoPlayer.src(media);
    g_videoPlayer.play();
}
function videoNext()
{
    var next = g_videoCurrentIndex + 1;
    if( next >= g_videoList.length )
        next = 0;
    videoPlayIndex(next);
}
function videoPrevious()
{
    var next = g_videoCurrentIndex - 1;
    if( next < 0)
        next = g_videoCurrentIndex - 1;
    videoPlayIndex(next);
}

function onVideoReady()
{
    g_videoPlayer.addEvent("loadstart",videoLoadStart);
    g_videoPlayer.addEvent("play",videoPlayStarted);
    g_videoPlayer.addEvent("timeupdate",videoTimeUpdate);
    g_videoPlayer.addEvent("ended",videoEnded);
    g_videoPlayer.addEvent("durationchange",videoDurationChange);
    g_videoPlayer.addEvent("progress",videoDownloadProgress);
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
    g_videoPlaying = true;
    playerSetPlaying();
}
function videoProgress()
{
    var curr_pos = g_videoPlayer.currentTime();
    var total_time = g_videoPlayer.duration();

    playerProgress(curr_pos,total_time);
}
function videoEnded()
{
    g_videoPlaying = false;
    playerSetPaused();
    videoNext();
}

function videoOnWindowResize()
{
    if( g_videoPlayer )
    {
        var h = $('#video_container').height();
        var w = $('#video_container').width();
        g_videoPlayer.size(w,h);
    }
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

var g_videoViewsUpdated = {};
function videoUpdateViews(id,index)
{
    if( id in g_videoViewsUpdated )
        return false;

    g_videoViewsUpdated[id] = true;

    var url = "/data/element_views.php?video_id=" + id;
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            g_totalPageViews = data['total_views'];
            var element_views = data['element_views'];
            g_videoList[index].views = element_views;
            playerUpdateTotalViewCount();
            playerTrackInfo(false,element_views);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}



