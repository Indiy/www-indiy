
var g_videoLeftIndex = 0;

function clickVideoIcon()
{
    clickBottomIcon("video",clickVideoMediaButton);
    scrollVideoToIndex();
}

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
    var max_left = g_videoList.length - 1;
    
    if( g_videoLeftIndex <= max_left )
    {
        g_videoLeftIndex += 3;
        if( g_videoLeftIndex > max_left )
            g_videoLeftIndex = max_left;
        scrollVideoToIndex(true);
    }
}

$(window).resize(scrollVideoToIndex);

$(document).ready(scrollVideoToIndex);

function scrollVideoToIndex(animate)
{
    var img_w = $('#video_list .content .item .picture img').width();
    var img_h = img_w/1.4;
    $('#video_list .content .item .picture img').css('height',img_h + 'px');

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
    if( animate === true )
        $('#video_list .content').animate({scrollLeft: dest});
    else
        $('#video_list .content').scrollLeft(dest);
}

