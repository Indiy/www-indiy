
function loveScrollLeft()
{
    var st = $('#love_list').scrollTop();
    $('#love_list').scrollTop(st - 540);
}

function loveScrollRight()
{
    var st = $('#love_list').scrollTop();
    $('#love_list').scrollTop(st + 540);
}

function showFanLogin()
{
    $('#popup_mask').show();
    $('#login_popup').show();
}

function closePopup()
{
    $('#popup_mask').hide();
    $('.popup_wrapper').hide();
}

function showChangePass()
{
    $('#popup_mask').show();
    $('#change_pass_popup').show();    
}

function fanChangePass()
{
    
}
