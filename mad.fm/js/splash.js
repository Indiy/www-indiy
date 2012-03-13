

function onSplashReady()
{
    $('#play_container').mouseover(mouseoverPlaylist);
    $('#play_container').mouseout(mouseoutPlaylist);
}
$(document).ready(onSplashReady);

var g_playlistTimer = false;
var g_playlistOpen = false;
function mouseoverPlaylist()
{
    if( g_playlistTimer !== false )
    {
        window.clearTimeout(g_playlistTimer);
        g_playlistTimer = false;
    }
    openPlaylist();
}
function mouseoutPlaylist()
{
    g_playlistTimer = window.setTimeout(closePlaylist,700);
}
function openPlaylist()
{
    if( !g_playlistOpen )
    {
        g_playlistOpen = true;
        $('#play_container .item_container').animate({ height: "256px" }, 300);
    }
}
function closePlaylist()
{
    if( g_playlistOpen )
    {
        g_playlistOpen = false;
        $('#play_container .item_container').animate({ height: "64px" }, 300);
    }
}
