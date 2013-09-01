
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

var g_currentPlaylistIndex = false;
function showPlaylistItemPopup(playlist_index)
{
    g_currentPlaylistIndex = playlist_index;
    $('#edit_playlist_item #name').val("");
    fillArtistFileIdSelect('#edit_playlist_item #image_id','IMAGE',false);
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
        media_id: media_id,
        bg_style: bg_style,
        bg_color: bg_color
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
            showFailure("Playlist item add failed.");
        }
    });
    return false; 
}

