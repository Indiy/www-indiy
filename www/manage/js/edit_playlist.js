
function showPlaylistPopup()
{
    showPopup('#edit_playlists');
}

function onPlaylistSubmit()
{
    showProgress("Adding playlist...");

    var name = $('#edit_playlists #playlist_name').val();
    var type = $('#edit_playlists #playlist_type').val();
    
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
