

function default_v2_ready()
{
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
    var width = $('#social_box .social_facebook').width();
    
    var facebook_widget = g_templateParams['facebook_widget'];
    var html = false;
    if( facebook_widget && facebook_widget.length > 0 )
    {
        html = facebook_widget.replace(/(.*data-width=)("\w+\d+\w+")(.*)/, '$1"' + width + '"$3')
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
    $('#social_box').show();

    showAllShowButtons();

    $('#v2_top_bar .right .show_feed').hide();
    $('#v2_top_bar .right .hide_feed').show();
    
    closeBottom(false);
}

function hideSocialFeed()
{
    $('#social_box').hide();

    showAllShowButtons();
}

function v2_showTab(index)
{
    hideSocialFeed();
    showAllShowButtons();
    $('#v2_top_bar .right #show_tab_' + index).hide();
    $('#v2_top_bar .right #hide_tab_' + index).show();

    showUserPage(index);
}
function v2_hideTabs()
{
    hideAllTabs();
    showAllShowButtons();
}
function v2_showStore()
{
    hideSocialFeed();
    showAllShowButtons();
    
    $('#v2_top_bar .right .show_store').hide();
    $('#v2_top_bar .right .hide_store').show();

    showStore();
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
