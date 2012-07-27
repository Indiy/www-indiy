
function fanRegisterAccount(need_confirm)
{
    var register_token = g_registerToken;
    var password = $('#password').val();
    var confirm_password = $('#confirm_password').val();
    
    if( password.length == 0 )
    {
        window.alert("Please enter a password for your fan account.");
        return false;
    }
    
    if( need_confirm && password != confirm_password )
    {
        window.alert("Passwords do not match.");
        return false;
    }
    
    var args = {
        'method': 'register',
        'register_token': register_token,
        'password': password
    };
        
    jQuery.ajax(
        {
            type: 'POST',
            url: '/fan/data/login_signup.php',
            data: args,
            dataType: 'text',
            success: function(text) 
            {
                var data = JSON.parse(text);
                if( data['error'] )
                {
                window.alert("Failed to setup your account, please try again.");
                }
                else
                {
                    window.location = data['url'];
                }
            },
            error: function()
            {
                window.alert("Failed to setup your account, please try again.");
            }
        });
    
    return false;
}

function fanLogin()
{
    var email = $('#login_email').val();
    var password = $('#login_password').val();
    
    if( email.length > 0 && password.length > 0 )
    {
        var args = {
            'method': 'login',
            'email': email,
            'password': password
        };
        jQuery.ajax(
        {
            type: 'POST',
            url: '/fan/data/login_signup.php',
            data: args,
            dataType: 'text',
            success: function(text) 
            {
                var data = JSON.parse(text);
                if( data['error'] )
                {
                    window.alert("Failed to login, please check your email address and password.");
                }
                else
                {
                    window.location = data['url'];
                }
            },
            error: function()
            {
                window.alert("Failed to login, please try again.");
            }
        });
    }
    else
    {
        window.alert("Please enter your email address and password.");
    }
    
    return false;
}

function fanSendRegToken()
{
    var email = $('#register_email').val();
    
    if( email.length > 0 )
    {
        var args = {
            'method': 'send_register_token',
            'email': email
        };
        jQuery.ajax(
        {
            type: 'POST',
            url: '/fan/data/login_signup.php',
            data: args,
            dataType: 'text',
            success: function(text) 
            {
                var data = JSON.parse(text);
                if( data['success'] )
                {
                    $('#login_signup .register_token_sent').show();
                }
                else
                {
                    window.alert("Failed to send alert email, please check your email address.");
                }
            },
            error: function()
            {
                window.alert("Failed to setup your account, please try again.");
            }
        });
        
    }
    else
    {
        window.alert("");
    }
}
