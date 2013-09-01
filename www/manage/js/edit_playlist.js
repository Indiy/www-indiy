
function showPlaylistPopup()
{
    showPopup('#edit_playlist');
}

function onPlaylistSubmit()
{
    showProgress("Adding playlist...");

    var name = $('#edit_playlist #playlist_name').val();
    var type = $('#edit_playlist #playlist_type').val();
    
    var url = "/manage/data/playlists.php";
    var data = {
        artist_id: g_artistId,
        name: name,
        type: type
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            showSuccess("Playlist added.");
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false; 
}

function updatePlaylists()
{
    for( var i = 0 ; i < g_playlistList.length ; ++i )
    {
        var playlist = g_playlistList[i];
        var sel = '#playlist_list_ul_' + i;
        $(sel).empty();
        for( var j = 0 ; j < playlist.items.length ; ++j )
        {
            var pl_item = playlist.items[j];

            var img_url = get_thumbnail(pl_item.image_url,pl_item.image_extra,210,132);
        
            var html = "";
            html += "<li id='arrayorder_{0}' class='playlist_sortable'>".format(pl_item.playlist_item_id);
            html += " <figure>";
            html += "  <span class='close'>";
            html += "   <a href='#' onclick='deletePlaylistItem({0},{1});'></a>".format(i,j);
            html += "  </span>";
            html += "  <a title='{0}' onclick='showPlaylistItemPopup({0},{1});'>".format(i,j);
            html += "   <img src='{0}' width='210' height='132'>".format(img_url);
            html += "  </a>";
            html += " </figure>";
            html += " <span>";
            html += "  <a title='Edit Item' onclick='showPlaylistItemPopup({0},{1});'>".format(i,j);
            html += pl_item.name;
            html += "  </a>";
            html += " </span>";
            html += " <br>";
            html += "</li>";
            $(sel).append(html);
        }
    }
    setupSortableList('ul.playlist_items_sortable',"/manage/data/playlist_items.php");
}
$(document).ready(updatePlaylists);

var g_currentPlaylistIndex = false;
function showPlaylistItemPopup(playlist_index)
{
    g_currentPlaylistIndex = playlist_index;
    
    $('#edit_playlist_item #name').val("");
    fillArtistFileIdSelect('#edit_playlist_item #image_id','IMAGE',false);
    $('#edit_playlist_item #bg_color').val('000000');
    fillArtistFileIdSelect('#edit_playlist_item #media_id',['AUDIO','VIDEO'],false);
    
    showPopup('#edit_playlist_item');
}

function onPlaylistItemSubmit()
{
    showProgress("Adding item to playlist...");
    
    var playlist = g_playlistList[g_currentPlaylistIndex];

    var name = $('#edit_playlist_item #name').val();
    var image_id = $('#edit_playlist_item #image_id').val();
    var bg_style = $('#edit_playlist_item #bg_style').val();
    var bg_color = $('#edit_playlist_item #bg_color').val();
    var media_id = $('#edit_playlist_item #media_id').val();
    
    var url = "/manage/data/playlist_items.php";
    var data = {
        playlist_id: playlist.playlist_id,
        name: name,
        image_id: image_id,
        bg_style: bg_style,
        bg_color: bg_color,
        media_id: media_id
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            showSuccess("Playlist item added.");
        },
        error: function()
        {
            showFailure("Update failed.");
        }
    });
    return false; 
}

