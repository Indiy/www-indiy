
function mouseoverClip(self)
{
    $('#link_tooltip').text('Copy to clipboard');
    g_clip.setText( self.previousSibling.href );
    if( g_clip.div ) 
    {
        g_clip.receiveEvent('mouseout', null);
        g_clip.reposition(self);
    }
    else
    {
        g_clip.glue(self);
    }
    g_clip.receiveEvent('mouseover',null);
    var new_offset = self.offset();
    new_offset.left -= 20;
    new_offset.top -= 20; 
    $('#link_tooltip').offset(new_offset)
    $('#link_tooltip').show();
}

var g_clip = false;

function clipMouseOut()
{
    $('#link_tooltip').hide();
}

function clipComplete()
{
    $('#link_tooltip').text('Copied');
}

function setupClipboard()
{
    ZeroClipboard.setMoviePath('/flash/ZeroClipboard.swf');
    g_clip = new ZeroClipboard.Client();
    g_clip.setHandCursor(true);
    g_clip.addEventListener('onMouseOut',clipMouseOut);
    g_clip.addEventListener('onComplete',clipComplete);
    $('.short_link_clip').mouseover(function() { mouseoverClip(this); });
}

$(document).ready(setupClipboard);

function onAddUserSubmit()
{
    $('#add_user_submit').hide();
    $('#status').text("Adding user...");
    var artist = $('#artist').val();
    var url = $('#url').val();
    var email = $('#email').val();
    var password = $('#password').val();
    
    var post_url = "/manage/add_user.php?";
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
            $('#status').text("User Added");
        },
        error: function()
        {
            $('#status').text("User Add Failed!");
        }
    });
    return false;
}

function onAddLabelSubmit()
{
    $('#add_label_submit').hide();
    $('#status').text("Adding label...");
    var name = $('#name').val();
    var email = $('#email').val();
    var password = $('#password').val();
    
    var post_url = "/manage/add_label.php?";
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
            $('#status').text("Label Added");
        },
        error: function()
        {
            $('#status').text("Label Add Failed!");
        }
    });
    return false;
}

function onStoreSettingsSubmit()
{
    $('#store_settings_submit').hide();
    $('#status').text("Updating settings...");
    var paypal_email = $('#paypal_email').val();
    
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
            $('#status').text("Settings updated.");
            $('#store_settings_submit').show();
        },
        error: function()
        {
            $('#status').text("Update failed!");
            $('#store_settings_submit').show();
        }
    });
    return false;    
}

