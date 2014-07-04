
function clickJoinNewsletter()
{    
    $('#social_email .input_button').hide();
    $('#social_email .success').show();
    
    var email = $('#social_email input').val();
    
    var args = {
        email: email,
        artist_id: g_artistId
    };
    
    var url = "/data/viewer_data.php";
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: args,
        dataType: 'json',
        success: function(data) {},
        error: function() {}
    });    
}
