

var PROGRESS_BAR_WIDTH = 468;
var PROGRESS_ROUND_LENGTH = 462;

var g_streamInfo = false;
var g_scrollingRight = true;
var g_lastStreamLoad = 0;
var g_playing = true;
var g_intervalUpdateTrack = false;
var g_historyShown = false;

var g_loveMap = {};

function ffmp3Callback(event,value)
{
    console.log('event: (' + event + '), value: (' + value + ')');
}

function onReady()
{
    loadSteamInfo();
    g_intervalUpdateTrack = window.setInterval(updateTrackInfo,200);
    window.setInterval(scrollTrackTitle,50);
}
$(document).ready(onReady);

function msTime()
{
    return (new Date()).getTime();
}

function scrollTrackTitle()
{
    var old_pos = $('#title_scoller').scrollLeft();
    var new_pos = old_pos;
    if( g_scrollingRight )
        new_pos--;
    else
        new_pos++;
    $('#title_scoller').scrollLeft(new_pos);
    if( old_pos == $('#title_scoller').scrollLeft() )
        g_scrollingRight = !g_scrollingRight;
}

function mins_secs(secs)
{
    var mins = Math.floor(secs / 60);
    secs -= mins * 60;
    return sprintf("%02d:%02d",mins,secs); 
}

function updateTrackInfo()
{
    if( !g_streamInfo )
        return;
    
    var track = g_streamInfo['history'][0];
    var title = track.artist + " - " + track.song;
    
    if( $('#track_title').text() != title )
    {
        $('#track_title').text(title);
        g_scrollingRight = true;
        $('#track_title').scrollLeft(0);
        if( title in g_loveMap )
            $('#player .heart').addClass('love');
        else
            $('#player .heart').removeClass('love');
    }
    
    var duration = track.duration;
    var start = track.start;
    var curr_pos = msTime()/1000 - start;
    if( curr_pos > duration )
        curr_pos = duration;
    
    var s = mins_secs(curr_pos) + " - " + mins_secs(duration);
    if( $('#track_duration').text() != s )
    {
        $('#track_duration').text(s);
        updateHistory();
    }
    var percent = curr_pos/duration;
    var width = percent * PROGRESS_BAR_WIDTH;
    $('#player .progress .bar').width(width);
    if( width >= PROGRESS_ROUND_LENGTH )
        $('#player .progress .bar').css('border-radius','6px 6px 6px 6px');
    else
        $('#player .progress .bar').css('border-radius','6px 0px 0px 6px');

    if( curr_pos + 10 > duration )
    {
        var delta = msTime() - g_lastStreamLoad;
        if( delta > 500 )
        {
            g_lastStreamLoad = msTime();
            loadSteamInfo();
        }
    }
}
function updateHistory()
{
    $('#history .content').empty();
    for( var i = 1 ; i < 4 ; ++i )
    {
        var track = g_streamInfo['history'][i];
        var title = track.artist + " - " + track.song;
        var duration = mins_secs(track.duration);
        var html = "<div class='row'>";
        html += "<div class='icon'><img src=''></div>";
        html += "<div class='title'>" + title + "</div>";
        html += "<div class='length'>" + duration + "</div>";
        html += "<div class='heart'><div></div></div>";
        html += "</div>";
        $('#history .content').append(html);
    }
}
function loadSteamInfo()
{
    jQuery.ajax(
    {
        type: 'GET',
        url: "data/stream_info.php",
        dataType: 'json',
        success: function(data) 
        {
            g_streamInfo = data;
            updateTrackInfo();
        },
        error: function()
        {
            //window.alert("Error!");
        }
    });
}

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
    var player = (document.ffmp3_player) ? document.ffmp3_player : document.getElementById('ffmp3_player');
    player.playSound();
    g_intervalUpdateTrack = window.setInterval(updateTrackInfo,200);
}
function playerPause()
{
    g_playing = false;
    var player = (document.ffmp3_player) ? document.ffmp3_player : document.getElementById('ffmp3_player');
    player.stopSound();
    window.clearInterval(g_intervalUpdateTrack);
}

function toggleHistory()
{
    if( g_historyShown )
        hideHistory();
    else
        showHistory();
}
function showHistory()
{
    g_historyShown = true;
    $('#history').fadeIn();
    updateHistory();
}
function hideHistory()
{
    g_historyShown = false;
    $('#history').fadeOut();
}

function showAddMusic()
{
    $('#add_music').fadeIn();
}
function hideAddMusic()
{
    $('#add_music').fadeOut();
}

function toggleLoveCurrentSong()
{
    var title = $('#track_title').text();
    if( title in g_loveMap )
    {
        delete g_loveMap[title];
        $('#player .heart').removeClass('love');
    }
    else 
    {
        $('#player .heart').addClass('love');
        g_loveMap[title] = true;
        var track = g_streamInfo['history'][0];
        showLoved(track.artist.artist,track.song);
    }
}

function showLoved(artist,song)
{
    $('#song_love .dialog .header .title span').text('"' + song + '"');
    
    var link_url = "http://www.myartistdna.fm"
    var host = "www.myartistdna.fm"
    var msg = 'Check out ' + artist + '\'s song "' + song + '" on MyArtistDNA.FM';
    var name = 'MyArtistDNA.FM';
    
    $('#fb_link').attr('href','http://www.facebook.com/sharer/sharer.php?u=' + host);
    $('#tw_link').attr('href','http://twitter.com/?status=' + encodeURIComponent(msg));

    var url = "http://www.tumblr.com/share/link?url=" + encodeURIComponent(link_url);
    url += "&name=" + encodeURIComponent(name);
    url += "&description=" + encodeURIComponent(msg);
    $('#tumblr_link').attr('href',url);
    
    var url = "http://pinterest.com/pin/create/button/?url=" + encodeURIComponent(link_url);
    url += "&description=" + encodeURIComponent(msg);
    $('#pin_link').attr('href',url);
    
    var url = "https://plusone.google.com/_/+1/confirm?hl=en&url=" + encodeURIComponent(link_url);
    $('#google_link').attr('href',url);
    
    var url = "mailto:?&subject=" + encodeURIComponent(msg);
    $('#email_link').attr('href',url);
    
    $('#song_love').fadeIn();
}

function hideLoved()
{
    $('#song_love').fadeOut();
}


