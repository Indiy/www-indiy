
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

$(window).resize(function() { scrollVideoToIndex(false); });

function scrollVideoToIndex(animate)
{
    var content_height = $('#video_list .content').height();
    var max_h = 0;

    $('#video_list .content .item').each(function() 
    {
        var h = $(this).height();
        max_h = Math.max(h,max_h);
    });

    var margin = (content_height - max_h)/2 + 10;
    $('#video_list .content .item').css('margin-top',margin + "px");

    var x0 = $('#video_list .item:eq(0)').position().left;
    var x1 = $('#video_list .item:eq(1)').position().left;
    var item_width = x1 - x0;
    var dest = item_width * g_videoLeftIndex;
    if( animate )
        $('#video_list .content').animate({scrollLeft: dest});
    else
        $('#video_list .content').scrollLeft(dest);
}

