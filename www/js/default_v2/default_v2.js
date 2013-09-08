

function default_v2_ready()
{
    if( IS_IOS || IS_PHONE || IS_TABLET )
    {
        $('.volume_hidden').addClass('hidden');
        $('body').addClass('hide_volume');
    }

    if( g_templateParams['tracker_bar_texture'] )
    {
        var url = g_templateParams['tracker_bar_texture'].image;
        var bg = "transparent url({0}) repeat-x".format(url);
        $('#tracker_bar').css('background',bg);
    }

    var twitter_enabled = twitterInsert();
    var facebook_enabled = facebookInsert();
    var instagram_enabled = instagramInsert();
    
    if( twitter_enabled || facebook_enabled || instagram_enabled )
    {
        if( twitter_enabled )
        {
            showTwitter();
        }
        else if( facebook_enabled )
        {
            showFacebook();
        }
        else if( instagram_enabled )
        {
            showInstagram();
        }

        if( !IS_PHONE && !IS_IPAD )
        {
            showSocialFeed();
        }
        else
        {
            hideSocialFeed();
        }
    }
    else
    {
        $('#social_box').addClass('hidden');
        $('#v2_top_bar .right .show_feed').addClass('hidden');
        $('#v2_top_bar .right .hide_feed').addClass('hidden');
    }
}
$(document).ready(default_v2_ready);

function twitterInsert()
{
    var height = $('#social_box .social_twitter').height() - 10;
    
    var twitter_widget = g_templateParams['twitter_widget'];
    var html = false;
    if( twitter_widget && twitter_widget.length > 0 )
    {
        var re = new RegExp("<a[^<]*</a>");
        var m = re.exec(twitter_widget);
        if( m )
        {
            html = m[0];
            var updates = '<a data-chrome="transparent" height="{0}" '.format(height);
            html = html.replace('<a ',updates);
        }
    }
    if( html !== false )
    {
        $('#social_box .social_twitter').html(html);
        twitterWidgetLoad();
    }
    else
    {
        $('#social_box .button.twitter').addClass('hidden');
    }
    return html !== false;
}
function twitterWidgetLoad()
{
    if( typeof twttr != 'undefined' )
    {
        twttr.widgets.load();
    }
    else
    {
        window.setTimeout(twitterWidgetLoad,300);
    }
}
function facebookInsert()
{
    var height = $('#social_box .social_facebook').height() - 10;
    var width = $('#social_box .social_facebook').width();
    
    var facebook_widget = g_templateParams['facebook_widget'];
    var html = false;
    if( facebook_widget && facebook_widget.length > 0 )
    {
        html = facebook_widget.replace(/(.*)(data-height="\w+\d+\w+")(.*)/, '$1 $3');
        html = html.replace(/(.*data-width=)("\w+\d+\w+")(.*)/, '$1"' + width + '" data-height="' + height +'" $3');
    }
    if( html !== false )
    {
        $('#social_box .social_facebook').html(html);
    }
    else
    {
        $('#social_box .button.facebook').addClass('hidden');
    }
    return html !== false;
}

function instagramInsert()
{
    $('#social_box .button.instagram').addClass('hidden');
    return false;
}


function showAllShowButtons()
{
    $('#v2_top_bar .right .show_button').show();
    $('#v2_top_bar .right .hide_button').hide();
}

function showSocialFeed()
{
    closeBottom(false);
    hideAllTabs();
    $('#social_box').show();

    showAllShowButtons();
    $('#v2_top_bar .right .show_feed').hide();
    $('#v2_top_bar .right .hide_feed').show();    
}
function hideSocialFeed()
{
    $('#social_box').hide();

    showAllShowButtons();
}
function toggleSocialFeed()
{
    hideStore();
    $('#iphone_show_store_button .button').html('+ SHOW STORE');

    if( $('#social_box').is(":visible") )
    {
        $('#iphone_show_social_button .button').html('+ SHOW SOCIAL');
        $('#social_box').hide();
    }
    else
    {
        $('#iphone_show_social_button .button').html('- HIDE SOCIAL');
        $('#social_box').show();
        $(document).scrollTop($('#iphone_show_social_button').position().top);
    }
}
function toggleStore()
{
    $('#social_box').hide();
    $('#iphone_show_social_button .button').html('+ SHOW SOCIAL');

    if( $('#store_tab').is(":visible") )
    {
        $('#iphone_show_store_button .button').html('+ SHOW STORE');
        hideStore();
    }
    else
    {
        $('#iphone_show_store_button .button').html('- HIDE STORE');
        showStore();
        $(document).scrollTop($('#iphone_show_store_button').position().top);
    }    
}

function v2_showTab(index)
{
    hideSocialFeed();
    showUserPage(index);
    
    showAllShowButtons();
    $('#v2_top_bar .right #show_tab_' + index).hide();
    $('#v2_top_bar .right #hide_tab_' + index).show();
}
function v2_hideTabs()
{
    hideAllTabs();
    showAllShowButtons();
}
function v2_showStore()
{
    hideSocialFeed();
    showStore();
    
    showAllShowButtons();
    $('#v2_top_bar .right .show_store').hide();
    $('#v2_top_bar .right .hide_store').show();
}
function v2_hideStore()
{
    showAllShowButtons();
    hideStore();
}

function showTwitter()
{
    $('#social_box .social_item').hide();
    $('#social_box .social_twitter').show();
}
function showFacebook()
{
    $('#social_box .social_item').hide();
    $('#social_box .social_facebook').show();
}
function showInstagram()
{
    $('#social_box .social_item').hide();
    $('#social_box .social_instagram').show();
}
