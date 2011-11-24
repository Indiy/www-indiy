
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

function uploadProgress(percentage)
{
    var html = "";
    html += "<div class='upload-progress'>";
    html += "<div class='upload-progress-done' style='width:" + percentage.toFixed(2) + "%;'/>";
    html += "<div class='upload-percent'>" + percentage.toFixed(2) + "%</div>";
    html += "</div>";

    $('#upload_bar').html(html);
}

function onUploadProgress(evt)
{
    if( evt.lengthComputable )
    {
        var percentage = evt.loaded / evt.total * 100.0;
        console.log("progress: " + percentage);
        uploadProgress(percentage);
    }
    else
    {
        console.log("progress event but can't calculate percent");
    }
}
function onUploadDone(evt)
{
    $('#status').show();
    $('#status').text('Upload done. Processing content...');
    $('#upload_bar').hide();
    $('#spinner').show();
}
function onUploadFailed(evt)
{
    $('#status').show();
    $('#status').text('Upload failed!');
    $('#upload_bar').hide();
    $('#spinner').hide();
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
                $('#status').show();
                $('#status').text('Upload success.');
                $('#upload_bar').hide();
                $('#spinner').hide();
            }
            else
            {
                $('#status').show();
                $('#status').text('Upload failed.');
                $('#upload_bar').hide();
                $('#spinner').hide();
            }
        }
        catch(e)
        {
            $('#status').show();
            $('#status').text('Upload failed.');
            $('#upload_bar').hide();
            $('#spinner').hide();
        }
    }
}

function startAjaxUpload(url,fillForm)
{
    $('#ajax_from').hide();
    $('#status').show();
    $('#status').text('Uploading content...');
    
    try
    {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() { uploadReadyStateChange(this); };
        var upload = xhr.upload;
        if( upload )
        {
            upload.addEventListener('progress',onUploadProgress,false);
            upload.addEventListener('load',onUploadDone,false);
            upload.addEventListener('error',onUploadFailed,false);
        }
        
        var form_data = new FormData();
        fillForm(form_data);
        
        xhr.open("POST",url);
        xhr.send(form_data);
        $('#upload_bar').show();
        return false;
    }
    catch(e)
    {
        $('#spinner').show();
        return true;
    }
    
}

function onAddVideoSubmit()
{
    function fillVideoForm(form_data)
    {
        var artist_id = $('#artist_id').val();
        var song_id = $('#song_id').val();
        var video_name = $('#video_name').val();
        var video_image_file = document.getElementById('video_image_file').files[0];
        var video_file = document.getElementById('video_file').files[0];
            
        form_data.append('artistid',artist_id);
        form_data.append('id',song_id);
        form_data.append('name',video_name);
        form_data.append('logo',video_image_file);
        form_data.append('video',video_file);
    }
    var url = '/manage/addvideo.php';
    return startAjaxUpload(url,fillVideoForm);
}

function onAddMusicSubmit()
{
    function fillMusicForm(form_data)
    {
        var artist_id = $('#artist_id').val();
        var song_id = $('#song_id').val();
        var song_name = $('#song_name').val();
        var song_bgcolor = $('#song_bgcolor').val();
        var song_bgposition = $('#song_bgposition option:selected').val();
        var song_bgrepeat = $('#song_bgrepeat option:selected').val();
        var free_download = $('input[@name=download]:checked').val();
        var amazon_url = $('#amazon_url').val();
        var itunes_url = $('#itunes_url').val();
        var mad_store = $('#mad_store').attr('checked'); 

        form_data.append('artistid',artist_id);
        form_data.append('id',song_id);
        form_data.append('name',song_name);
        form_data.append('bgcolor',song_bgcolor);
        form_data.append('bgposition',song_bgposition);
        form_data.append('bgrepeat',song_bgrepeat);
        form_data.append('download',free_download);
        form_data.append('amazon',amazon_url);
        form_data.append('itunes',itunes_url);
        form_data.append('mad_store',mad_store);

        var song_image = document.getElementById('song_image');
        if( song_image.files && song_image.files.length > 0 )
        {
            form_data.append('logo',song_image.files[0]);
        }
        var song_audio = document.getElementById('song_audio');
        if( song_audio.files && song_audio.files.length > 0 )
        {
            form_data.append('audio',song_audio.files[0]);
        }
        form_data.append('WriteTags','submit');
    }
    
    var url = '/manage/addmusic.php';
    return startAjaxUpload(url,fillMusicForm);
}

