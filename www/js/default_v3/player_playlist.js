
var g_videoIsPlaying = false;
var g_musicIsPlaying = false;
var g_videoPlayerReady = false;
var g_musicPlayerReady = false;
var g_videoContainers = 0;
var g_currentPlaylist = false;
var g_currentPlaylistIndex = 0;
var g_volRatio = 0.8;
var g_mediaAutoStart = true;
var g_swipeReadyCount = 0;
var g_swipeCount = 0;

function playlistReady()
{
    $('#playlist_tab').scrollbar();

    g_videoContainers = 0;
    var html = "";
    for( var i = 0 ; i < g_playlistList.length ; ++i )
    {
        var playlist = g_playlistList[i];
        if( playlist.type == 'DIR' )
        {
            for( var j = 0 ; j < playlist.items.length ; ++j )
            {
                var child_playlist = playlist.items[j];
                setupPlaylist(child_playlist);
            }
        }
        else
        {
            setupPlaylist(playlist);
        }
    }
    
    if( g_videoContainers == 0 )
    {
        // Dont need video
        g_videoPlayerReady = true;
    }
    
    if( IS_VERY_OLD_IE )
    {
        // Old IE videojs doesnt fire events right
        g_videoPlayerReady = true;
    }
    
    setupJplayer();
    
    $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange',function()
    {
        console.log("webkitfullscreenchange");
    });
}
$(document).ready(playlistReady);

function setupPlaylist(playlist)
{
    var sel = "#playlist_bg_{0}".format(playlist.playlist_id);
    playlist.bg_sel = sel;
    playlist.video_container_sel = sel + " .video_container";
    
    var html = getImageHolders(playlist);
    $('body').prepend(html);

    setupSwipe(playlist);
    if( maybeVideoCreateTag(playlist) )
    {
        ++g_videoContainers;
    }
}

function getImageHolders(playlist)
{
    var html = "";
    
    html += "<div id='playlist_bg_{0}' class='full_screen_bg'>".format(playlist.playlist_id);
    html += " <div class='pad'></div>";
    for( var i = 0 ; i < playlist.items.length ; ++i )
    {
        html += " <div id='image_holder_{0}' class='image_holder'></div>".format(i);
    }
    html += " <div class='pad'></div>";
    html += " <div class='video_container'></div>";
    html += "</div>";
    return html;
}
function setupSwipe(playlist)
{
    g_swipeCount++;
    var sel = playlist.bg_sel;
    var opts = {
        panelCount: playlist.items.length,
        resizeCallback: makeCallback(swipeResizeBackgrounds,playlist),
        onPanelChange: makeCallback(swipePanelChange,playlist),
        onPanelVisible: makeCallback(swipePanelVisible,playlist),
        onReady: makeCallback(swipeReady,playlist)
    };
    $(sel).swipe(opts);
}
function makeCallback(callback,arg1)
{
    var f = function(arg2,arg3,arg4) {
        return callback(this,arg1,arg2,arg3,arg4);
    };
    return f;
}

function playlistChangePlaylist(new_playlist,playlist_item_index)
{
    var old_playlist = g_currentPlaylist;
    if( old_playlist.video_player )
    {
        old_playlist.video_player.pause();
        $(old_playlist.video_container_sel).hide();
    }
    
    g_currentPlaylist = new_playlist;
    $('.full_screen_bg').hide();
    $(new_playlist.bg_sel).show();
    
    if( new_playlist.items.length < 2 )
    {
        $('#player_prev').addClass('hidden');
        $('#player_next').addClass('hidden');
    }
    else
    {
        $('#player_prev').removeClass('hidden');
        $('#player_next').removeClass('hidden');
    }

    currentPlaylistChangeIndex(playlist_item_index,false);
}

function currentPlaylistChangeIndex(index,animate)
{
    if( animate !== false )
    {
        animate = true;
    }
    var sel = g_currentPlaylist.bg_sel;
    $(sel).swipe('scrollto',index,animate);
}

function swipePanelVisible(that,playlist,index)
{
    playlistLoadImage(playlist,index);
}
function swipePanelChange(that,playlist,index)
{
    g_currentPlaylistIndex = index;
    
    playlistLoadImage(playlist,index);
    
    var playlist_item = playlist.items[index];
    
    var media_type = playlist_item.media_type;
    var media_extra = playlist_item.media_extra;
    
    if( media_type == 'AUDIO' )
    {
        if( playlist.video_player )
        {
            playlist.video_player.pause();
            $(playlist.video_container_sel).hide();
        }
        
        playerTrackInfo(playlist_item.name,playlist_item.views);
        
        var media = {
            mp3: playlist_item.media_url
        };
        if( playlist_item.media_extra && playlist_item.media_extra.alts && playlist_item.media_extra.alts.ogg )
        {
            media.oga = g_artistFileBaseUrl + playlist_item.audio_extra.alts.ogg;
        }
        $('#jquery_jplayer').jPlayer("setMedia", media);
        if( g_mediaAutoStart )
        {
            $('#jquery_jplayer').jPlayer("play");
        }
    }
    else if( media_type == 'VIDEO' )
    {
        $('#jquery_jplayer').jPlayer("pause");
        
        playerTrackInfo(playlist_item.name,playlist_item.views);
        
        var left_sl = $(playlist.bg_sel).scrollLeft();
        $(playlist.video_container_sel).css({left: left_sl });
        $(playlist.video_container_sel).show();

        var url = playlist_item.video_file;
        
        var media = [ { type: "video/mp4", src: url } ];
    
        if( playlist_item.video_extra && playlist_item.video_extra.alts && playlist_item.video_extra.alts.ogv )
        {
            var url_ogv = g_artistFileBaseUrl + playlist_item.video_extra.alts.ogv;
            media.push( { type: "video/ogg", src: url_ogv } );
        }
        
        playlist.video_player.src(media);
        
        if( g_mediaAutoStart )
        {
            playlist.video_player.play();
            $(playlist.video_container_sel).show();
        }
        else
        {
            $(playlist.video_container_sel).hide();
        }
        videoOnWindowResize(playlist);
    }
    else
    {
        if( playlist.video_player )
        {
            playlist.video_player.pause();
            $(playlist.video_container_sel).hide();
        }
        if( playlist_item.iframe_code )
        {
            $('#jquery_jplayer').jPlayer("pause");
        }
        
        playerPhotoInfo(playlist_item.name,playlist_item.location,playlist_item.views);
    }
    
    
    if( media_extra && media_extra.media_length )
    {
        playerProgress(0,media_extra.media_length);
    }
    else
    {
        playerProgress(0,0);
    }
    // Just inhibit the first play
    g_mediaAutoStart = true;

    genericUpdateViews('media',playlist_item.playlist_item_id,playlist_item);
    playlistSetVolume(g_volRatio);
}
function playlistLoadImage(playlist,index)
{
    var sel = playlist.bg_sel;
    imageLoadItem(playlist.items[index],index,sel);
}

function swipeResizeBackgrounds(that,playlist)
{
    var sel = playlist.bg_sel;
    imageResizeBackgrounds(playlist.items,sel);
    videoOnWindowResize(playlist);

    if( playlist.bg_sel && playlist.video_container_sel )
    {
        var left_sl = $(playlist.bg_sel).scrollLeft();
        $(playlist.video_container_sel).css({left: left_sl });
    }
}

function swipeReady(playlist)
{
    g_swipeReadyCount++;
    maybeAudioAndVideoAndSwipeReady();
    
}
function playlistNext()
{
    var index = g_currentPlaylistIndex + 1;
    if( index == g_currentPlaylist.items.length )
        index = 0;
    
    currentPlaylistChangeIndex(index);
}
function playlistPrevious()
{
    var index = g_currentPlaylistIndex - 1;
    if( index < 0 )
        index = g_currentPlaylist.items.length - 1;
    
    currentPlaylistChangeIndex(index);
}
function playlistPlayPause()
{
    hideTooltip();
    var playlist = g_currentPlaylist;
    var playlist_item = playlist.items[g_currentPlaylistIndex];
    var media_type = playlist_item.media_type;
    
    if( media_type == 'AUDIO' )
    {
        if( g_musicIsPlaying )
        {
            $('#jquery_jplayer').jPlayer("pause");
        }
        else
        {
            $('#jquery_jplayer').jPlayer("play");
        }
    }
    else if( media_type == 'VIDEO' )
    {
        if( g_videoIsPlaying )
        {
            playlist.video_player.pause();
        }
        else
        {
            $(playlist.video_container_sel).show();
            $(playlist.video_container_sel + " video").show();
            playlist.video_player.play();
            $('#big_play_icon').hide();
            videoCheckFullscreenLater();
        }
    }
}
function playlistSeek(seek_ratio)
{
    var playlist_item = g_currentPlaylist.items[g_currentPlaylistIndex];
    var media_type = playlist_item.media_type;
    
    if( media_type == 'AUDIO' )
    {
        $('#jquery_jplayer').jPlayer( "playHead", seek_ratio * 100 );
    }
    else if( media_type == 'VIDEO' )
    {
        var video_player = g_currentPlaylist.video_player;
        var seek_secs = seek_ratio * video_player.duration();
        video_player.currentTime(seek_secs);
    }
}
function playlistSetVolume(vol_ratio)
{
    $('#jquery_jplayer').jPlayer("volume",vol_ratio);
    if( g_currentPlaylist.video_player )
    {
        g_currentPlaylist.video_player.volume(vol_ratio);
    }
    g_volRatio = vol_ratio;
}

function setupJplayer()
{
    $('#jquery_jplayer').jPlayer({
                                 ready: jplayerReady,
                                 solution: "html, flash",
                                 supplied: "mp3, oga",
                                 swfPath: g_jplayerSWF,
                                 verticalVolume: true,
                                 wmode: "window",
                                 volume: 0.8
                                 })
    .bind($.jPlayer.event.ended,jplayerEnded)
    .bind($.jPlayer.event.timeupdate,jplayerTimeUpdate)
    .bind($.jPlayer.event.play,jplayerPlay)
    .bind($.jPlayer.event.pause,jplayerPause)
    .bind($.jPlayer.event.volumechange,jplayerVolume);
}
var g_musicStartIndex = false;
function jplayerReady() 
{
    g_musicPlayerReady = true;
    
    maybeAudioAndVideoAndSwipeReady();
}
function jplayerTimeUpdate(event)
{
    //var percent = event.jPlayer.status.currentPercentAbsolute;
    var total_time = event.jPlayer.status.duration;
    var curr_time = event.jPlayer.status.currentTime;
    
    playerProgress(curr_time,total_time);
}
var g_musicIsPlaying = false;
function jplayerPlay()
{
    g_musicIsPlaying = true;
    playerSetPlaying();
}
function jplayerPause()
{
    g_musicIsPlaying = false;    
    playerSetPaused();
}
function jplayerEnded()
{
    g_musicIsPlaying = false;
    playerSetPaused();
    playlistNext();
}
function jplayerVolume(event)
{
    var vol_ratio = event.jPlayer.options.volume;
    volumeSetLevel(vol_ratio);
}
var g_autoStart = true;
function maybeAudioAndVideoAndSwipeReady()
{
    if( !g_autoStart )
    {
        return;
    }

    if( g_musicPlayerReady && g_videoPlayerReady )
    {
        var vol_ratio = 0.8;
        volumeSetLevel(vol_ratio);
        
        var playlist = g_playlistList[0];
        if( playlist.type == 'DIR' )
        {
            catalogClickPlaylistMediaItem(0,0,0);
        }
        else
        {
            catalogClickPlaylistMediaItem(0,0);
        }
        g_autoStart = false;
    }
}

function videoOnWindowResize(playlist)
{
    if( playlist.video_player )
    {
        var h = $(playlist.video_container_sel).height();
        var w = $(playlist.video_container_sel).width();
        playlist.video_player.size(w,h);
    }
}
function onVideoReady(that,playlist)
{
    that.addEvent("loadstart",makeCallback(videoLoadStart,playlist));
    that.addEvent("play",makeCallback(videoPlayStarted,playlist));
    that.addEvent("pause",makeCallback(videoPaused,playlist));
    that.addEvent("timeupdate",makeCallback(videoTimeUpdate,playlist));
    that.addEvent("ended",makeCallback(videoEnded,playlist));
    that.addEvent("durationchange",makeCallback(videoDurationChange,playlist));
    that.addEvent("progress",makeCallback(videoDownloadProgress,playlist));
    
    g_videoPlayerReady = true;
    maybeAudioAndVideoAndSwipeReady();
}
function videoLoadStart(that,playlist)
{
    console.log("videoLoadStart");
    //seekVideo();
}
function videoDownloadProgress(that,playlist)
{
    //seekVideo();
}
function videoTimeUpdate(that,playlist)
{
    videoProgress(that,playlist);
}
function videoDurationChange(that,playlist)
{
    videoProgress(that,playlist);
}
function videoPlayStarted(that,playlist)
{
    console.log("videoPlayStarted");
    g_videoIsPlaying = true;
    playerSetPlaying();
    
    videoOnWindowResize(playlist);
}
function videoPaused(that,playlist)
{
    console.log("videoPaused");

    g_videoIsPlaying = false;
    playerSetPaused();
    videoCheckFullscreen();
}
function videoProgress(that,playlist)
{
    var curr_pos = that.currentTime();
    var total_time = that.duration();

    playerProgress(curr_pos,total_time);
}
function videoEnded(that,playlist)
{
    console.log("videoEnded");

    g_videoIsPlaying = false;
    playerSetPaused();
    videoCheckFullscreen();
    playlistNext();
}
function videoCheckFullscreen()
{
    if( IS_IPHONE || IS_IPOD )
    {
        var playlist = g_currentPlaylist;
        var sel = playlist.video_container_sel;
        
        var is_fullscreen = $(sel + " video")[0].webkitDisplayingFullscreen;
        if( !is_fullscreen )
        {
            $(sel + " video").hide();
            $(sel).hide();
            $('#big_play_icon').show();
        }
        else
        {
            videoCheckFullscreenLater();
        }
    }
}
var g_videoCheckFullscreenTimeout = false;
function videoCheckFullscreenLater()
{
    if( g_videoCheckFullscreenTimeout !== false )
    {
        window.clearTimeout(g_videoCheckFullscreenTimeout);
        g_videoCheckFullscreenTimeout = false;
    }
    g_videoCheckFullscreenTimeout = window.setTimeout(videoCheckFullscreen,500);
}

function maybeVideoCreateTag(playlist)
{
    var video = false;
    for( var i = 0 ; i < playlist.items.length ; ++i )
    {
        var pi = playlist.items[i];
        if( pi.media_type == 'VIDEO' )
        {
            video = pi;
        }
    }
    
    if( video === false )
    {
        return 0;
    }

    var sel = playlist.bg_sel;
    
    var h = $(sel + ' .video_container').height();
    var w = $(sel + ' .video_container').width();
    
    var url = video.video_file;
    var url_ogv = false;
    if( video.video_extra && video.video_extra.alts && video.video_extra.alts.ogv )
    {
        url_ogv = g_artistFileBaseUrl + video.video_extra.alts.ogv;
    }
    
    var w_h = " width='" + w + "' height='" + h + "' ";
    
    var video_sel = "video_player_{0}".format(playlist.playlist_id);
    playlist.video_sel = "#" + video_sel;
    
    var html = "";
    html += "<video id='{0}' {1} class='video-js vjs-default-skin' preload='auto'>".format(video_sel,w_h);
    html += " <source src='{0}' type='video/mp4' />".format(url);
    if( url_ogv )
    {
        html += " <source src='{0}' type='video/ogg' />".format(url_ogv);
    }
    html += "</video>";
    
    $(sel + ' .video_container').empty();
    $(sel + ' .video_container').html(html);
    playlist.video_player = _V_(video_sel);
    playlist.video_player.ready(makeCallback(onVideoReady,playlist));
    return 1;
}


