
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
    
    $("#music_list").scrollbar();
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

function musicPanelVisible(index)
{
    var song = g_musicList[index];
    musicLoadImage(song,index);
}
function musicPanelChange(index)
{
    g_songsPlayed++;
    if( g_songsPlayed == 3 )
        maybeAskForEmail();
    
    g_currentSongIndex = index;
    var song = g_musicList[index];
    
    loveChangedMusic(song.id,song.name);
    
    var media = {
        mp3: song.mp3,
        oga: song.mp3.replace(".mp3",".ogg")
    };
    $('#jquery_jplayer').jPlayer("setMedia", media);
    playerProgress(0,2*60);
    if( g_musicIsPlaying )
        $('#jquery_jplayer').jPlayer("play");

    musicLoadImage(song,index);
    
    g_currentSongId = song.id;
    window.location.hash = '#song_id=' + g_currentSongId; 
    
    playerTrackInfo(song.name,song.listens);
    
    if( musicUpdateListens(song.id,index) )
    {
        g_totalPageViews++;
        playerUpdateTotalViewCount();
    }
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

function musicPlayPause()
{
    if( g_musicIsPlaying )
        $('#jquery_jplayer').jPlayer("pause");
    else
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
            musicChange(i);
            return;
        }
    }
}

function musicChange( index ) 
{
    if( !g_musicPlayerReady )
    {
        g_musicStartIndex = index;
        return;
    }

    setPlayerMode("music");
    volumeSetLevel(g_musicVolRatio);

    $('#music_bg').swipe('scrollto',index);
}

function musicNext()
{
    var index = g_currentSongIndex + 1;
    if( index == g_musicList.length )
        index = 0;
    
    musicChange(index);
}
function musicPrevious()
{
    var index = g_currentSongIndex - 1;
    if( index < 0 )
        index = g_musicList.length - 1;
    
    musicChange(index);
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

function musicToggleLoveIndex(index)
{
    var song = g_musicList[index];
    toggleLoveMusic(song.id);
    musicUpdatePlaylistLove();
}

function musicUpdatePlaylistLove()
{
    for( var i = 0 ; i < g_musicList.length ; ++i )
    {
        var song = g_musicList[i];
        
        if( isMusicLoved(song.id) )
        {
            $('#song_playlist_' + i).addClass('loved');
        }
        else
        {
            $('#song_playlist_' + i).removeClass('loved');            
        }
    }
}

var g_freeDownloadIndex = false;
function clickFreeDownload(index)
{
    g_freeDownloadIndex = index;
    if( g_fanEmail )
    {
        doFreeDownload();
    }
    else
    {
        $('#submit_email_popup #email').val("");
        $('#popup_mask').show();
        $('#submit_email_popup').show();
    }
}
function closePopup()
{
    $('#popup_mask').hide();
    $('#submit_email_popup').hide();
}

function onSubmitEmail()
{
    var email = $('#submit_email_popup #email').val();
    if( email.length > 0 && email.match(EMAIL_REGEX) )
    {
        closePopup();
        g_fanEmail = email;
        doFreeDownload();
    }
    else
    {
        window.alert("Please enter a valid email address.");
    }
}

function doFreeDownload()
{
    var song = g_musicList[g_freeDownloadIndex];
    g_freeDownloadIndex = false;
    
    var email = escape(g_fanEmail);
    var url = "/download.php?artist={0}&id={1}&email={2}".format(g_artistId,song.id,email);
    window.location.href = url;
}

