

function showStoreSettings()
{
    $('#store_settings #paypal_email').val(g_artistData.paypal_email);
    showPopup('#store_settings');
}

function onStoreSettingsSubmit()
{
    showProgress("Updating record...");

    var paypal_email = $('#store_settings #paypal_email').val();
    
    var post_url = "/manage/store_settings.php?";
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
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;    
}

