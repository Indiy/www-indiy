

function streamReady()
{
    var height = $('#mad_tw_timeline').height();
    
    var html = '<a class="twitter-timeline" height="{0}" href="https://twitter.com/search?q=%23meeklive" data-widget-id="319675836225699842">Tweets about "#meeklive"</a>'.format(height);
    
    $('#mad_tw_timeline').html(html);
    //twttr.widgets.load();
}
$(document).ready(streamReady);


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
