
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
        'artist_id': g_artistId,
        'name': name,
        'type': type
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
