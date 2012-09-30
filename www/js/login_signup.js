
var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

var USERNAME_REGEX = new RegExp('^[A-Za-z0-9]*$'); 


var HOSTNAME_REGEX = new RegExp('^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?(?:\\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?)*\\.?$');

function onPasswordKeyPress(myfield,e,callback)
{
    var keycode = 0;
    if( window.event ) 
        keycode = window.event.keyCode;
    else if( e ) 
        keycode = e.which;
    
    if( keycode == 13 )
    {
        var username = $('#login_username').val();
        var password = $('#login_password').val();
        if( username.length > 0 && password.length > 0 )
            callback();
        return false;
    }
    else
    {
        return true;
    }
}

function forgotPasswordSubmit()
{           
    var username = $('#login_username').val();

    var args = {
        method: "send_code",
        email: username
    };

    jQuery.ajax(
        {
            type: "POST",
            url: "/data/forgot_password.php",
            data: args,
            dataType: "json",
            success: function(data)
            {
                if( data['error'] )
                {
                    window.alert(data['error']);
                }
                else
                {
                    window.alert("Email sent.  Please check your email for your password reset link.");
                }
            },
            error: function()
            {
                window.alert("Failed to send email, please check your email address and try again.");
                return false;
            }
        });
} 

function loginFacebook()
{
    loginSocialNetwork('facebook');
}
function loginTwitter()
{
    loginSocialNetwork('twitter');
}
function loginSocialNetwork(network)
{
    var url = "/data/login.php?network={0}".format(network);

    jQuery.ajax(
        {
            type: "GET",
            url: url,
            dataType: "jsonp",
            success: function(data)
            {
                var error = data['error'];
                if( error )
                {
                    window.alert(error);
                }
                else
                {
                    window.location.href = data['url'];
                }
            },
            error: function()
            {
                window.alert("Failed to contact login server.");
            }
        });
}
function loginSubmit()
{           
    var username = $('#login_username').val();
    var password = $('#login_password').val();

    
    if( username.length == 0 || password.length == 0 )
    {
        window.alert("Please enter an email address and password.");
        return false;
    }

    var args = {
        'method': 'login',
        'username': username,
        'password': password
    };

    jQuery.ajax(
        {
            type: "POST",
            url: "/data/login.php",
            data: args,
            dataType: "json",
            success: function(data)
            {
                if( data['success'] )
                {   
                    window.location.href = data['url'];
                    return true;
                }
                else
                {
                    window.alert("Wrong email address or password. Please try again.");
                    return false;
                }
            },
            error: function()
            {
                window.alert("Login Error. Please try again.");
                return false;
            }
        });
}

