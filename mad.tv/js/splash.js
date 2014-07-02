

function onSplashReady()
{
    if( !('ontouchstart' in document) )
    {
        $('body').addClass('no_touch');
        $('#play_container').mouseover(mouseoverPlaylist);
        $('#play_container').mouseout(mouseoutPlaylist);
    }
    else
    {
        mouseoverPlaylist();
    }
        
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
        url: "/data/stream_info.php",
        dataType: 'json',
        success: function(data) 
        {
            g_genreList = data.genre_list;
            renderGenreList();
        },
        error: function()
        {
            //window.alert("Error!");
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
        var id = g.genre_id;
        var name = g.name;
        
        var html = "<a href='player.html?genre_id=" + id + "'>";
        html += "<div class='item " + cls + "'>";
        html += "<div class='label'>I WANT " + name + "</div>";
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
        var height = g_genreList.length * 64;
        $('#play_container .item_container').animate({ height: height + "px" }, 300);
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


