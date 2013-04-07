
function showAddArtist()
{
    showPopup('#add_user');
}
function showAddLabel()
{
    showPopup('#add_label');
}

function myClose()
{
    window.location.reload();
}

function onAddUserSubmit()
{
    showProgress("Adding user...");

    var artist = $('#add_user #artist').val();
    var url = $('#add_user #url').val();
    var email = $('#add_user #email').val();
    var password = $('#add_user #password').val();
    
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
            g_onCloseCallback = myClose;
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

    var name = $('#add_label #name').val();
    var email = $('#add_label #email').val();
    var password = $('#add_label #password').val();
    
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

function showAccountSettings()
{
    $('#account_settings #player_template').empty();

    var html = "<option value='DEFAULT'>DEFAULT</option>";
    $('#account_settings #player_template').append(html);

    for( name in TEMPLATE_SCHEMA )
    {
        var schema = TEMPLATE_SCHEMA[name];
        var desc = schema.description;
        if( schema.type == 'PLAYER' )
        {
            var html = "<option value='{0}'>New {1} Template</option>".format(name,desc);
            $('#account_settings #player_template').append(html);
        }
    }
    for( var i = 0 ; i < g_templateList.length ; ++i )
    {
        var template = g_templateList[i];
        var schema = TEMPLATE_SCHEMA[template.type];
        if( schema.type == 'PLAYER' )
        {
            var selected = "";
            if( g_artistData.template_id == template.id )
            {
                selected = "selected=selected";
            }
            var desc = template.id + ": " + template.name;

            var html = "<option value='{0}' {1}>{2}</option>".format(template.id,selected,desc);
            $('#account_settings #player_template').append(html);
        }
    }

    $('#account_settings #artist_id').val(g_artistId);
    $('#account_settings #account_type').val(g_artistData.account_type);
    //$('#account_settings #player_template').val(g_artistData.player_template);
    
    if( 'aws' in g_artistData && g_artistData.aws.cloudfront_enable )
    {
        $('#account_settings input[name=cloudfront_enable]:eq(0)').attr('checked','checked');
    }
    else
    {
        $('#account_settings input[name=cloudfront_enable]:eq(1)').attr('checked','checked');
    }
    
    showPopup('#account_settings');
}
function getTemplateId(callback)
{
    var val = $('#account_settings #player_template').val();
    
    if( val == 'DEFAULT' )
    {
        callback(null);
    }
    
    var id = parseInt(val);
    if( isNaN(id) )
    {
        var schema = TEMPLATE_SCHEMA[val];
    
        var args = {
            artist_id: g_artistId,
            type: val,
            name: "New " + schema.description,
            params_json: JSON.stringify(schema.default_params)
        };
        
        jQuery.ajax(
        {
            type: 'POST',
            url:  '/manage/data/template.php',
            data: args,
            dataType: 'json',
            success: function(data) 
            {
                g_templateList.push(data.template);
                callback(data.template.id);
            },
            error: function()
            {
                showFailure("Update Failed");
            }
        });
    }
    else
    {
        callback(id);
    }
}

function onAccountSettingsSubmit()
{
    showProgress("Updating record...");

    getTemplateId(updateAccountSettings);
}

function updateAccountSettings(template_id)
{
    var account_type = $('#account_settings #account_type').val();
    var aws_cloudfront_enable = $('#account_settings input[@name=cloudfront_enable]:checked').val();
    var args = {
        'artist_id': g_artistId,
        'account_type': account_type,
        'template_id': template_id,
        'aws_cloudfront_enable': aws_cloudfront_enable
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url:  '/manage/data/account_settings.php',
        data: args,
        dataType: 'text',
        success: function(data) 
        {
            g_artistData.account_type = account_type;
            g_artistData.player_template = player_template;
            showSuccess("Update Success");
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;
}


