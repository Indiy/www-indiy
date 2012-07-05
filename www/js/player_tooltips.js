
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
    offset.left -= 2;
    offset.top -= 35;
    $('#tooltip').offset(offset);

    $('#tooltip').show();    
}

function hideTooltip(event)
{
    $('#tooltip').hide();
}
