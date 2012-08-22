
var MUSIC_IMAGE_PRELOAD_TIMEOUT = 3000;

var g_musicIsPlaying = false;
var g_musicVolRatio = 0.8;
var g_musicPlayerReady = false;
var g_musicStartIndex = false;

$(document).ready(musicOnReady);

function musicOnReady()
{
    if( g_musicList.length == 0 )
        return;
     
    window.setTimeout(musicPreloadImages,MUSIC_IMAGE_PRELOAD_TIMEOUT);
    
    var opts = {
        panelCount: g_musicList.length,
        resizeCallback: musicResizeBackgrounds,
        onPanelChange: musicPanelChange,
        onPanelVisible: musicPanelVisible,
        onReady: musicSwipeReady
    };
    $('#music_bg').swipe(opts);
}

function musicSwipeReady()
{
    setupJplayer();
}

function setupJplayer()
{
    var opts = {
        ready: jplayerReady,
        solution: "html, flash",
        supplied: "mp3, oga",
        swfPath: "/js/Jplayer.swf",
        verticalVolume: true,
        wmode: "window",
        volume: 0.8
    };

    var player = $('#jquery_jplayer').jPlayer(opts);
    player.bind($.jPlayer.event.ended,jplayerEnded);
    player.bind($.jPlayer.event.timeupdate,jplayerTimeUpdate);
    player.bind($.jPlayer.event.play,jplayerPlay);
    player.bind($.jPlayer.event.pause,jplayerPause);
    player.bind($.jPlayer.event.volumechange,jplayerVolume);
}

function jplayerReady() 
{
    g_musicPlayerReady = true;
    
    if( g_musicStartIndex !== false )
    {
        if( IS_IOS )
            g_musicIsPlaying = false;
        else
            g_musicIsPlaying = true;
        musicChangeIndex(g_musicStartIndex);
    }
}

function musicPanelVisible(index)
{
    var song = g_musicList[index];
    musicLoadImage(song,index);
}
function musicPanelChange(index)
{
    g_currentSongIndex = index;
    var song = g_musicList[index];
    
    var media = {
        mp3: song.mp3,
        oga: song.mp3.replace(".mp3",".ogg")
    };
    $('#jquery_jplayer').jPlayer("setMedia", media);
    if( g_musicIsPlaying )
        $('#jquery_jplayer').jPlayer("play");

    musicLoadImage(song,index);
    
    g_currentSongId = song.id;
    window.location.hash = '#song_id=' + g_currentSongId; 
    
    playerTrackInfo(song.name,song.listens);
    
    musicUpdateListens(song.id,index);
}

function musicHide()
{
    $('#music_bg').hide();
    $('#jquery_jplayer').jPlayer("stop");
}

function musicShow()
{
    $('#music_bg').show();
}

function jplayerTimeUpdate(event)
{
    //var percent = event.jPlayer.status.currentPercentAbsolute;
    var total_time = event.jPlayer.status.duration;
    var curr_time = event.jPlayer.status.currentTime;
    
    //playerProgress(curr_time,total_time);
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

function musicPlayPause()
{
    if( g_musicIsPlaying )
        $('#jquery_jplayer').jPlayer("pause");
    else
        $('#jquery_jplayer').jPlayer("play");
}
function musicPlay()
{
    if( !g_musicIsPlaying )
        $('#jquery_jplayer').jPlayer("play");
}


var g_songsPlayed = 0;
var g_currentSongId = 0;
var g_currentSongIndex = 0;

function musicChangeId( song_id )
{
    for( var i = 0 ; i < g_musicList.length ; ++i )
    {
        var song = g_musicList[i];
        if( song.id == song_id )
        {
            musicChangeIndex(i);
            return;
        }
    }
}

function musicChangeIndex( index ) 
{
    if( !g_musicPlayerReady )
    {
        g_musicStartIndex = index;
        return;
    }

    setPlayerMode("music");

    $('#music_bg').swipe('scrollto',index);
}

function musicNext()
{
    var index = g_currentSongIndex + 1;
    if( index == g_musicList.length )
        index = 0;
    
    musicChangeIndex(index);
}
function musicPrevious()
{
    var index = g_currentSongIndex - 1;
    if( index < 0 )
        index = g_musicList.length - 1;
    
    musicChangeIndex(index);
}
function musicVolume(vol_ratio)
{
    $('#jquery_jplayer').jPlayer("volume",vol_ratio);
    g_musicVolRatio = vol_ratio;
}

function musicPreloadImages()
{
    for( var i = 0 ; i < g_musicList.length ; ++i  )
    {
        var song = g_musicList[i];
        musicLoadImage(song,i);
    }
}

function musicLoadImage(song,index)
{
    imageLoadItem(song,index,'#music_bg');
}

function musicResizeBackgrounds()
{
    imageResizeBackgrounds(g_musicList,'#music_bg');
}


var g_musicListenUpdated = {};
function musicUpdateListens(song_id,index)
{
    if( song_id in g_musicListenUpdated )
        return false;

    g_musicListenUpdated[song_id] = true;

    var url = "/data/element_views.php?song_id=" + song_id;
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}

