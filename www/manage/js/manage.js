

var g_hideTooltipTimer = false;
var g_clip = false;

var HOSTNAME_REGEX = new RegExp('^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?(?:\\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?)*\\.?$');

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

function startTooltipTimer()
{
    clearTooltipTimer();
    g_hideTooltipTimer = setTimeout("$('#link_tooltip').hide();",1000);
}
function clearTooltipTimer()
{
    if( g_hideTooltipTimer )
    {
        clearTimeout(g_hideTooltipTimer);
        g_hideTooltipTimer = false;
    }
}

function mouseenterLink(self)
{
    $('.link_copy').text('Copy');
    clearTooltipTimer();
    var url = self.href;
    g_clip.setText(url);
    var short_url = url.substring(7);
    $('#link_url').text(short_url);
    
    $('#link_tooltip').show();
    var new_offset = $(self).offset();
    new_offset.left -= $('#link_tooltip').width()/2;
    new_offset.top -= $('#link_tooltip').height() + 5; 
    $('#link_tooltip').offset(new_offset)

    var link_tooltip = $('#link_tooltip').get(0);
    if( g_clip.div ) 
    {
        g_clip.reposition(link_tooltip);
    }
    else
    {
        g_clip.glue(link_tooltip);
    }
}
function mouseleaveLink()
{
    startTooltipTimer();
}

function clipMouseOver()
{
    clearTooltipTimer();   
}
function clipMouseOut()
{
    startTooltipTimer();
}
function clipComplete()
{
    $('.link_copy').text('Copied');
}
function mouseenterToolTip()
{
    clearTooltipTimer();
}
function mouseleaveToolTip()
{
    startTooltipTimer();
}

function setupClipboard()
{
    ZeroClipboard.setMoviePath('/flash/ZeroClipboard.swf');
    g_clip = new ZeroClipboard.Client();
    g_clip.setHandCursor(true);
    g_clip.addEventListener('onMouseOver',clipMouseOver);
    g_clip.addEventListener('onMouseOut',clipMouseOut);
    g_clip.addEventListener('onComplete',clipComplete);
    $('.share a').mouseenter(function() { mouseenterLink(this); });
    $('.share a').mouseleave(mouseleaveLink);
    $('#link_tooltip').mouseenter(mouseenterToolTip);
    $('#link_tooltip').mouseleave(mouseleaveToolTip);
}

$(document).ready(setupClipboard);

function onAddUserSubmit()
{
    $('#ajax_form').hide();
    $('.status_container').hide();
    $('#progress_show').show();

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
            $('.status_container').hide();
            $('#success_msg').show();
        },
        error: function()
        {
            $('.status_container').hide();
            $('#failure_msg').show();
        }
    });
    return false;
}

function onAddLabelSubmit()
{
    $('#ajax_form').hide();
    $('.status_container').hide();
    $('#progress_show').show();

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
            $('.status_container').hide();
            $('#success_msg').show();
        },
        error: function()
        {
            $('.status_container').hide();
            $('#failure_msg').show();
        }
    });
    return false;
}

function onStoreSettingsSubmit()
{
    $('#store_settings_submit').hide();
    $('#status').text("Updating settings...");
    $('#status').show();
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
        },
        error: function()
        {
            $('#status').text("Update failed!");
        }
    });
    return false;    
}

function uploadProgress(percentage)
{
    var text = "" + percentage.toFixed(0);
    $('#upload_percent').text(text);
}

function onUploadProgress(evt)
{
    if( evt.lengthComputable )
    {
        var percentage = evt.loaded / evt.total * 100.0;
        //console.log("progress: " + percentage);
        uploadProgress(percentage);
    }
    else
    {
        console.log("progress event but can't calculate percent");
    }
}
function onUploadDone(evt)
{
    $('.status_container').hide();
    $('#processing_msg').show();
}
function onUploadFailed(evt)
{
    $('.status_container').hide();
    $('#failure_msg').show();
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
                    
                $('.status_container').hide();
                $('#success_msg').show();
                if( 'upload_error' in data )
                    $('#success_msg .status').text(data['upload_error']);
            }
            else
            {
                $('.status_container').hide();
                $('#failure_msg').show();
            }
        }
        catch(e)
        {
            $('.status_container').hide();
            $('#failure_msg').show();
        }
    }
}

function startAjaxUpload(url,fillForm)
{
    $('#ajax_form').hide();
    $('.status_container').hide();
    
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
        $('#uploading_msg').show();
        return false;
    }
    catch(e)
    {
        $('#progress_msg').show();
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
        form_data.append('remove_video',g_removeVideo);
        form_data.append('remove_video_image',g_removeVideoImage);
    }
    var url = '/manage/addvideo.php';
    return startAjaxUpload(url,fillVideoForm);
}
function checkFileExtensions(element_id,extensions,error_string)
{
    var file = document.getElementById(element_id);
    if( file.files && file.files.length > 0 )
    {
        var file_name = file.files[0].fileName;
        if( file_name && file_name.length > 0 )
        {
            file_name = file_name.toLowerCase();
            var file_name_parts = file_name.split('.');
            var ext = file_name_parts[file_name_parts.length - 1];
            for( var k in extensions )
            {
                var valid_ext = extensions[k];
                if( valid_ext == ext )
                    return;
            }
            window.alert(error_string);
        }
    }
}
function onVideoChange()
{
    checkFileExtensions('video_file',['mov','mp4'],"Please upload video in MP4 or MOV format.");
}

function onAddMusicSubmit()
{
    var song_image = document.getElementById('song_image');
    if( g_needsImage && ( !song_image || !song_image.value || song_image.value.length == 0 ) )
    {
        window.alert("Please upload an image for the page.");
        return false;
    }

    function fillMusicForm(form_data)
    {
        var artist_id = $('#artist_id').val();
        var song_id = $('#song_id').val();
        var song_name = $('#song_name').val();
        var song_bgcolor = $('#song_bgcolor').val();
        var bg_style = $('#bg_style option:selected').val();
        var free_download = $('input[@name=download]:checked').val();
        var amazon_url = $('#amazon_url').val();
        var itunes_url = $('#itunes_url').val();
        var mad_store = $('#mad_store').is(':checked');

        form_data.append('artistid',artist_id);
        form_data.append('id',song_id);
        form_data.append('name',song_name);
        form_data.append('bgcolor',song_bgcolor);
        form_data.append('bg_style',bg_style);
        form_data.append('download',free_download);
        form_data.append('amazon',amazon_url);
        form_data.append('itunes',itunes_url);
        form_data.append('mad_store',mad_store);
        form_data.append('remove_image',g_removeImage);
        form_data.append('remove_song',g_removeSong);

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

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

function onSongChange()
{
    checkFileExtensions('song_audio',['mp3'],"Please upload songs in MP3 format.");
}

function onSocializePublish()
{
    $('#socialize_form').hide();
    $('#status').show();
    $('#status').text("Posting update...");
    var update_text = $('#update_text').val();
    var network = $('input[name=network]:checked').val();
    
    var post_url = "/manage/socialize.php?";
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
            $('#status').text("Update posted.");
        },
        error: function()
        {
            $('#status').text("Update failed!");
        }
    });
    return false;
}

function onSocialConfigSave()
{
    $('#social_config_form').hide();
    $('#status').show();
    $('#status').text("Updating Settings...");
    var fb_setting = $('input[name=fb_setting]:checked').val();
    var tw_setting = $('input[name=tw_setting]:checked').val();
    var fb_page_url = $('#fb_page_url').val();
    
    var post_url = "/manage/social_config.php?";
    post_url += "&artist_id=" + escape(g_artistId);
    post_url += "&fb_setting=" + fb_setting;
    post_url += "&tw_setting=" + tw_setting;
    post_url += "&fb_page_url=" + escape(fb_page_url);
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'text',
        success: function(data) 
        {
            $('#status').text("Updated settings.");
        },
        error: function()
        {
            $('#status').text("Update failed!");
        }
    });
    return false;
}
function onAccountSettingsSubmit()
{
    $('#ajax_form').hide();
    $('#status').show();
    $('#status').text("Updating Settings...");
    var post_url = "/manage/account_settings.php?";
    post_url += $('#ajax_form').serialize();
    
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'text',
        success: function(data) 
        {
            $('#status').text("Updated settings.");
        },
        error: function()
        {
            $('#status').text("Update failed!");
        }
    });
    return false;
}
function onAddProductSubmit()
{
    function fillProductForm(form_data)
    {
        var artist_id = $('#artist_id').val();
        var product_id = $('#product_id').val();
        var filename = $('#filename').val();
        var name = $('#name').val();
        var category = $('#category option:selected').val();
        var description = $('#description').val();
        var price = $('#price').val();
        var sku = $('#sku').val();
        var size = $('#size').val();
        var color = $('#color').val();
        var situation = $('#situation').val();
        
        form_data.append('artistid',artist_id);
        form_data.append('id',product_id);
        form_data.append('filename',filename);
        form_data.append('name',name);
        form_data.append('origin',category);
        form_data.append('description',description);
        form_data.append('price',price);
        form_data.append('sku',sku);
        form_data.append('size',size);
        form_data.append('color',color);
        form_data.append('situation',situation);
        
        var product_image = document.getElementById('product_image');
        if( product_image.files && product_image.files.length > 0 )
        {
            form_data.append('file',product_image.files[0]);
        }
        form_data.append('submit',situation);
    }
    
    var url = '/manage/addproduct.php';
    return startAjaxUpload(url,fillProductForm);
}
function onAddContentSubmit()
{
    function fillContentForm(form_data)
    {
        g_editor.saveHTML();
        var artist_id = $('#artist_id').val();
        var content_id = $('#content_id').val();
        var name = $('#name').val();
        var body = $('#body').val();
        
        form_data.append('artistid',artist_id);
        form_data.append('id',content_id);
        form_data.append('name',name);
        form_data.append('body',body);
        
        var content_image = document.getElementById('content_image');
        if( content_image.files && content_image.files.length > 0 )
        {
            form_data.append('logo',content_image.files[0]);
        }
        form_data.append('submit','submit');
    }
    
    var url = '/manage/addcontent.php';
    return startAjaxUpload(url,fillContentForm);
}
function clickAddFacebook()
{
    $('#social_config_form').hide();
    $('#status').show();
    $('#status').text("Adding Facebook...");

    var url = "/manage/add_network.php?";
    url += "&artist_id=" + escape(g_artistId);
    url += "&network=facebook";
    window.location.href = url;
}

function clickAddTwitter()
{
    $('#social_config_form').hide();
    $('#status').show();
    $('#status').text("Adding Twitter...");

    var url = "/manage/add_network.php?";
    url += "&artist_id=" + escape(g_artistId);
    url += "&network=twitter";
    window.location.href = url;
}

function validateEditProfile()
{
    var url = $('#url').val();
    if( !url.match(HOSTNAME_REGEX) )
    {
        window.alert("Please enter a valid URL.  A-Z, a-z, -, ., 0-9 are allowed.");
        return false;
    }

    var email = $('#email').val();
    if( !email.match(EMAIL_REGEX) )
    {
        window.alert("Please enter a valid email address.");
        return false;
    }
    return true;
}

function onInviteFriends()
{
    $('#invite_friends_form').hide();
    $('#status').show();
    $('#status').text("Sending Form...");
    var friends = $('#friends_text').val();
    
    var post_url = "/manage/invite_friends.php?";
    post_url += "&artist_id=" + escape(g_artistId);
    post_url += "&friends=" + escape(friends);
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'text',
        success: function(data) 
        {
            $('#status').text("Thank you, your friends will be invited.");
        },
        error: function()
        {
            $('#status').text("Thank you, your friends will be invited.");
        }
    });
    return false;
}

function hoverInQuestion(event)
{
    $('#question_tooltip').show();
    var id = $(event.target).attr('id');
    $('#question_tooltip').text(g_tooltipText[id]);

    var new_offset = $(event.target).offset();
    new_offset.left -= $('#question_tooltip').width()/2 - 40;
    new_offset.top -= $('#question_tooltip').height() + 20;
    $('#question_tooltip').offset(new_offset);
    
}
function hoverOutQuestion(event)
{
    $('#question_tooltip').hide();
}
function setupQuestionTolltips()
{
    $('.tooltip').hover(hoverInQuestion,hoverOutQuestion);
}


