
var g_photoListIndex = 0;
var g_currentPhotoIndex = 0;

$(document).ready(photoOnReady);
function photoOnReady()
{
    if( g_photoList.length > 0 )
    {
        scrollPhotoListToIndex();
        
        $(window).resize(scrollPhotoListToIndex);
        //$(window).resize(photoResizeBackgrounds);
        
        var opts = {
            panelCount: g_photoList.length,
            resizeCallback: photoResizeBackgrounds,
            onPanelChange: photoPanelChange,
            onPanelVisible: photoPanelVisible
        };
        $('#photo_bg').swipe(opts);
    }
}

function photoHide()
{
    $('#photo_bg').hide();
}
function photoShow()
{
    $('#photo_bg').show();
}

function photoListScrollLeft()
{
    if( g_photoListIndex > 0 )
    {
        g_photoListIndex -= 3;
        if( g_photoListIndex < 0 )
            g_photoListIndex = 0;
        scrollPhotoListToIndex(true);
    }
}

function photoListScrollRight()
{
    var max_left = g_photoList.length - 3;
    
    if( g_photoListIndex < max_left )
    {
        g_photoListIndex += 3;
        if( g_photoListIndex > max_left )
            g_photoListIndex = max_left;
        scrollPhotoListToIndex(true);
    }
}

function scrollPhotoListToIndex(animate)
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

function photoPanelChange(index)
{
    console.log("photoPanelChange: " + index);
}

function photoPanelVisible(index)
{
    var photo = g_photoList[index];
    photoLoadImage(photo,index);
}

function photoChangeId( photo_id )
{
    for( var i = 0 ; i < g_photoList.length ; ++i )
    {
        var photo = g_photoList[i];
        if( photo.id == photo_id )
        {
            photoShowIndex(i);
            return;
        }
    }
}

function photoShowIndex( index ) 
{
    setPlayerMode("photo");
    
    g_currentPhotoIndex = index;
    var photo = g_photoList[index];
    
    loveChangedPhoto(photo.id,photo.name);
    
    photoLoadImage(photo,index);
    photoScrollToCurrentIndex();
    
    g_currentPhotoId = photo.id;
    window.location.hash = '#photo_id=' + g_currentPhotoId; 
    
    playerPhotoInfo(photo.name,photo.location,photo.listens);
}

function photoNext()
{
    var index = g_currentPhotoIndex + 1;
    if( index == g_photoList.length )
        index = 0;
    
    photoShowIndex(index);
}
function photoPrevious()
{
    var index = g_currentPhotoIndex - 1;
    if( index < 0 )
        index = g_photoList.length - 1;
    
    photoShowIndex(index);
}

function photoPreloadImages()
{
    for( var i = 0 ; i < g_photoList.length ; ++i  )
    {
        var photo = g_photoList[i];
        photoLoadImage(photo,i);
    }
}

function photoLoadImage(photo,index)
{
    imageLoadItem(photo,index,'#photo_bg');
}
function photoScrollToCurrentIndex()
{
    var win_width = $('#photo_bg').width();
    var left = win_width * g_currentPhotoIndex;
    $('#photo_bg').scrollLeft(left);
}

function photoResizeBackgrounds()
{
    imageResizeBackgrounds(g_photoList,'#photo_bg');
    photoScrollToCurrentIndex();
}

function photoGetBackgroundParams(photo)
{
    var win_height = $('#photo_bg').height();
    var win_width = $('#photo_bg').width();
    var win_ratio = win_width / win_height;
    
    var img_width = photo.image_data.width;
    var img_height = photo.image_data.height;
    var img_ratio = img_width/img_height;
    
    
    var height = 0;
    var width = 0;
    var margin_left = 0;
    var margin_top = 0;
    
    if( win_ratio < img_ratio )
    {
        height = win_height;
        width = height * img_ratio;
        margin_left = -(width - win_width)/2;
    }
    else
    {
        width = win_width;
        height = width / img_ratio;
        margin_top = -(height - win_height)/2;
    }
    
    var ret = {
        'width': width,
        'height': height,
        'margin_top': margin_top,
        'margin_left': margin_left
    };
    return ret;
}


