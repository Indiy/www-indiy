
var g_videoLeftIndex = 0;

function videoListScrollLeft()
{
    if( g_videoLeftIndex > 0 )
    {
        g_videoLeftIndex -= 3;
        if( g_videoLeftIndex < 0 )
            g_videoLeftIndex = 0;
        scrollVideoToIndex();
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
        scrollVideoToIndex();
    }
}

$(window).resize(scrollVideoToIndex);

function scrollVideoToIndex()
{
    var item_width = $('#video_list .item').width();
    var dest = item_width * g_videoLeftIndex;
    //$('#video_list .content').scrollLeft(dest);
    $('#video_list .content').animate({scrollLeft: dest});
}

