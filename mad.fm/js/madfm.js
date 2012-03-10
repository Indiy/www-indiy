
var g_streamInfo = false;

function ffmp3Callback(event,value)
{
    console.log('event: (' + event + '), value: (' + value + ')');
}

function onReady()
{
    loadSteamInfo();
}
$(document).ready(onReady);

function updateTrackInfo()
{
    if( !g_streamInfo )
        return;
    
    var track = g_streamInfo['history'][0];
    var name = track.artist + " - " + track.song;
    $('#track_title').text(name);
    
    var duration = track.duration;
    var start = track.start;
    
    if( start == null )
    
}

function loadSteamInfo()
{
    jQuery.ajax(
    {
        type: 'GET',
        url: "http://www.myartistdna.fm/data/stream_info.php",
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





