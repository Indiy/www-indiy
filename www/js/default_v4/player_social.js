(function(){

window.twitterInsert = twitterInsert;
window.instagramInsert = instagramInsert;

function twitterInsert()
{
    var height = $('body').height() - 80;
    
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
        $('#social_twitter').html(html);
        load_twitter();
    }
    return html !== false;
}
function load_twitter()
{
    if( typeof twttr != 'undefined' )
    {
        twttr.widgets.load();
    }
    else
    {
        window.setTimeout(load_twitter,300);
    }
}

function instagramInsert()
{
    $('.instagram-lite').instagramLite({
        clientID: '4456c161ef3849bca5119242b28c64ca',
        username: 'kobebryant'
    });
}

})();