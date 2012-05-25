
var g_videoLeftIndex = 0;

function videoListScrollLeft()
{
    if( g_videoLeftIndex > 0 )
    {
        g_videoLeftIndex -= 3;
        if( g_videoLeftIndex < 0 )
            g_videoLeftIndex = 0;
        scrollVideoToIndex(true);
    }
}

function videoListScrollRight()
{
    var max_left = 3;
    
    if( g_videoLeftIndex <= max_left )
    {
        g_videoLeftIndex += 3;
        if( g_videoLeftIndex > max_left )
            g_videoLeftIndex = max_left;
        scrollVideoToIndex(true);
    }
}

$(window).resize(scrollVideoToIndex);

function scrollVideoToIndex(animate)
{
    var item_width = $('#video_list .item').width();
    var dest = item_width * g_videoLeftIndex;
    if( animate )
        $('#video_list .content').animate({scrollLeft: dest});
    else
        $('#video_list .content').scrollLeft(dest);
}

