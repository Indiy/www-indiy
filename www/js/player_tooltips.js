
$(document).ready(tooltipsOnReady);

function tooltipsOnReady()
{
    $('.photos.tooltiped').hover(showTooltip,hideTooltip);
}

function showTooltip(event)
{
    var text = $(this).attr('tooltip');
    $('#tooltip span').html(text);

    var offset = $(this).offset();
    
    var new_offset = {
        left: offset.left - 2,
        top: offset.top - 25
    };
    $('#tooltip').offset(new_offset);
    $('#tooltip').show();    
}

function hideTooltip(event)
{
    $('#tooltip').hide();
}
