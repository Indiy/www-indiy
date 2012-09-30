

function orderSavePassword()
{
    var password = $('#password').val();
    
    if( password.length == 0 )
    {
        window.alert("Please enter a password for your fan account.");
        return false;
    }
    
    var args = {
        'method': 'set_password',
        'password': password
    };
        
    jQuery.ajax(
        {
            type: 'POST',
            url: '/data/fan_signup.php',
            data: args,
            dataType: 'text',
            success: function(text) 
            {
                if( data['error'] )
                {
                    window.alert(data['error']);
                }
                else
                {
                    $('#save_password_form').hide();
                    $('#save_password_success').show();
                }
            },
            error: function()
            {
                window.alert("Failed to setup your account, please try again.");
            }
        });
    
    return false;
}
