

function default_v2_ready()
{
    var height = $('#mad_tw_timeline').height() - 10;
    
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
        $('#mad_tw_timeline').html(html);
        twitterWidgetLoad();
    }
    else
    {
        g_twitterFeedDisabled = true;
        $('#mad_tw_timeline').hide();
        $('#v2_top_bar .right .show_feed').hide();
        $('#v2_top_bar .right .hide_feed').hide();
    }
}
$(document).ready(default_v2_ready);

function twitterWidgetLoad()
{
    if( typeof twttr != 'undefined' )
    {
        twttr.widgets.load();
        
        if( !IS_PHONE && !IS_IPAD )
        {
            showTwitter();
        }
    }
    else
    {
        window.setTimeout(twitterWidgetLoad,300);
    }
}

function showTwitter()
{
    $('#mad_tw_timeline').show();

    $('#v2_top_bar .right .show_button').show();
    $('#v2_top_bar .right .hide_button').hide();

    $('#v2_top_bar .right .show_feed').hide();
    $('#v2_top_bar .right .hide_feed').show();
    
    closeBottom(false);
}

function hideTwitter()
{
    $('#mad_tw_timeline').hide();

    $('#v2_top_bar .right .show_button').show();
    $('#v2_top_bar .right .hide_button').hide();
}

function showTab($index)
{
    hideTwitter();
    showUserTab($index);
}
function hideTabs()
{
    hideAllTabs();
}
