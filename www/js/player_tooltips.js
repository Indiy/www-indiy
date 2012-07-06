
$(document).ready(tooltipsOnReady);

function tooltipsOnReady()
{
    $('.tooltiped').hover(showTooltip,hideTooltip);
}


function showTooltip(event)
{
    var TOOLTIP_OFFSET_MAP = {
        'PHOTOS': -32,
        'MUSIC': -30,
        'VIDEO': -30,
        'LOVE': -20,
        'PLAY': -20,
        'VOLUME': -30
    };

    var text = $(this).attr('tooltip');
    $('#tooltip span').html(text);

    var offset = $(this).offset();
    
    var map_offset = TOOLTIP_OFFSET_MAP[text]; 
    
    var new_offset = {
        left: offset.left + map_offset,
        top: offset.top - 35
    };
    $('#tooltip').css({ left: new_offset.left, top: new_offset.top });
    $('#tooltip').show();
}

function hideTooltip(event)
{
    $('#tooltip').hide();
}
