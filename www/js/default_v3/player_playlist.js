

function playlistReady()
{
    var html = "";
    for( var i = 0 ; i < g_playlistList.length ; ++i )
    {
        var playlist = g_playlistList[i];
        if( playlist.type == 'DIR' )
        {
            for( var j = 0 ; j < playlist.items.length ; ++j )
            {
                html = getImageHolders(playlist.items[j]);
                $('body').prepend(html);
                
                setupSwipe(playlist.items[j]);
            }
        }
        else
        {
            html = getImageHolders(playlist);
            $('body').prepend(html);
            
            setupSwipe(playlist);
        }
    }
    setupJplayer();
}
$(document).ready(playlistReady);

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
    html += " <div id='video_container'></div>";
    html += "</div>";
    return html;
}
function setupSwipe(playlist)
{
    var sel = "#playlist_bg_{0}".format(playlist.playlist_id);
    var opts = {
        panelCount: playlist.items.length,
        resizeCallback: makeCallback(playlistResizeBackgrounds,playlist),
        onPanelChange: makeCallback(playlistPanelChange,playlist),
        onPanelVisible: makeCallback(playlistPanelVisible,playlist),
        onReady: makeCallback(playlistSwipeReady,playlist)
    };
    $(sel).swipe(opts);
}
function makeCallback(callback,arg1)
{
    return function(arg2) { return callback(arg1,arg2); };
}

function clickPlaylist(index)
{
    var playlist = g_playlistList[index];
    
    $('#playlist_tab .item_column.playlist').children().removeClass('active');
    $('#playlist_tab .item_column.playlist').children().addClass('inactive');

    var sel = "#playlist_tab .item_column.playlist #item_{0}".format(index);
    $(sel).removeClass('inactive');
    $(sel).addClass('active');
    
    if( playlist.type == 'DIR' )
    {
        $('#playlist_tab .item_column.dir').empty();
        $('#playlist_tab .item_column.media').empty();
        
        for( var i = 0 ; i < playlist.items.length ; ++i )
        {
            var pi = playlist.items[i];
            var image_url = getImgUrlWithWidth(pi,233);
            
            var html = "";
            html += "<div id='item_{1}' class='item inactive' onclick='clickPlaylistDirItem({0},{1});'>".format(index,i);
            html += " <img src='{0}'/>".format(image_url);
            html += " <div class='overlay'></div>";
            html += " <div class='name'>{0}</div>".format(pi.name);
            html += "</div>";
            
            $('#playlist_tab .item_column.dir').append(html);
        }
    }
}

function clickPlaylistDirItem(playlist_index,child_playlist_index)
{
    var playlist = g_playlistList[playlist_index];
    var child_playlist = playlist.items[child_playlist_index];
    
    $('#playlist_tab .item_column.dir').children().removeClass('active');
    $('#playlist_tab .item_column.dir').children().addClass('inactive');

    var sel = "#playlist_tab .item_column.dir #item_{0}".format(child_playlist_index);
    $(sel).removeClass('inactive');
    $(sel).addClass('active');
    
    $('#playlist_tab .item_column.media').empty();
    for( var i = 0 ; i < child_playlist.items.length ; ++i )
    {
        var pi = child_playlist.items[i];
        var image_url = getImgUrlWithWidth(pi,233);
        
        var tup = "{0},{1},{2}".format(playlist_index,child_playlist_index,i);
        
        var html = "";
        html += "<div id='item_{1}' class='item inactive' onclick='clickPlaylistMediaItem({0});'>".format(tup,i);
        html += " <img src='{0}'/>".format(image_url);
        html += " <div class='overlay'></div>";
        html += " <div class='name'>{0}</div>".format(pi.name);
        html += "</div>";
        
        $('#playlist_tab .item_column.media').append(html);
    }
}

var g_currentPlaylist = false;
var g_currentPlaylistIndex = 0;

function clickPlaylistMediaItem(playlist_index,child_playlist_index,playlist_item_index)
{
    hideAllTabs();
    var playlist = g_playlistList[playlist_index];
    
    if( typeof playlist_item_index !== 'indefined' )
    {
        playlist = playlist.items[child_playlist_index];
    }
    else
    {
        playlist_item_index = child_playlist_index;
    }
    var playlist_item = playlist[playlist_item_index];
    
    g_currentPlaylist = playlist;
    
    $('.full_screen_bg').hide();
    var sel = "#playlist_bg_{0}".format(playlist.playlist_id);
    $(sel).show();

    currentPlaylistChangeIndex(playlist_item_index);
}

function currentPlaylistChangeIndex(index)
{
    var sel = "#playlist_bg_{0}".format(g_currentPlaylist.playlist_id);
    $(sel).swipe('scrollto',index);
}

function playlistPanelVisible(playlist,index)
{
    playlistLoadImage(playlist,index);
}
function playlistPanelChange(playlist,index)
{
    console.log("playlistPanelChange: " + index);
    g_currentPlaylistIndex = index;
    
    playlistLoadImage(playlist,index);
    
    var playlist_item = playlist.items[index];
    
    var media_type = playlist_item.media_type;
    var media_extra = playlist_item.media_extra;
    
    if( media_type == 'NONE' )
    {
        playerPhotoInfo(playlist_item.name,playlist_item.location,playlist_item.views);
    }
    else if( media_type == 'AUDIO' )
    {
        playerTrackInfo(playlist_item.name,playlist_item.views);
        
        var media = {
            mp3: playlist_item.media_url
        };
        if( playlist_item.audio_extra && playlist_item.audio_extra.alts && playlist_item.audio_extra.alts.ogg )
        {
            media.oga = g_artistFileBaseUrl + playlist_item.audio_extra.alts.ogg;
        }
        $('#jquery_jplayer').jPlayer("setMedia", media);
        if( g_mediaAutoStart )
        {
            $('#jquery_jplayer').jPlayer("play");
        }
        // Just inhibit the first play
        g_mediaAutoStart = true;
    }
    else if( media_type == 'VIDEO' )
    {
        playerTrackInfo(playlist_item.name,playlist_item.views);
    }
    
    if( media_extra && media_extra.media_length )
    {
        playerProgress(0,media_extra.media_length);
    }
    else
    {
        playerProgress(0,0);
    }
    
    
    /*
    g_songsPlayed++;
    if( g_songsPlayed == 3 )
        maybeAskForEmail();
    
    g_currentSongIndex = index;
    var song = g_musicList[index];
    
    loveChangedMusic(song.id,song.name);
    
    var media = {
        mp3: song.mp3
    };
    
    if( song.audio_extra && song.audio_extra.alts && song.audio_extra.alts.ogg )
    {
        media.oga = g_artistFileBaseUrl + song.audio_extra.alts.ogg;
    }
    
    $('#jquery_jplayer').jPlayer("setMedia", media);
    playerProgress(0,0);
    
    if( g_mediaAutoStart )
    {
        $('#jquery_jplayer').jPlayer("play");
    }
    // Just inhibit the first play
    g_mediaAutoStart = true;

    playlistLoadImage(g_currentPlaylist,index);
    
    g_currentSongId = song.id;
    
    updateAnchorMedia({ song_id: song.id });
    //commentChangedMedia('song',song.id);
    
    playerTrackInfo(song.name,song.views);
    
    if( musicUpdateListens(song.id,index) )
    {
        playerUpdateTotalViewCount(g_totalPageViews + 1);
    }
    */
}
function playlistLoadImage(playlist,index)
{
    var sel = "#playlist_bg_{0}".format(playlist.playlist_id);
    imageLoadItem(playlist.items[index],index,sel);
}

function playlistResizeBackgrounds(playlist)
{
    var sel = "#playlist_bg_{0}".format(playlist.playlist_id);
    imageResizeBackgrounds(playlist.items,sel);
}

function playlistSwipeReady(playlist)
{
    console.log("playlistSwipeReady");
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

function setupJplayer()
{
    $('#jquery_jplayer').jPlayer({
                                 ready: jplayerReady,
                                 solution: "html, flash",
                                 supplied: "mp3, oga",
                                 swfPath: "/js/Jplayer.swf",
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
    
    if( g_musicStartIndex !== false )
    {
        if( IS_IOS )
            g_musicIsPlaying = false;
        else
            g_musicIsPlaying = true;
        musicChange(g_musicStartIndex);
        var vol_ratio = 0.8;
        volumeSetLevel(vol_ratio);
    }
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
    musicNext();
}
function jplayerVolume(event)
{
    var vol_ratio = event.jPlayer.options.volume;
    volumeSetLevel(vol_ratio);
}



