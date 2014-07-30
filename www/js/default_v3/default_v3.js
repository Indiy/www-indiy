

function default_v3_ready(show_social)
{
    if( IS_IOS || IS_PHONE || IS_TABLET )
    {
        $('.volume_hidden').addClass('hidden');
        $('body').addClass('hide_volume');
        g_mediaAutoStart = false;
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
        if( true )
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

            hideSocialFeed();
        }
    }
    else
    {
        $('#iphone_show_social_button').addClass('hidden');
        $('#social_box').addClass('hidden');
        $('#v3_top_bar .right .show_feed').addClass('hidden');
        $('#v3_top_bar .right .hide_feed').addClass('hidden');
    }
    
    if( !$('#iphone_top_text').is(":visible") )
    {
        $('#iphone_bottom_section').children().unwrap();
    }
    
    if( IS_EMBED )
    {
        $('body').addClass('embed');
        $('#v3_top_bar .show_tab').addClass('hidden');
        $('#v3_top_bar .hide_tab').addClass('hidden');
        g_mediaAutoStart = false;
    }
    
    var fullscreenEnabled = document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled;
    if( !fullscreenEnabled || IS_PHONE || IS_TABLET )
    {
        $('#tracker_bar .buttons .fullscreen').hide();
    }
}
//$(document).ready(default_v3_ready);

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
    $('#v3_top_bar .right .show_button').show();
    $('#v3_top_bar .right .hide_button').hide();
}

function showSocialFeed()
{
    closeBottom(true);
    hideAllTabs();
    $('#social_box').show();

    showAllShowButtons();
    $('#v3_top_bar .right .show_feed').hide();
    $('#v3_top_bar .right .hide_feed').show();
}
function hideSocialFeed()
{
    $('#social_box').hide();

    showAllShowButtons();
}
function toggleSocialFeed()
{
    hideStore();
    $('#iphone_show_store_button .button').html(templateString('show_store_text','+ SHOW STORE'));

    if( $('#social_box').is(":visible") )
    {
        $('#iphone_show_social_button .button').html(templateString('show_social_feed_text','+ SHOW SOCIAL'));
        $('#social_box').hide();
    }
    else
    {
        $('#iphone_show_social_button .button').html(templateString('hide_social_feed_text','- HIDE SOCIAL'));
        $('#social_box').show();
        $(document).scrollTop($('#iphone_show_social_button').position().top);
    }
}
function toggleStore()
{
    $('#social_box').hide();
    $('#iphone_show_social_button .button').html(templateString('show_social_feed_text','+ SHOW SOCIAL'));

    if( $('#store_tab').is(":visible") )
    {
        $('#iphone_show_store_button .button').html(templateString('show_store_text','+ SHOW STORE'));
        hideStore();
    }
    else
    {
        $('#iphone_show_store_button .button').html(templateString('hide_store_text','- HIDE STORE'));
        showStore();
        $(document).scrollTop($('#iphone_show_store_button').position().top);
    }    
}

function v3_showTab(index)
{
    hideSocialFeed();
    showUserPage(index);
    
    showAllShowButtons();
    $('#v3_top_bar .right #show_tab_' + index).hide();
    $('#v3_top_bar .right #hide_tab_' + index).show();
}
function v3_hideTabs()
{
    hideAllTabs();
    showAllShowButtons();
}
function v3_showStore()
{
    hideSocialFeed();
    showStore();
    
    showAllShowButtons();
    $('#v3_top_bar .right .show_store').hide();
    $('#v3_top_bar .right .hide_store').show();
}
function v3_hideStore()
{
    showAllShowButtons();
    hideStore();
}
function v3_showPlaylistTab()
{
    hideAllTabs();
    hideSocialFeed();
    showContentPage();
    $('#playlist_tab').show();
    catalogExpandCurrent();
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

function toggleFullscreen()
{
    if( !document.fullscreenElement
        && !document.mozFullScreenElement
        && !document.webkitFullscreenElement
        && !document.msFullscreenElement )
    {
        if( document.documentElement.requestFullscreen )
        {
            document.documentElement.requestFullscreen();
        }
        else if( document.documentElement.msRequestFullscreen )
        {
            document.documentElement.msRequestFullscreen();
        }
        else if( document.documentElement.mozRequestFullScreen )
        {
            document.documentElement.mozRequestFullScreen();
        }
        else if( document.documentElement.webkitRequestFullscreen )
        {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    }
    else
    {
        if( document.exitFullscreen )
        {
            document.exitFullscreen();
        }
        else if( document.msExitFullscreen )
        {
            document.msExitFullscreen();
        }
        else if( document.mozCancelFullScreen )
        {
            document.mozCancelFullScreen();
        }
        else if( document.webkitExitFullscreen )
        {
            document.webkitExitFullscreen();
        }
    }
}

function templateString(name,def)
{
    var val = def;
    if( name in g_templateParams )
    {
        val = g_templateParams[name];
    }
    if( !val || val.length == 0 )
    {
        val = def;
    }
    return val;
}
