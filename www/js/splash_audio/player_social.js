(function(){

$(document).ready(ready);

function ready()
{
    facebookInsert();
    twitterInsert();
    instagramInsert();
}

function facebookInsert()
{
    var height = $('.social_item.facebook').height() - 10;
    var width = $('.social_item.facebook').width();
    
    var facebook_widget = g_templateParams['facebook_widget'];
    var html = false;
    if( facebook_widget && facebook_widget.length > 0 )
    {
        html = facebook_widget.replace(/(.*)(data-height="\w+\d+\w+")(.*)/, '$1 $3');
        html = html.replace(/(.*data-width=)("\w+\d+\w+")(.*)/, '$1"' + width + '" data-height="' + height +'" $3');
    }
    if( html !== false )
    {
        $('.social_item.facebook').html(html);
    }
    else
    {
        $('#top_bar_nav .social.button.facebook').addClass('hidden');
    }
    return html !== false;
}

function twitterInsert()
{
    var height = $('.social_item.twitter').height();
    
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
        $('.social_item.twitter').html(html);
        load_twitter();
    }
    else
    {
        $('#top_bar_nav .social.button.twitter').addClass('hidden');
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
    var instagram_username = g_templateParams['instagram_username'];
 
    if( instagram_username )
    {
        $('.social_item.instagram .instagram-lite').instagramLite({
            clientID: '4456c161ef3849bca5119242b28c64ca',
            username: instagram_username
        });
        return true;
    }
    else
    {
        $('#top_bar_nav .social.button.instagram').addClass('hidden');
        return false;
    }
}

})();