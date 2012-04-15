

function showStoreSettings()
{
    $('#edit_store #paypal_email').val(g_artistData.paypal_email);
    showPopup('#edit_store');
}

function onStoreSettingsSubmit()
{
    showProgress("Updating record...");

    var paypal_email = $('#edit_store #paypal_email').val();
    
    var post_url = "/manage/data/store.php?";
    post_url += "&artist_id=" + escape(g_artistId);
    post_url += "&paypal_email=" + escape(paypal_email);
    post_url += "&submit=1";
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'text',
        success: function(data) 
        {
            showSuccess("Settings updated.");
            g_artistData = data.artist_data;
            updateProfile();
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;    
}

