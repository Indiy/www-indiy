
$(document).ready(tooltipsOnReady);

function tooltipsOnReady()
{
    $('.tooltiped').hover(showTooltip,hideTooltip);
}

function showTooltip(event)
{
    var text = $(this).attr('tooltip');
    $('#tooltip span').html(text);

    var offset = $(this).offset();
    
    var width = $('#tooltip').width();
    
    var new_offset = {
        left: offset.left - 2,
        top: offset.top - 35
    };
    $('#tooltip').css({ left: new_offset.left, top: new_offset.top });
    $('#tooltip').show();
}

function hideTooltip(event)
{
    $('#tooltip').hide();
}
