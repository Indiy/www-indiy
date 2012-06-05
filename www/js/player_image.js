
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
    
    var ret = {
        'width': width,
        'height': height,
        'margin_top': margin_top,
        'margin_left': margin_left
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
    
    var ret = {
        'width': width,
        'height': height,
        'margin_top': margin_top,
        'margin_left': margin_left
    };
    return ret;
}

