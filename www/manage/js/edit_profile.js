

function showEditProfile()
{
    clearFileElement('#edit_video #logo');

    $('#edit_profile #artist_id').val(g_artistId);
    $('#edit_profile #artist').val(g_artistData.artist);
    $('#edit_profile #email').val(g_artistData.email);
    $('#edit_profile #url').val(g_artistData.url);
    $('#edit_profile #artist_location').val(g_artistData.location);
    $('#edit_profile #artist_type').val(g_artistData.artist_type);
    if( g_artistData.gender == 'male' )
    {
        $('#edit_profile input[name=artist_gender]:eq(0)').attr('checked','checked');
    }
    else if( g_artistData.gender == 'female' )
    {
        $('#edit_profile input[name=artist_gender]:eq(1)').attr('checked','checked');
    }
    else
    {
        $('#edit_profile input[name=artist_gender]').removeAttr('checked');
    }
    
    if( g_artistData.logo )
    {
        var html = "<img src='{0}' />".format(g_artistData.logo_url);
        $('#edit_profile .image_image').html(html);        
    }
    else
    {
        $('#edit_profile .image_image').empty();
    }
    
    if( g_artistData.account_type == 'PREMIUM' )
    {
        $('#edit_profile #custom_domain_container').show();
        $('#edit_profile #custom_domain').val(g_artistData.custom_domain);
    }
    else
    {
        $('#edit_profile #custom_domain_container').hide();
    }
    $('#edit_profile #user_tags').val(g_artistData.tags);
    if( g_artistData.start_media_type )
    {
        $('#edit_profile #start_media_type').val(g_artistData.start_media_type);
    }

    showPopup('#edit_profile');
    return false;
}


function validateEditProfile()
{
    var url = $('#edit_profile #url').val();
    if( !url.match(HOSTNAME_REGEX) )
    {
        window.alert("Please enter a valid URL.  A-Z, a-z, -, ., 0-9 are allowed.");
        return false;
    }
    
    var email = $('#edit_profile #email').val();
    if( !email.match(EMAIL_REGEX) )
    {
        window.alert("Please enter a valid email address.");
        return false;
    }
    
    var artist = $('#edit_profile #artist').val();
    if( artist.length == 0 )
    {
        window.alert("Please enter an artist name.");
        return false;
    }
    
    var needs_image = !g_artistData.logo;
    
    var logo = $('#edit_profile #logo')[0];
    if( needs_image && ( !logo || !logo.value || logo.value.length == 0 ) )
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
        var artist = $('#edit_profile #artist').val();
        var email = $('#edit_profile #email').val();
        var url = $('#edit_profile #url').val();
        var custom_domain = $('#edit_profile #custom_domain').val();
        var tags = $('#edit_profile #user_tags').val();
        var location = $('#edit_profile #artist_location').val();
        var gender = $('#edit_profile #artist_location').val();
        var artist_location = $('#edit_profile #artist_location').val();
        var artist_type = $('#edit_profile #artist_type option:selected').val();
        var artist_gender = $('#edit_profile input[@name=artist_gender]:checked').val();
        var start_media_type = $('#edit_profile #start_media_type').val();
        
        form_data.append('artistid',g_artistId);
        form_data.append('artist',artist);
        form_data.append('email',email);
        form_data.append('url',url);
        form_data.append('custom_domain',custom_domain);
        form_data.append('tags',tags);
        form_data.append('artist_location',artist_location);
        form_data.append('artist_type',artist_type);
        form_data.append('artist_gender',artist_gender);
        form_data.append('start_media_type',start_media_type);
        
        var logo = $('#edit_profile #logo')[0];
        if( logo.files && logo.files.length > 0 )
        {
            form_data.append('logo',logo.files[0]);
        }
        form_data.append('WriteTags','submit');
        form_data.append('ajax',true);
    }
    
    var url = '/manage/data/profile.php';
    return startAjaxUpload(url,fillProfileForm,onProfileSuccess);
}
function onProfileSuccess(data)
{
    g_artistData = data.artist_data;
    updateProfile();
}

function showChangePassword()
{
    $('#change_password #old_password').val("");
    $('#change_password #new_password').val("");
    $('#change_password #confirm_password').val("");
    showPopup('#change_password');
    return false;
}

function submitChangePassword()
{
    var old_password = $('#change_password #old_password').val();
    var new_password = $('#change_password #new_password').val();
    var confirm_password = $('#change_password #confirm_password').val();
    
    if( new_password.length > 0 
       && confirm_password.length > 0
       && new_password == confirm_password )
    {
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


