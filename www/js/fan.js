
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
    
    $('#old_password').val("");
    $('#new_password').val("");
    $('#confirm_password').val("");
}

function fanChangePass()
{
    var old_pass = $('#old_password').val();
    var new_pass = $('#new_password').val();
    var confirm_pass = $('#confirm_password').val();
    
    if( new_pass.length == 0 )
    {
        window.alert("Please enter a new password");
        return false;
    }
    
    if( new_pass != confirm_pass )
    {
        window.alert("Passwords do not match!");
        return false;
    }
    
    var args = {
        method: "change_password",
        old_password: old_pass,
        new_password: new_pass
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url: '/data/fan_signup.php',
        data: args,
        dataType: 'text',
        success: function(text) 
        {
            var data = JSON.parse(text);
            if( data['error'] )
            {
                window.alert(data['detail']);
            }
            else
            {
                window.alert("Password changed.");
                closePopup();
            }
        },
        error: function()
        {
            window.alert("Failed to change password.");
        }
    });
    
    return false;
    
}
