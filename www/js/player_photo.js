
var g_photoListIndex = 0;

$(document).ready(photoOnReady);
function photoOnReady()
{
    if( g_photoList.length > 0 )
    {
        scrollPhotoToIndex();
        
        $(window).resize(scrollPhotoToIndex);
        $(window).resize(photoResizeBackgrounds);
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
        scrollPhotoToIndex(true);
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
    $('#photo_bg .image_holder').hide();
    $('#photo_bg #image_holder_' + index).show();
    
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
    return;

    var image = photo.image;
    var color = photo.bg_color;
    var bg_style = photo.bg_style;
    
    if( !photo.loaded )
    {
        photo.loaded = true;
        var holder = $('#photo_bg #image_holder_' + index);
        
        var win_height = $('#photo_bg').height();
        var win_width = $('#photo_bg').width();
        
        holder.css("background-color", "#" + color);
        if( bg_style == 'STRETCH' )
        {
            var image_params = photoGetBackgroundParams(photo);
            
            var img_style = "width: {0}px; height: {1}px;".format(image_params.width,image_params.height);
            var img_url = "/timthumb.php?src={0}&w={1}&zc=0&q=100".format(image,win_width);
            
            var div_holder_style = "";
            div_holder_style += "height: {0}px; ".format(win_height);
            div_holder_style += "width: {0}px; ".format(win_width);
            div_holder_style += "margin-top: {0}px; ".format(image_params.margin_top);
            div_holder_style += "margin-left: {0}px; ".format(image_params.margin_left);
            div_holder_style += "padding-bottom: {0}px; ".format(-image_params.margin_top);
            div_holder_style += "padding-right: {0}px; ".format(-image_params.margin_left);
            
            var html = "";
            html += "<div style='{0}'>".format(div_holder_style);
            html += "<img src='{0}' style='{1}' />".format(img_url,img_style);
            html += "</div>"
            holder.html(html);
            
            holder.css("background-image","none");
            holder.css("background-repeat","no-repeat");
            holder.css("background-position","center center");
        }
        else if( bg_style == 'CENTER' )
        {
            holder.css("background-image","url(" + image + ")");
            holder.css("background-repeat","no-repeat");
            holder.css("background-position","center center");
            var html = "<div style='width: 100%; height: {0}px;'></div>".format(win_height);
            holder.html(html);
        }
        else if( bg_style == 'TILE' )
        {
            holder.css("background-image","url(" + image + ")");
            holder.css("background-repeat","repeat");
            holder.css("background-position","center center");
            var html = "<div style='width: 100%; height: {0}px;'></div>".format(win_height);
            holder.html(html);
        }
    }            
}

function photoResizeBackgrounds()
{
    imageResizeBackgrounds(g_photoList,'#photo_bg');
    return;

    for( var i = 0 ; i < g_photoList.length ; ++i )
    {
        var photo = g_photoList[i];
        
        if( !photo.loaded )
            continue;
        
        var bg_style = photo.bg_style;
        if( bg_style == 'STRETCH' )
        {
            var win_height = $('#photo_bg').height();
            var win_width = $('#photo_bg').width();
            
            var div_holder = $('#photo_bg  #image_holder_' + i + ' div');
            var image = $('#photo_bg #image_holder_' + i + ' div img');
            
            div_holder.height(win_height);
            div_holder.width(win_width);
            
            var image_params = photoGetBackgroundParams(photo);
            
            image.width(image_params.width);
            image.height(image_params.height);
            
            //div_holder.scrollLeft(-image_params.margin_left);
            //div_holder.scrollTop(-image_params.margin_top);
            div_holder.css("margin-left",image_params.margin_left + "px");
            div_holder.css("margin-top",image_params.margin_top + "px");
            div_holder.css("padding-right",-image_params.margin_left + "px");
            div_holder.css("padding-bottom",-image_params.margin_top + "px");
        }
    }
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


