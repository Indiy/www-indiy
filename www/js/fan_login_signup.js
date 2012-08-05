
function fanRegisterAccount(need_confirm,successCallback,failureCallback)
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
            url: '/data/fan_signup.php',
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
                    if( successCallback )
                    {
                        successCallback(data);
                        return;
                    }
                    else
                    {
                        window.location = data['url'];
                        return;
                    }
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
    var username = $('#login_email').val();
    var password = $('#login_password').val();
    
    if( username.length > 0 && password.length > 0 )
    {
        var args = {
            'method': 'login',
            'username': username,
            'password': password
        };
        jQuery.ajax(
        {
            type: 'POST',
            url: '/data/login.php',
            data: args,
            dataType: 'json',
            success: function(data)
            {
                if( data['success'] )
                {
                    window.location = data['url'];
                }
                else
                {
                    window.alert("Failed to login, please check your email address and password.");
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
            url: '/data/fan_signup.php',
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
