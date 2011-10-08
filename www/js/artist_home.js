

// Helper functions for artist home page

function updateListens(image)
{
    var url = "/data/listens.php?image=" + image;

    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            var total_listens = data['total_listens'];
            var track_listens = data['track_listens'];
            $('#total_listens').text(total_listens);
            $('#current_track_listens').text(track_listens);
        },
        error: function()
        {
            alert('Failed to get listens!');
        }
    });
}

