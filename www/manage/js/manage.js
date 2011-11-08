
function onAddUserSubmit()
{
    $('#add_user_submit').hide();
    $('#status').text("Adding user...");
    var artist = $('#artist').val();
    var url = $('#url').val();
    var email = $('#email').val();
    var password = $('#password').val();
    
    var post_url = "/manage/add_user.php?artist=" + escape(artist);
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
                $('#status').text("User Added");
                },
                error: function()
                {
                $('#status').text("User Add Failed!");
                }
                });
    return false;
}

