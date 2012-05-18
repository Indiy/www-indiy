
function showAddArtist()
{
    showPopup('#add_user');
}
function showAddLabel()
{
    showPopup('#add_label');
}

function onAddUserSubmit()
{
    showProgress("Adding user...");

    var artist = $('#artist').val();
    var url = $('#url').val();
    var email = $('#email').val();
    var password = $('#password').val();
    
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

    var name = $('#name').val();
    var email = $('#email').val();
    var password = $('#password').val();
    
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

