

function streamReady()
{
    var height = $('#mad_tw_timeline').height() - 10;
    
    var html = '<a class="twitter-timeline" height="{0}" data-chrome="transparent" href="https://twitter.com/search?q=%23meeklive" data-widget-id="319675836225699842">Tweets about "#meeklive"</a>'.format(height);
    
    $('#mad_tw_timeline').html(html);
    twitterWidgetLoad();
}
$(document).ready(streamReady);

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


function hideTwitter()
{
    $('#mad_tw_timeline').hide();
    
    $('#overlay .top_bar .right .show_feed').show();
    $('#overlay .top_bar .right .hide_feed').hide();
}
function showTwitter()
{
    $('#mad_tw_timeline').show();

    $('#overlay .top_bar .right .show_feed').hide();
    $('#overlay .top_bar .right .hide_feed').show();
}

function showContentPage()
{
}

function hideAllTabs()
{
    hideStore();
}

function updateAnchor()
{
    
}
