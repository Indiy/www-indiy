
var FB_PLACEHOLDER = "You can only embed a Facebook Fan Page not a Profile Page!";
var FB_REGEX = new RegExp('http://www.facebook.com/(.*)','i');

function blurFBPageURL(field)
{
    var val = $('#social_config #fb_page_url').val()
    if( val == '' )
    {
        $('#social_config #fb_page_url').val(FB_PLACEHOLDER);
        $('#social_config #fb_page_url').addClass('placeholder');
    }
}
function focusFBPageURL(field)
{
    var val = $('#social_config #fb_page_url').val();
    if( val == FB_PLACEHOLDER )
    {
        $('#social_config #fb_page_url').val('');
    }
    $('#social_config #fb_page_url').removeClass('placeholder');
}

function setSocialNetworkMode(selector,value)
{
    if( value == 'AUTO' )
        $(selector + ':eq(0)').attr('checked','checked');
    else if( value == 'MANUAL' )
        $(selector + ':eq(1)').attr('checked','checked');
    else
        $(selector + ':eq(2)').attr('checked','checked');
}
function showSocialConfigPopup()
{
    $('#social_config #fb_page_url').val(g_artistData.fb_page_url);
    $('#social_config #fb_page_url').val(g_artistData.fb_page_url);
    setSocialNetworkMode('#social_config input[name=fb_setting]',g_artistData.fb_setting);
    setSocialNetworkMode('#social_config input[name=tw_setting]',g_artistData.tw_setting);

    var artist_url = g_artistData.url;
    var embed_url = "{0}{1}&embed=true".format(g_playerUrl,artist_url);
    var embed = '<iframe src="{0}" border="0" width="400" height="600" frameborder="0" name="{1}"></iframe>'.format(embed_url,artist_url);

    $('#social_config #embed_code').val(embed);

    if( g_artistData.twitter )
    {
        $('#social_config #tw_account_name').val(g_artistData.twitter);
        $('#social_config #tw_account_name').show();
        $('#social_config #tw_account_add_container').hide();
    }
    else
    {
        $('#social_config #tw_account_name').hide();
        $('#social_config #tw_account_add_container').show();
    }
    if( g_artistData.facebook )
    {
        $('#social_config #fb_account_name').val(g_artistData.facebook);
        $('#social_config #fb_account_name').show();
        $('#social_config #fb_account_add_container').hide();
    }
    else
    {
        $('#social_config #fb_account_name').hide();
        $('#social_config #fb_account_add_container').show();
    }

    blurFBPageURL();
    showPopup('#social_config');
    return false;
}
function socialConfigSubmit()
{
    showProgress("Updating record...");
    var fb_setting = $('#social_config input[name=fb_setting]:checked').val();
    var tw_setting = $('#social_config input[name=tw_setting]:checked').val();
    var fb_page_url = $('#social_config #fb_page_url').val();
    if( fb_page_url == FB_PLACEHOLDER )
    {
        fb_page_url = "";
    }

    var post_url = "/manage/data/social_config.php?";
    post_url += "&artist_id=" + escape(g_artistId);
    post_url += "&fb_setting=" + fb_setting;
    post_url += "&tw_setting=" + tw_setting;
    post_url += "&fb_page_url=" + escape(fb_page_url);
    post_url += "&ajax=true";
    jQuery.ajax(
    {
        type: 'POST',
        url: post_url,
        dataType: 'json',
        success: function(data) 
        {
            showSuccess("Update Success");
            g_artistData = data.artist_data;
            updateProfile();
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;    
}

function onSocialConfigSave()
{
    var fb_page_url = $('#social_config #fb_page_url').val();
    
    if( fb_page_url && fb_page_url.length > 0 && fb_page_url != FB_PLACEHOLDER )
    {
        if( fb_page_url.startsWith("www.") )
        {
            fb_page_url = "http://" + fb_page_url;
            $('#social_config #fb_page_url').val(fb_page_url);
        }
        else if( fb_page_url.startsWith("facebook.com") )
        {
            fb_page_url = "http://www." + fb_page_url;
            $('#social_config #fb_page_url').val(fb_page_url);
        }
    
        var match = FB_REGEX.exec(fb_page_url);
        if( match && match.length > 1 )
        {
            var username = match[1];
        }
        else
        {
            window.alert("Please enter a valid Facebook Page URL.  Must be a Facebook Page, this tab does not support Facebook Profiles.  Please see the FAQ for more information.");
            return false;
        }
        var fql = 'SELECT page_id FROM page WHERE username = "' + username + '"';
        var check_url = "https://api.facebook.com/method/fql.query?";
        check_url += "&query=" + escape(fql);
        check_url += "&format=json";
        jQuery.ajax(
        {
            type: 'GET',
            url: check_url,
            dataType: 'jsonp',
            success: function(data) 
            {
                if( data && data.length > 0 )
                {
                    socialConfigSubmit();
                }
                else
                {
                    window.alert("Please enter a valid Facebook Page URL.  Must be a Facebook Page, this tab does not support Facebook Profiles.  Please see the FAQ for more information.");
                }
            },
            error: function()
            {
                socialConfigSubmit();
            }
        });
        return false;
    }
    else
    {
        socialConfigSubmit();
    }
}

function clickAddFacebook()
{
    showProgress("Adding Facebook account...");
    
    var url = "/manage/add_network.php?";
    url += "&artist_id=" + escape(g_artistId);
    url += "&network=facebook";
    window.location.href = url;
}

function clickAddTwitter()
{
    showProgress("Adding Twitter account...");
    
    var url = "/manage/add_network.php?";
    url += "&artist_id=" + escape(g_artistId);
    url += "&network=twitter";
    window.location.href = url;
}



