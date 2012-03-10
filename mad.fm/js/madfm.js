

var PROGRESS_BAR_WIDTH = 405;

var g_streamInfo = false;

var g_scrollingRight = true;


function ffmp3Callback(event,value)
{
    console.log('event: (' + event + '), value: (' + value + ')');
}

function onReady()
{
    loadSteamInfo();
    window.setInterval(updateTrackInfo,200);
    window.setInterval(scrollTrackTitle,50);
}
$(document).ready(onReady);

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
    }
    
    var duration = track.duration;
    var start = track.start;
    var curr_pos = (new Date()).getTime()/1000 - start;
    if( curr_pos > duration )
        curr_pos = duration;
    
    var s = mins_secs(curr_pos) + " - " + mins_secs(duration);
    if( $('#track_duration').text() != s )
    {
        $('#track_duration').text(s);
    }
    var percent = curr_pos/duration;
    var width = percent * PROGRESS_BAR_WIDTH;
    $('#player .progress .bar').width(width);

    if( curr_pos + 10 > duration )
        loadSteamInfo();
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





