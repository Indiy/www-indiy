
var g_videoLeftIndex = 0;
var g_videoPlayer = false;
var g_videoPlaying = false;
var g_videoVolRatio = 1.0;
var g_videoReady = false;
var g_playIndexOnReady = false;

$(document).ready(videoOnReady);
function videoOnReady()
{
    if( g_videoList.length > 0 )
    {
        scrollVideoListToIndex();
        $(window).resize(scrollVideoListToIndex);
        
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
    volumeSetLevel(g_videoVolRatio);
    var video = g_videoList[index];

    updateAnchorMedia({ video_id: video.id });
    commentChangedMedia('video',video.id);
    
    loveChangedVideo(video.id,video.name);
    
    playerTrackInfo(video.name,video.views);
    videoUpdateViews(video.id,index);
    
    var left_sl = $('#video_bg').scrollLeft();
    $('#video_container').css({left: left_sl });
    $('#video_container').show();

    var url = video.video_file;
    var url_ogv = url.replace(".mp4",".ogv");

    if( 'video_data' in video )
    {
        var window_height = $(window).height();
    
        var res_list = false;
        if( _V_.isFF() )
        {
            res_list = video.video_data.ogv;
        }
        else
        {
            res_list = video.video_data.mp4;
        }

        $("#quality_popup .size").removeClass("current");

        var all_res_list = [ 1080, 720, 480, 360, 240 ];
        var auto_res = false;
        var res_count = 0;
        for( var i = 0 ; i < all_res_list.length ; ++i )
        {
            var res = all_res_list[i];
            if( res in res_list )
            {
                res_count++;
                if( auto_res == false )
                {
                    auto_res = res;
                }
                else if( res > window_height )
                {
                    auto_res = res;
                }
            }
            else
            {
                $("#quality_popup .size" + res).hide();
            }
        }
        
        $("#quality_popup .size" + auto_res).addClass("current");
        if( res_count > 1 )
        {
            $('#video_bitrate').show();
            $('#video_bitrate').html(auto_res + "p");
        }
        else
        {
            $('#video_bitrate').hide();
        }
        
        if( auto_res )
        {
            var mp4 = video.video_data.mp4[auto_res];
            var ogv = video.video_data.ogv[auto_res];
        
            url = "{0}/artists/files/{1}".format(g_trueSiteUrl,mp4);
            url_ogv = "{0}/artists/files/{1}".format(g_trueSiteUrl,ogv);
        }
    }
    else
    {
        $('#video_bitrate').hide();
    }
    
    var media = [
                 { type: "video/mp4", src: url },
                 { type: "video/ogg", src: url_ogv }
                 ];
    
    g_videoPlayer.src(media);
    
    if( g_mediaAutoStart )
    {
        g_videoPlayer.play();
        $('#video_container').show();
    }
    else
    {
        $('#video_container').hide();
    }
    // Just inhibit the first play
    g_mediaAutoStart = true;
    videoResizeBackgrounds();
}

function videoResizeBackgrounds()
{
    imageResizeBackgrounds(g_videoList,'#video_bg');
    
    var left_sl = $('#video_bg').scrollLeft();
    $('#video_container').css({left: left_sl });

    videoOnWindowResize();
}

function videoHide()
{
    if( g_videoPlayer )
    {
        videoPause();
    }
    $('#video_container').hide();
    $('#video_bg').hide();
}
function videoShow()
{
    //$('#video_container').show();
    $('#video_bg').show();
    if( g_videoList.length < 2 )
    {
        $('.player_nav_button').addClass("hidden");
    }
    else
    {
        $('.player_nav_button').removeClass("hidden");
    }

}
function videoPlayPause()
{
    if( g_videoPlaying )
    {
        videoPause();
    }
    else
    {
        $('#video_container').show();
        $('#big_play_icon').hide();
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
        scrollVideoListToIndex(true);
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
        scrollVideoListToIndex(true);
    }
}

function scrollVideoListToIndex(animate)
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

function videoChangeId(video_id)
{
    for( var i = 0 ; i < g_videoList.length ; ++i )
    {
        var video = g_videoList[i];
        if( video.id == video_id )
        {
            videoPlayIndex(i);
            return;
        }
    }
}

function videoPlayIndex(index,animate)
{
    if( animate === false )
        animate = false;
    else
        animate = true;

    setPlayerMode("video");

    if( !g_videoReady )
    {
        g_playIndexOnReady = index;
        return;
    }
    g_playIndexOnReady = false;
    
    $('#video_bg').swipe('scrollto',index,animate);

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
    if( next < 0 )
        next = g_videoList.length  - 1;
    videoPlayIndex(next);
}
function videoVolume(vol_ratio)
{
    g_videoPlayer.volume(vol_ratio);
    g_videoVolRatio = vol_ratio;
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
    if( g_playIndexOnReady !== false )
    {
        videoPlayIndex(g_playIndexOnReady,false);
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
    g_videoPlaying = true;
    playerSetPlaying();
    
    videoOnWindowResize();
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
    $('#video_container video').bind('contextmenu', function(e) { return false; });
}



