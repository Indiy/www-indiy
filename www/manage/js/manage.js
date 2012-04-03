


var HOSTNAME_REGEX = new RegExp('^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?(?:\\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?)*\\.?$');

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

var FB_REGEX = new RegExp('http://www.facebook.com/(.*)','i');


function showProgress(text)
{
    showMessagePopup('#progress',text);
}
function showSuccess(text)
{
    $('#message_popup #success_msg .social_success').hide();
    showMessagePopup('#success',text);
}
function showFailure(text)
{
    showMessagePopup('#failure',text);
}
function showProcessing()
{
    showMessagePopup('#processing');
}
function showUploading()
{
    showMessagePopup('#uploading');
}
function showMessagePopup(selector,text)
{
    showPopup('#message_popup',true);
    $('#message_popup .status_container').hide();
    if( text )
        $('#message_popup ' + selector + ' .status').text(text);
    $('#message_popup ' + selector).show();
}

function onAddUserSubmit()
{
    showProgress("Adding user...");

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
            showSuccess("Label added.");
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;
}

function onStoreSettingsSubmit()
{
    showProgress("Updating record...");

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
            showSuccess("Settings updated.");
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;    
}

function checkElementFileExtensions(file,extensions,error_string)
{
    if( file.files && file.files.length > 0 )
    {
        var file_name = file.files[0].name;
        if( file_name && file_name.length > 0 )
        {
            file_name = file_name.toLowerCase();
            var file_name_parts = file_name.split('.');
            var ext = file_name_parts[file_name_parts.length - 1];
            for( var k in extensions )
            {
                var valid_ext = extensions[k];
                if( valid_ext == ext )
                    return false;
            }
            window.alert(error_string);
            return true;
        }
    }
    return false;
}
function checkElementSize(file,size_limit,error_string)
{
    if( file.files && file.files.length > 0 )
    {
        var size = file.files[0].size;
        if( size && size > size_limit )
        {
            window.alert(error_string);
        }
    }
}
function checkFileExtensions(element_id,extensions,error_string)
{
    var file = $(element_id)[0];
    checkElementFileExtensions(file,extensions,error_string);
}
function onVideoChange()
{
    checkFileExtensions('#video_file',['mov','mp4'],"Please upload video in MP4 or MOV format.");
}

function onImageChange(file)
{
    if( checkElementFileExtensions(file,['png','jpg','gif','jpeg'],"Please upload images in PNG, JPG, or GIF format.") )
        return;
    checkElementSize(file,2*1024*1024,"Please upload images 1280x800 and less than 2MB in size.");
}

function onSocializePublish()
{
    showProgress("Posting update...");
    
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
            showSuccess("Updated Posted.");
        },
        error: function()
        {
            showFailure("Update Failed.");
        }
    });
    return false;
}

function onAccountSettingsSubmit()
{
    showProgress("Updating record...");

    var post_url = "/manage/account_settings.php?";
    post_url += $('#ajax_form').serialize();
    
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
    
    var artist = $('#artist').val();
    if( artist.length == 0 )
    {
        window.alert("Please enter an artist name.");
        return false;
    }
    var logo = document.getElementById('logo');
    if( g_needsImage && ( !logo || !logo.value || logo.value.length == 0 ) )
    {
        window.alert("Please upload a logo image.");
        return false;
    } 
    return true;
}
function onEditProfileSubmit()
{
    if( !validateEditProfile() )
        return false;

    function fillProfileForm(form_data)
    {
        var artist_id = $('#artist_id').val();
        var artist = $('#artist').val();
        var email = $('#email').val();
        var url = $('#url').val();
        var custom_domain = $('#custom_domain').val();
        var listens = $('input[name=listens]:checked').val()
        var newpass = $('#newpass').val();
        var tags = $('#user_tags').val();
        
        form_data.append('artistid',artist_id);
        form_data.append('artist',artist);
        form_data.append('email',email);
        form_data.append('url',url);
        form_data.append('custom_domain',custom_domain);
        form_data.append('listens',listens);
        form_data.append('newpass',newpass);
        form_data.append('tags',tags);
        
        var logo = document.getElementById('logo');
        if( logo.files && logo.files.length > 0 )
        {
            form_data.append('logo',logo.files[0]);
        }
        form_data.append('WriteTags','submit');
    }
    
    var url = '/manage/register.php';
    return startAjaxUpload(url,fillProfileForm);
}
function onInviteFriends()
{
    showProgress("Updating record...");

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
            showSuccess("Thank you, your friends will be invited.");
        },
        error: function()
        {
            showSuccess("Thank you, your friends will be invited.");
        }
    });
    return false;
}



function showChangePassword()
{
    $('#ajax_form').hide();
    $('#change_password').show();
}

function submitChangePassword()
{
    var old_password = $('#old_password').val();
    var new_password = $('#new_password').val();
    var confirm_password = $('#confirm_password').val();
    
    if( new_password.length > 0 
        && confirm_password.length > 0
        && new_password == confirm_password )
    {
        $('#change_password').hide();
        showProgress("Updating record...");
        
        var data = {
            artist_id: g_artistId,
            old_password: old_password, 
            new_password: new_password 
        };
        var postData = JSON.stringify(data);
        jQuery.ajax(
        {
            type: 'POST',
            url: '/manage/data/password.php',
            contentType: 'application/json',
            data: postData,
            processData: false,
            dataType: 'text',
            success: function(data) 
            {
                showSuccess("Password changed.");
            },
            error: function()
            {
                showSuccess("Password change failed!");
            }
        });
    }
    else
    {
        window.alert("Passwords do not match.");        
    }
}

function deleteAccount(id)
{
    var ret = window.confirm("Are you sure you want to delete your account?");
    if( ret )
        ret = window.confirm("This action can not be undone, are you sure?");
    if( ret )
    {
        var url = "/manage/delete_account.php?artist_id=" + id;
        url += "&confirm=true";
        window.location.href = url;
    }
}


