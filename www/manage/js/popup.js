
var g_popupNumber = 0;
var g_onCloseCallback = false;

function showPopup(selector,immediate)
{
    $('.popup_wrapper').hide();
    if( immediate )
    {
        $(selector).show();
        $('#mask').show();
    }
    else
    {
        $(selector).fadeIn();
        $('#mask').fadeIn();
    }
}
function closePopup()
{
    $('.popup_wrapper').fadeOut();
    $('#mask').fadeOut();
    g_popupNumber++;
    
    if( g_onCloseCallback )
    {
        var callback = g_onCloseCallback;
        g_onCloseCallback = false;
        callback();
    }
}


