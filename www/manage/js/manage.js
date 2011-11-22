
function mouseoverClip(self)
{
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

    $('#link_tooltip').text('Copy to clipboard');
    $('#link_tooltip').show();
    var new_offset = $(self).offset();
    new_offset.left -= $('#link_tooltip').width()/2;
    new_offset.top -= $('#link_tooltip').height() + 15; 
    $('#link_tooltip').offset(new_offset)
}

var g_clip = false;

function clipMouseOver()
{
    $('#link_tooltip').show();    
}
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
    g_clip.addEventListener('onMouseOver',clipMouseOver);
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

function onUploadProgress(evt)
{
    if( evt.lengthComputable )
    {
        var percentage = evt.loaded / evt.total * 100.0;
        console.log("progress: " + percentage);
    }
    else
    {
        console.log("progress event but can't calculate percent");
    }
}
function onUploadDone(evt)
{
    file.upload_progress = 100.0;
}
function onUploadFailed(evt)
{
    window.alert("Upload failed: " + evt.code);
}
function uploadReadyStateChange(xhr)
{
    if( xhr.readyState == 4 )
    {
        var status_code = xhr.status;
        var text = xhr.responseText;
        try
        {
            if( status_code == 200 && text.length > 0 )
            {
                var data = JSON.parse(text);
            }
            else
            {
                window.alert("Failure status code: " + status_code);
            }
        }
        catch(e)
        {
            window.alert("Exception: " + e);
        }
    }
}

function onAddVideoSubmit()
{
    try
    {
        var artist_id = $('#artist_id').val();
        var song_id = $('#song_id').val();
        var video_name = $('#video_name').val();
        var video_image_file = $('#video_image_file').file[0];
        var video_file = $('#video_file').file[0];
    
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() { uploadReadyStateChange(this); };
        var upload = xhr.upload;
        
        function makeCallback(callback)
        {
            return function(evt) { callback(evt,file_obj); }
        }
        if( upload )
        {
            upload.addEventListener('progress',onUploadProgress,false);
            upload.addEventListener('load',onUploadDone,false);
            upload.addEventListener('error',onUploadFailed,false);
        }
        var form_data = new FormData();
        form_data.append('artistid',artist_id);
        form_data.append('id',song_id);
        form_data.append('name',video_name);
        form_data.append('logo',video_image_file);
        form_data.append('video',video_file);
        
        var url = '/manage/addvideo.php';
        xhr.open("POST",url);
        xhr.send(form_data);
    }
    catch(e)
    {
        $('#add_video_form').submit();
    }
    
}

