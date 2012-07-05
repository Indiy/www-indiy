
$(document).ready(tooltipsOnReady);

function tooltipsOnReady()
{
    $('.photos.tooltiped').hover(showTooltip,hideTooltip);
}

function showTooltip(event)
{
    var text = $(this).data('tooltip');
    $('#tooltip span').html(text);

    var offset = $(this).offset();
    offset.left += 10;
    offset.top -= 30;
    $('#tooltip').offset(offset);

    $('#tooltip').show();    
}

function hideTooltip(event)
{
    //$('#tooltip').hide();
}
