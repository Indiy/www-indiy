
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
            html += "<div id='item_{1}' class='item inactive' onclick='clickPlaylistItem({0},{1});'>".format(index,i);
            html += " <img src='{0}'/>".format(image_url);
            html += " <div class='overlay'></div>";
            html += " <div class='name'>{0}</div>".format(pi.name);
            html += "</div>";
            
            $('#playlist_tab .item_column.dir').append(html);
        }
    }
}

