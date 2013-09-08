
function clickPlaylist(index)
{
    var playlist = g_playlistList[index];
    
    if( playlist.type == 'DIR' )
    {
        $('#playlist_tab .item_column.dir').empty();
        $('#playlist_tab .item_column.media').empty();
        
        for( var i = 0 ; i < playlist.items.length ; ++i )
        {
            var pi = playlist.items[i];
            var image_url = getImgUrlWithWidth(pi,233);
            
            var html = "";
            html += "<div class='item inactive' onclick='clickPlaylistItem({0},{1});'>".format(index,i);
            html += " <img src='{0}'/>".format(image_url);
            html += " <div class='overlay'></div>";
            html += " <div class='name'>{0}</div>".format(pi.name);
            html += "</div>";
            
            $('#playlist_tab .item_column.dir').append(html);
        }
    }
}

