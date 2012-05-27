
g_musicIsPlaying = false;

$(document).ready(musicOnReady);

function musicOnReady()
{

    $('#jquery_jplayer').jPlayer({
        ready: jplayerReady,
        solution: "html, flash",
        supplied: "mp3, oga",
        swfPath: "/js/Jplayer.swf",
        verticalVolume: true,
        wmode: "window"
    })
    .bind($.jPlayer.event.ended,jplayerEnded)
    .bind($.jPlayer.event.timeupdate,jplayerTimeUpdate)
    .bind($.jPlayer.event.play,jplayerPlay)
    .bind($.jPlayer.event.pause,jplayerPause)
    .bind($.jPlayer.event.volumechange,jplayerVolume);
     
    //window.setTimeout(preloadImages,1000);
    //$(window).resize(resizeBackgrounds);
}

function musicHide()
{
    $('#jquery_jplayer').jPlayer("stop");
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
    var vol = event.jPlayer.options.volume;
    //$('#player .volume .current').css('height',vol * 100 + "%");
}

function musicPlayPause()
{
    if( g_musicIsPlaying )
        $('#jquery_jplayer').jPlayer("pause");
    else
        $('#jquery_jplayer').jPlayer("play");
}

function jplayerReady() 
{
    musicChange(0);
}

var g_songsPlayed = 0;
var g_currentSongId = 0;
var g_currentSongIndex = 0;

function musicChange( index ) 
{
    setPlayerMode("music");

    g_songsPlayed++;
    if( g_songsPlayed == 3 )
        maybeAskForEmail();
    
    g_currentSongIndex = index;
    var song = g_musicList[index];
    //$("#playlist .song_list_item").removeClass("current");
    //$("#playlist #song_list_item_" + song.id).addClass("current");
    
    var media = {
        mp3: song.mp3,
        oga: song.mp3.replace(".mp3",".ogg")
    };
    if( song.mp3.endsWith("mp3") )
    {
        $('#jquery_jplayer').jPlayer("setMedia", media);
        $('#jquery_jplayer').jPlayer("play");
    }
    else
    {
        $('#jquery_jplayer').jPlayer("stop");
    }
    
    g_currentSongId = song.id;
    window.location.hash = '#song_id=' + g_currentSongId; 
    
    playerTrackInfo(song.name,song.listens);
    
    if( musicUpdateListens(song.id,index) )
    {
        g_totalListens++;
        playerUpdateTotalViewCount();
    }
}

function musicNext()
{
    var index = g_currentSongIndex + 1;
    if( index == g_musicList.length )
        index = 0;
    
    musicChange(index);
}


var g_musicListenUpdated = {};
function musicUpdateListens(song_id,index)
{
    if( song_id in g_musicListenUpdated )
        return false;

    g_musicListenUpdated[song_id] = true;

    var url = "/data/listens.php?song_id=" + song_id;
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            g_totalListens = data['total_listens'];
            var track_listens = data['track_listens'];
            g_musicList[index].listens = track_listens;
            playerUpdateTotalViewCount();
            playerTrackInfo(false,track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}




