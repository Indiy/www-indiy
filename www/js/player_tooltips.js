
$(document).ready(tooltipsOnReady);

function tooltipsOnReady()
{
    if( !IS_IOS )
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
    UNLOVE: -30,
    MORE: 2
};

var TOOLTIP_CARROT_MARGIN_MAP = {
    LOVE: -6,
    MUSIC: -9,
    PAUSE: -21,
    PHOTOS: -9,
    PLAY: -16,
    UNLOVE: 3,
    VIDEO: -9,
    VOLUME: -9,
    MORE: -16
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
    
    var left = offset.left + map_offset;
    var top = offset.top - 35;
    $('#tooltip').css({ left: left, top: top });

    var margin = TOOLTIP_CARROT_MARGIN_MAP[text];
    var margin_px = "{0}px".format(margin);
    $('#tooltip .carrot').css({ 'margin-left': margin_px });

    $('#tooltip').show();
}

function hideTooltip(event)
{
    $('#tooltip').hide();
}
