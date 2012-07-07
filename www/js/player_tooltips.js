
$(document).ready(tooltipsOnReady);

function tooltipsOnReady()
{
    $('.tooltiped').hover(showTooltip,hideTooltip);
}

var TOOLTIP_OFFSET_MAP = {
    LOVE: -12,
    MUSIC: -9,
    PHOTOS: -15,
    PLAY: 2,
    VIDEO: -11,
    VOLUME: -15,
    PAUSE: 2,
    UNLOVE: -30
};

function showTooltip(event)
{
    var text = $(this).attr('tooltip');
    
    if( text == "PLAY" )
    {
        if( $('#track_play_pause_button').hasClass('playing') )
            text = "PAUSE";
    }
    else if( text == "LOVE" )
    {
        if( $('#love_button').hasClass('love_active') )
            text = "UNLOVE";
    }
    
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
