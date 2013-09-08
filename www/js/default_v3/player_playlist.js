

function playlistReady()
{
    var html = "";
    for( var i = 0 ; i < g_playlistList.length ; ++i )
    {
        var playlist = g_playlistList[i];
        if( playlist.type == 'DIR' )
        {
            for( var j = 0 ; j < playlist.items.length ; ++j )
            {
                html = getImageHolders(playlist.items[j]);
                $(body).prepend(html);
            }
        }
        else
        {
            html = getImageHolders(playlist);
            $(body).prepend(html);
        }
    }
}

function getImageHolders(playlist)
{
    var html = "";
    
    html += "<div id='playlist_bg_{0}' class='full_screen_bg'>".format(playlist.playlist_id);
    html += " <div class='pad'></div>";
    for( var i = 0 ; i < playlist.items.length ; ++i )
    {
        html += " <div id='image_holder_{0}' class='image_holder'></div>".format(i);
    }
    html += " <div class='pad'></div>";
    html += " <div id='video_container'></div>";
    html += "</div>";
    return html;
}

function clickPlaylist(index)
{
    var playlist = g_playlistList[index];
    
    $('#playlist_tab .item_column.playlist').children().removeClass('active');
    $('#playlist_tab .item_column.playlist').children().addClass('inactive');

    var sel = "#playlist_tab .item_column.playlist #item_{0}".format(index);
    $(sel).removeClass('inactive');
    $(sel).addClass('active');
    
    if( playlist.type == 'DIR' )
    {
        $('#playlist_tab .item_column.dir').empty();
        $('#playlist_tab .item_column.media').empty();
        
        for( var i = 0 ; i < playlist.items.length ; ++i )
        {
            var pi = playlist.items[i];
            var image_url = getImgUrlWithWidth(pi,233);
            
            var html = "";
            html += "<div id='item_{1}' class='item inactive' onclick='clickPlaylistDirItem({0},{1});'>".format(index,i);
            html += " <img src='{0}'/>".format(image_url);
            html += " <div class='overlay'></div>";
            html += " <div class='name'>{0}</div>".format(pi.name);
            html += "</div>";
            
            $('#playlist_tab .item_column.dir').append(html);
        }
    }
}

function clickPlaylistDirItem(playlist_index,child_playlist_index)
{
    var playlist = g_playlistList[playlist_index];
    var child_playlist = playlist.items[child_playlist_index];
    
    $('#playlist_tab .item_column.dir').children().removeClass('active');
    $('#playlist_tab .item_column.dir').children().addClass('inactive');

    var sel = "#playlist_tab .item_column.dir #item_{0}".format(child_playlist_index);
    $(sel).removeClass('inactive');
    $(sel).addClass('active');
    
    $('#playlist_tab .item_column.media').empty();
    for( var i = 0 ; i < child_playlist.items.length ; ++i )
    {
        var pi = child_playlist.items[i];
        var image_url = getImgUrlWithWidth(pi,233);
        
        var tup = "{0},{1},{2}".format(playlist_index,child_playlist_index,i);
        
        var html = "";
        html += "<div id='item_{1}' class='item inactive' onclick='clickPlaylistMediaItem({0});'>".format(tup,i);
        html += " <img src='{0}'/>".format(image_url);
        html += " <div class='overlay'></div>";
        html += " <div class='name'>{0}</div>".format(pi.name);
        html += "</div>";
        
        $('#playlist_tab .item_column.media').append(html);
    }
}

var g_currentPlaylist = false;
var g_currentPlaylistIndex = 0;

function clickPlaylistMediaItem(playlist_index,child_playlist_index,playlist_item_index)
{
    var playlist = playlist_index[playlist_index];
    
    if( typeof playlist_item_index !== 'indefined' )
    {
        playlist = playlist.items[child_playlist_index];
    }
    else
    {
        playlist_item_index = child_playlist_index;
    }
    var playlist_item = playlist[playlist_item_index];
    
    g_currentPlaylist = playlist;
    g_currentPlaylistIndex = playlist_item_index;
    
    
    
}
