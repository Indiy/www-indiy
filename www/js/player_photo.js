
var g_photoListIndex = 0;

$(document).ready(photoOnReady);
function photoOnReady()
{
    if( g_photoList.length > 0 )
    {
        scrollPhotoToIndex();
        
        $(window).resize(scrollPhotoToIndex);
    }
}


function photoListScrollLeft()
{
    if( g_photoListIndex > 0 )
    {
        g_photoListIndex -= 3;
        if( g_photoListIndex < 0 )
            g_photoListIndex = 0;
        scrollPhotoToIndex(true);
    }
}

function photoListScrollRight()
{
    var max_left = g_photoList.length - 3;
    
    if( g_photoListIndex <= max_left )
    {
        g_photoListIndex += 3;
        if( g_photoListIndex > max_left )
            g_photoListIndex = max_left;
        scrollPhotoToIndex(true);
    }
}

function scrollPhotoToIndex(animate)
{
    var img_w = $('#photo_list .content .item .picture img').width();
    var img_h = img_w/1.4;
    $('#photo_list .content .item .picture img').css('height',img_h + 'px');
    
    var content_height = $('#photo_list .content').height();
    var max_h = 0;
    
    $('#photo_list .content .item').each(function() 
                                         {
                                         var h = $(this).height();
                                         max_h = Math.max(h,max_h);
                                         });
    
    var margin = (content_height - max_h)/2 + 10;
    $('#photo_list .content .item').css('margin-top',margin + "px");
    
    var sel = '#photo_list .item:eq({0})'.format(g_photoListIndex);
    var curr_scroll = $('#photo_list .content').scrollLeft();
    var dest = curr_scroll + $(sel).position().left;
    if( animate === true )
        $('#photo_list .content').animate({scrollLeft: dest});
    else
        $('#photo_list .content').scrollLeft(dest);
}
