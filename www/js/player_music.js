
g_playerIsPlaying = false;

function setupAudioPlayer()
{
    $('#playlist .song_list').lionbars(/*{ autohide: true }*/);
    $('#playlist .lb-wrap').css('height','200px');

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

function formatMinSeconds(seconds)
{
    seconds = Math.floor(seconds);
    var mins = Math.floor(seconds / 60);
    var seconds = seconds % 60;
    var seconds_string = '';
    if( seconds < 10 )
        seconds_string += "0";
    seconds_string += seconds;
    return mins + ":" + seconds_string;
}
function jplayerTimeUpdate(event)
{
    var percent = event.jPlayer.status.currentPercentAbsolute;
    var total_time = event.jPlayer.status.duration;
    var curr_time = event.jPlayer.status.currentTime;
    
    var time = formatMinSeconds(curr_time) + " / " + formatMinSeconds(total_time);
    $('#track_progress').html(time);
    $('#track_current_bar').css('width',percent + "%");
}
var g_playerIsPlaying = false;
function jplayerPlay()
{
    g_playerIsPlaying = true;
    $('#track_play_pause_button').addClass('playing');
}
function jplayerPause()
{
    g_playerIsPlaying = false;    
    $('#track_play_pause_button').removeClass('playing');
}
function jplayerEnded()
{
    g_playerIsPlaying = false;
    $('#track_play_pause_button').removeClass('playing');
    playListNext();
}
function jplayerVolume(event)
{
    var vol = event.jPlayer.options.volume;
    $('#player .volume .current').css('height',vol * 100 + "%");
}

function playerPlayPause()
{
    if( g_playerIsPlaying )
        $('#jquery_jplayer').jPlayer("pause");
    else
        $('#jquery_jplayer').jPlayer("play");
}

function jplayerReady() 
{
    playListChange(0);
}

var g_songsPlayed = 0;
var g_currentSongId = 0;
var g_currentSongIndex = 0;

function playListChange( index ) 
{
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
    
    
    $('#track_name').html(song.name);
    $('#track_play_count').html(song.listens);
    
    if( updateListens(song.id,index) )
    {
        g_totalListens++;
        $('#total_view_count').html(g_totalListens);
    }
}

function maybeAskForEmail()
{
    
}

var g_listenUpdated = {};
function updateListens(song_id,index)
{
    if( song_id in g_listenUpdated )
        return false;

    g_listenUpdated[song_id] = true;

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
            $('#total_view_count').html(g_totalListens);
            $('#track_play_count').html(track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}




