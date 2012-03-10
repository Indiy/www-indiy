
var g_streamInfo = false;

var SCROLL_SETTINGS = {
    autoScroll: "always", 
    autoScrollDirection: "backandforth", 
    autoScrollInterval: 10, 
    autoScrollStep: 1
};


function ffmp3Callback(event,value)
{
    console.log('event: (' + event + '), value: (' + value + ')');
}

function onReady()
{
    loadSteamInfo();
    window.setInterval(updateTrackInfo,500);
    $('#trackTitle').smoothDivScroll(SCROLL_SETTINGS);
}
$(document).ready(onReady);

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
    var name = track.artist + " - " + track.song;
    
    if( $('#track_title').text() != name )
    {
        $('#track_title').text(name);
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





