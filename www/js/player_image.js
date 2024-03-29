

function imageLoadItem(item,index,root_tag)
{
    var image = item.image;
    var color = item.bgcolor;
    if( typeof color == 'undefined' )
        color = item.bg_color;
    var bg_style = item.bg_style;
    
    if( !item.loaded )
    {
        item.loaded = true;
        var holder = $(root_tag + ' #image_holder_' + index);
        
        var win_height = $(root_tag).height();
        var win_width = $(root_tag).width();
        
        holder.css("background-color", "#" + color);
        if( bg_style == 'STRETCH' )
        {
            var image_params = imageGetStretchParams(item,root_tag);
            
            var img_style = "width: {0}px; height: {1}px;".format(image_params.width,image_params.height);
            
            var img_url = image_params.img_url;
            
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
        else if( bg_style == 'LETTERBOX' )
        {
            var image_params = imageGetLetterboxParams(item,root_tag);
            
            var img_style = "width: {0}px; height: {1}px;".format(image_params.width,image_params.height);
            
            var img_url = image_params.img_url;
            
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
            var img_url = image;
        
            holder.css("background-image","url({0})".format(img_url));
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


function imageResizeBackgrounds(list,root_tag)
{
    for( var i = 0 ; i < list.length ; ++i )
    {
        var item = list[i];
        
        if( !item.loaded )
            continue;
        
        var bg_style = item.bg_style;
        if( bg_style == 'STRETCH' || bg_style == 'LETTERBOX' )
        {
            var image_params;
            
            if( bg_style == 'STRETCH' )
                image_params = imageGetStretchParams(item,root_tag);
            else if( bg_style == 'LETTERBOX' )
                image_params = imageGetLetterboxParams(item,root_tag);
        
            var win_height = $(root_tag).height();
            var win_width = $(root_tag).width();
            
            var div_holder = $(root_tag + ' #image_holder_' + i + ' div');
            var image = $(root_tag + ' #image_holder_' + i + ' div img');
            
            div_holder.height(win_height);
            div_holder.width(win_width);
            
            image.width(image_params.width);
            image.height(image_params.height);
            
            div_holder.css("margin-left",image_params.margin_left + "px");
            div_holder.css("margin-top",image_params.margin_top + "px");
            div_holder.css("padding-right",-image_params.margin_left + "px");
            div_holder.css("padding-bottom",-image_params.margin_top + "px");
        }
    }
}

function imageGetStretchParams(item,root_tag)
{
    var bg_justify = "CENTER";
    if( 'bg_justify' in item )
        bg_justify = item.bg_justify;

    var win_height = $(root_tag).height();
    var win_width = $(root_tag).width();
    var win_ratio = win_width / win_height;
    
    var img_width = item.image_data.width;
    var img_height = item.image_data.height;
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
    
    if( bg_justify == "TOP" )
        margin_top = 0;
    
    var img_url = getImgUrl(item,root_tag);
    
    var ret = {
        'width': width,
        'height': height,
        'margin_top': margin_top,
        'margin_left': margin_left,
        'img_url': img_url
    };
    return ret;
}

function imageGetLetterboxParams(item,root_tag)
{
    var win_height = $(root_tag).height();
    var win_width = $(root_tag).width();
    var win_ratio = win_width / win_height;
    
    var img_width = item.image_data.width;
    var img_height = item.image_data.height;
    var img_ratio = img_width/img_height;
    
    
    var height = 0;
    var width = 0;
    var margin_left = 0;
    var margin_top = 0;
    
    if( win_ratio > img_ratio )
    {
        height = win_height;
        width = height * img_ratio;
        margin_left = (win_width - width)/2;
    }
    else
    {
        width = win_width;
        height = width / img_ratio;
        margin_top = (win_height - height)/2;
    }
    
    var img_url = getImgUrl(item,root_tag);
    
    var ret = {
        'width': width,
        'height': height,
        'margin_top': margin_top,
        'margin_left': margin_left,
        'img_url': img_url
    };
    return ret;
}
function getImgUrl(item,root_tag)
{
    var win_width = $(root_tag).width();
    var img_width = item.image_data.width;

    var tim_width = win_width;
    if( IS_IOS )
    {
        tim_width = 2*tim_width;
    }
    
    var img_url = item.image;
    if( tim_width < (img_width*0.8) )
    {
        // If we're doing more than a 20% downscale, do it on the server
        
        switch( tim_width )
        {
            case 320:
            case 480:
            case 640:
            case 768:
            case 800:
            case 960:
            case 1024:
            case 1080:
            case 1280:
            case 1440:
            case 1536:
            case 1600:
            case 2048:
                // Don't round up natural values
                break;
            default:
                // Round up wierd values to closest 100 to make server chache work better
                tim_width = Math.ceil(tim_width/100)*100;
                break;
        }
        
        var key = "w" + tim_width;

        if( item.image_extra
           && item.image_extra.alts
           && key in item.image_extra.alts )
        {
            img_url = g_artistFileBaseUrl + item.image_extra.alts[key];
        }
    }
    return img_url;
}

