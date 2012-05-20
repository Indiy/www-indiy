
function showAddArtist()
{
    showPopup('#add_user');
}
function showAddLabel()
{
    showPopup('#add_label');
}

function myClose()
{
    window.location.reload();
}

function onAddUserSubmit()
{
    showProgress("Adding user...");

    var artist = $('#add_user #artist').val();
    var url = $('#add_user #url').val();
    var email = $('#add_user #email').val();
    var password = $('#add_user #password').val();
    
    var post_url = "/manage/data/user_admin.php?";
    post_url += "&add_user=1";
    post_url += "&artist=" + escape(artist);
    post_url += "&url=" + escape(url);
    post_url += "&email=" + escape(email);
    post_url += "&password=" + escape(password);
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'json',
        success: function(data) 
        {
            showSuccess("User added.");
            g_onCloseCallback = myClose;
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;
}

function onAddLabelSubmit()
{
    showProgress("Adding label...");

    var name = $('#add_label #name').val();
    var email = $('#add_label #email').val();
    var password = $('#add_label #password').val();
    
    var post_url = "/manage/data/user_admin.php?";
    post_url += "&add_label=1";
    post_url += "&name=" + escape(name);
    post_url += "&email=" + escape(email);
    post_url += "&password=" + escape(password);
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'json',
        success: function(data) 
        {
            showSuccess("Label added.");
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;
}

function showAccountSettings()
{
    $('#account_settings #artist_id').val(g_artistId);
    showPopup('#account_settings');
}
function onAccountSettingsSubmit()
{
    showProgress("Updating record...");
    
    var post_url = "/manage/data/account_settings.php?";
    post_url += $('#account_settings #ajax_form').serialize();
    
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'text',
        success: function(data) 
        {
            showSuccess("Update Success");
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;
}


