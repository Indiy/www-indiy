
function showSocialPost(page_index)
{
    var page = g_audioList[page_index];
    var name = page.name;
    var short_link = page.short_link;
    var update_text = "Check out my new Art, {0}: {1} via @myartistdna".format(name,short_link);
    $('#social_post #update_text').val(update_text);
    showPopup('#social_post');
}

function onSocializePublish()
{
    showProgress("Posting update...");
    
    var update_text = $('#social_post #update_text').val();
    var network = $('#social_post input[name=network]:checked').val();
    
    var post_url = "/manage/data/social_post.php?";
    post_url += "&artist_id=" + escape(g_artistId);
    post_url += "&update_text=" + escape(update_text);
    post_url += "&network=" + network;
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'text',
        success: function(data) 
        {
            showSuccess("Updated Posted.");
        },
        error: function()
        {
            showFailure("Update Failed.");
        }
    });
    return false;
}


