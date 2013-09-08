

var g_playlistIndex = false;
function showPlaylistPopup(playlist_index)
{
    if( playlist_index !== false )
    {
        g_playlistIndex = playlist_index;
        var playlist = g_playlistList[g_playlistIndex];
        $('#edit_playlist #playlist_name').val(playlist.name);
        $('#edit_playlist #display_name').val(playlist.display_name);
        $('#edit_playlist #playlist_type').val(playlist.type);
        fillArtistFileIdSelect('#edit_playlist #image_id','IMAGE',playlist.image_id);
    }
    else
    {
        g_playlistIndex = false;
        $('#edit_playlist #playlist_name').val("");
        $('#edit_playlist #display_name').val("");
        $('#edit_playlist #playlist_type').val("AUDIO");
        fillArtistFileIdSelect('#edit_playlist #image_id','IMAGE',false);
    }
    showPopup('#edit_playlist');
}

function onPlaylistSubmit()
{
    var name = $('#edit_playlist #playlist_name').val();
    var display_name = $('#edit_playlist #display_name').val();
    var type = $('#edit_playlist #playlist_type').val();
    var image_id = $('#edit_playlist #image_id').val();
    
    if( name.length == 0 )
    {
        window.alert("Please enter a name for your playlist.");
        return;
    }
    if( display_name.length == 0 )
    {
        window.alert("Please enter a display name for your playlist.");
        return;
    }
    
    showProgress("Adding playlist...");

    var url = "/manage/data/playlists.php";
    var data = {
        artist_id: g_artistId,
        name: name,
        display_name: display_name,
        type: type,
        image_id: image_id
    };
    if( g_playlistIndex !== false )
    {
        var playlist = g_playlistList[g_playlistIndex];
        data.playlist_id = playlist.playlist_id;
    }
    
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            window.location.reload();
        },
        error: function()
        {
            showFailure("Playlist update failed.");
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
            html += "<li id='arrayorder_{0}'>".format(pl_item.playlist_item_id);
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
var g_currentPlaylistItemIndex = false;
function showPlaylistItemPopup(playlist_index,playlist_item_index)
{
    g_currentPlaylistIndex = playlist_index;

    var playlist = g_playlistList[playlist_index];

    var html = "<option val='0'>None</option>";
    $('#edit_playlist_item #child_playlist_id').html(html);
    for( var i = 0 ; i < g_playlistList.length ; ++i )
    {
        var p = g_playlistList[i];
        if( p.playlist_id != playlist.playlist_id )
        {
            var html = "<option val='{0}'>{1}</option>".format(p.playlist_id,p.name);
            $('#edit_playlist_item #child_playlist_id').append(html);
        }
    }

    $('#edit_playlist_item .playlist_type').hide();
    if( playlist.type == 'DIR' )
    {
        $('#edit_playlist_item .playlist_type.dir').show();
    }
    else
    {
        $('#edit_playlist_item .playlist_type.normal').show();
    }
    if( playlist_item_index !== false )
    {
        var playlist = g_playlistList[playlist_index];
        g_currentPlaylistItemIndex = playlist_item_index;
        var playlist_item = playlist.items[playlist_item_index];
        
        $('#edit_playlist_item #name').val(playlist_item.name);
        fillArtistFileIdSelect('#edit_playlist_item #image_id','IMAGE',playlist_item.image_id);
        $('#edit_playlist_item #bg_style').val(playlist_item.bg_style);
        $('#edit_playlist_item #bg_color').val(playlist_item.bg_color);
        fillArtistFileIdSelect('#edit_playlist_item #media_id',['AUDIO','VIDEO'],playlist_item.media_id);
        $('#edit_playlist_item #iframe_code').val(playlist_item.iframe_code);
        $('#edit_playlist_item #child_playlist_id').val(playlist_item.child_playlist_id);
    }
    else
    {
        g_currentPlaylistItemIndex = false;
        $('#edit_playlist_item #name').val("");
        fillArtistFileIdSelect('#edit_playlist_item #image_id','IMAGE',false);
        $('#edit_playlist_item #bg_style').val('LETTERBOX');
        $('#edit_playlist_item #bg_color').val('000000');
        fillArtistFileIdSelect('#edit_playlist_item #media_id',['AUDIO','VIDEO'],false);
        $('#edit_playlist_item #iframe_code').val("");
        $('#edit_playlist_item #child_playlist_id')[0].value = "";
    }
    
    showPopup('#edit_playlist_item');
}

function onPlaylistItemSubmit()
{
    var playlist = g_playlistList[g_currentPlaylistIndex];

    var name = $('#edit_playlist_item #name').val();
    var image_id = $('#edit_playlist_item #image_id').val();
    var bg_style = $('#edit_playlist_item #bg_style').val();
    var bg_color = $('#edit_playlist_item #bg_color').val();
    var media_id = $('#edit_playlist_item #media_id').val();
    var iframe_code = $('#edit_playlist_item #iframe_code').val();
    
    if( name.length == 0 )
    {
        window.alert('Please name your item.');
        return;
    }
    
    showProgress("Adding item to playlist...");

    var url = "/manage/data/playlist_items.php";
    var data = {
        playlist_id: playlist.playlist_id,
        name: name,
        image_id: image_id,
        bg_style: bg_style,
        bg_color: bg_color,
        media_id: media_id,
        iframe_code: iframe_code
    };

    if( g_currentPlaylistItemIndex !== false )
    {
        data.playlist_item_id = playlist.items[g_currentPlaylistItemIndex].playlist_item_id;
    }
    
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            if( g_currentPlaylistItemIndex !== false )
            {
                playlist.items[g_currentPlaylistItemIndex] = data.playlist_item;
            }
            else
            {
                playlist.items.unshift(data.playlist_item);
            }
            updatePlaylists();
            showSuccess("Playlist item added.");
        },
        error: function()
        {
            showFailure("Update failed.");
        }
    });
    return false; 
}
function deletePlaylistItem(i,j)
{
    var playlist = g_playlistList[i];
    var playlist_item = playlist.items[j];
    
    var url = "/manage/data/playlist_items.php";
    var data = {
        playlist_item_id: playlist_item.playlist_item_id
    };
    
    var r = window.confirm("Are you sure you want to delete this item?");
    if( r )
    {
        jQuery.ajax(
        {
            type: 'DELETE',
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) 
            {
                playlist.items.splice(j,1);
                updatePlaylists();
            },
            error: function()
            {
                window.alert("Delete failed.");
            }
        });
    }
    return false;
}
function deletePlaylist(i)
{
    var playlist = g_playlistList[i];
    
    var url = "/manage/data/playlists.php";
    var data = {
        playlist_id: playlist.playlist_id
    };
    
    var r = window.confirm("Are you sure you want to delete this playlist?");
    if( r )
    {
        jQuery.ajax(
        {
            type: 'DELETE',
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) 
            {
                window.location.reload();
            },
            error: function()
            {
                window.alert("Delete failed.");
            }
        });
    }
    return false;
}

