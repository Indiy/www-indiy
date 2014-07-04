
function catalogClickPlaylist(index)
{
    var playlist = g_playlistList[index];
    
    $('#playlist_tab .item_column.playlist').children().removeClass('active');
    $('#playlist_tab .item_column.playlist').children().addClass('inactive');

    var sel = "#playlist_tab .item_column.playlist #item_{0}".format(index);
    $(sel).removeClass('inactive');
    $(sel).addClass('active');
    
    
    $('#playlist_tab .item_column.dir').empty();
    $('#playlist_tab .item_column.media').empty();
    
    if( playlist.type == 'DIR' )
    {
        $('#playlist_tab .headers .column2').show();
        $('#playlist_tab .item_column.dir').show();
        for( var i = 0 ; i < playlist.items.length ; ++i )
        {
            var pi = playlist.items[i];
            var image_url = getImgUrlWithWidth(pi,233);
            
            var html = "";
            html += "<div id='item_{1}' class='item inactive' onclick='catalogClickPlaylistDirItem({0},{1});'>".format(index,i);
            html += " <img src='{0}'/>".format(image_url);
            html += " <div class='overlay'></div>";
            html += " <div class='name'>{0}</div>".format(pi.name);
            html += "</div>";
            
            $('#playlist_tab .item_column.dir').append(html);
        }
    }
    else
    {
        $('#playlist_tab .headers .column2').hide();
        $('#playlist_tab .item_column.dir').hide();
        var playlist_tuple = "{0}".format(index);
        populateMedia(playlist,playlist_tuple);
    }
    $('#playlist_tab').scrollbar("repaint");
}

function catalogClickPlaylistDirItem(playlist_index,child_playlist_index)
{
    var playlist = g_playlistList[playlist_index];
    var child_playlist = playlist.items[child_playlist_index];
    
    $('#playlist_tab .item_column.dir').children().removeClass('active');
    $('#playlist_tab .item_column.dir').children().addClass('inactive');

    var sel = "#playlist_tab .item_column.dir #item_{0}".format(child_playlist_index);
    $(sel).removeClass('inactive');
    $(sel).addClass('active');
    
    var playlist_tuple = "{0},{1}".format(playlist_index,child_playlist_index);
    populateMedia(child_playlist,playlist_tuple);
    $('#playlist_tab').scrollbar("repaint");
}
function populateMedia(playlist,playlist_tup)
{
    $('#playlist_tab .item_column.media').empty();
    for( var i = 0 ; i < playlist.items.length ; ++i )
    {
        var pi = playlist.items[i];
        var image_url = getImgUrlWithWidth(pi,233);
        
        var media_tup = "{0},{1}".format(playlist_tup,i);
        
        var html = "";
        html += "<div id='item_{1}' class='item inactive' onclick='catalogClickPlaylistMediaItem({0});'>".format(media_tup,i);
        html += " <img src='{0}'/>".format(image_url);
        html += " <div class='overlay'></div>";
        html += " <div class='name'>{0}</div>".format(pi.name);
        html += "</div>";
        
        $('#playlist_tab .item_column.media').append(html);
    }
}

function catalogClickPlaylistMediaItem(playlist_index,child_playlist_index,playlist_item_index)
{
    $('#playlist_tab').hide();
    var playlist = g_playlistList[playlist_index];
    
    if( typeof playlist_item_index !== 'undefined'
        && playlist_item_index !== false )
    {
        playlist = playlist.items[child_playlist_index];
    }
    else
    {
        playlist_item_index = child_playlist_index;
    }
    playlistChangePlaylist(playlist,playlist_item_index);
}
