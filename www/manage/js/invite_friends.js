
function showInvitePopup()
{
    $('#invite_friends #friends_text').val('');
    showPopup('#invite_friends');
}

function onInviteFriends()
{
    showProgress("Sending Invites...");

    var friends = $('#invite_friends #friends_text').val();
    
    var post_url = "/manage/data/invite_friends.php?";
    post_url += "&artist_id=" + escape(g_artistId);
    post_url += "&friends=" + escape(friends);
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'text',
        success: function(data) 
        {
            showSuccess("Thank you, your friends will be invited.");
        },
        error: function()
        {
            showSuccess("Thank you, your friends will be invited.");
        }
    });
    return false;
}

