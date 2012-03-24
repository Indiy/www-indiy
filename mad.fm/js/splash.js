

function onSplashReady()
{
    $('#play_container').mouseover(mouseoverPlaylist);
    $('#play_container').mouseout(mouseoutPlaylist);
    
    if( typeof(g_genreList) == "undefined" )
        loadGenreList();
    else
        renderGenreList();
}
$(document).ready(onSplashReady);

function loadGenreList()
{
    jQuery.ajax(
    {
        type: 'GET',
        url: "http://www.myartistdna.fm/data/stream_info.php",
        dataType: 'json',
        success: function(data) 
        {
            g_genreList = data.genre_list;
            renderGenreList();
        },
        error: function()
        {
        }
    });
}

function renderGenreList()
{
    $('#genre_container').empty();
    var cls = 'top';
    for( var i = 0 ; i < g_genreList.length ; ++i )
    {
        var g = g_genreList[i];
        var html = "<a href='player.html?genre=" + g.stream_name + "'>";
        html += "<div class='item " + cls + "'>";
        html += "<div class='label'>I WANT " + g.genre + "</div>";
        html += "<div class='icon'></div>";
        html += "</div>";
        html += "</a>";
        if( cls == 'top' )
            cls = 'second';
        else
            cls = 'reg';
        $('#genre_container').append(html);
    }
}

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
