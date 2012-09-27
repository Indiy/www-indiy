
var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

var USERNAME_REGEX = new RegExp('^[A-Za-z0-9]*$'); 


var HOSTNAME_REGEX = new RegExp('^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?(?:\\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?)*\\.?$');


function showPopup(name)
{
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();
	
    $('#mask').css({'width':maskWidth,'height':maskHeight});
    
    $('#mask').fadeIn(600);	
    $('#mask').fadeTo("slow",0.5);	
	
    var winH = $(window).height();
    var winW = $(window).width();
    
    var dialogHeight = $(name).height();
    var dialogWidth = $(name).width();
    
    var top = winH / 2 - dialogHeight / 2;
    var left = winW / 2 - dialogWidth / 2;
    
    $(name).css('top',top);
    $(name).css('left',left);
	
    $(name).fadeIn(600);     
}

function showLogin()
{
    showPopup('#login_dialog');
}

function closeLogin()
{
    $('#login_dialog').fadeOut(100);
    $('#mask').fadeOut(100);
}

function showSignup()
{
    window.location.href = "/signup.php";
    return;

    $('#login_dialog').hide();
    showPopup('#signup_dialog');
}

function closeSignup()
{
    $('#signup_dialog').fadeOut(100);
    $('#mask').fadeOut(100);
}

function onPasswordKeyPress(myfield,e,callback)
{
    var keycode = 0;
    if( window.event ) 
        keycode = window.event.keyCode;
    else if( e ) 
        keycode = e.which;
    
    if( keycode == 13 )
    {
        var username = $('#login_dialog #username').val();
        var password = $('#login_dialog #password').val();
        if( username.length > 0 && password.length > 0 )
            callback();
        return false;
    }
    else
    {
        return true;
    }
}

function onLoginClick()
{           
    var username = $('#login_dialog #username').val();
    var password = $('#login_dialog #password').val();

    
    if( username.length == 0 || password.length == 0 )
    {
        $("#validate-login").html("<span class='ui-error'>Please enter the username and password.</span>");
        return false;
    }

    var args = {
        'method': 'login',
        'username': username,
        'password': password
    };

    // Send the ajax request.
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
                    $("#validate-login").html("<span class='ui-error'>Wrong username or password. Please try again.</span>");
                    return false;
                }
            },
            error: function()
            {
                $("#validate-login").html("<span class='ui-error'>Login Error. Please try again.</span>");                   
                return false;
            }
        });
}

function onSignupClick()
{
    var name = $('#signup_name').val();
    var url = $('#signup_url').val();
    var email = $('#signup_email').val();
    var password = $('#signup_password').val();
    var agree = $('#signup_agree').attr('checked');

    if( !email.match(EMAIL_REGEX) )
    {
        $('#signup_error').show();
        $('#signup_error').text("Please enter a valid email address.");
    }
    else if( !url.match(HOSTNAME_REGEX) )
    {
        $('#signup_error').show();
        $('#signup_error').text("Please enter a valid URL.  A-Z, a-z, -, ., 0-9 are allowed.");
    }
    else if( agree 
            && name.length > 0 
            && email.length > 0 
            && password.length > 0
            && url.length > 0
            )
    {
        var dict = {
            'name': name,
            'url': url,
            'email': email,
            'password': password
        };
        var data = JSON.stringify(dict);
        jQuery.ajax(
            {
                type: 'POST',
                url: '/data/signup.php',
                contentType: 'application/json',
                data: data,
                processData: false,
                dataType: 'text',
                success: function(text) 
                {
                    var data = JSON.parse(text);
                    if( data['error'] )
                    {
                        $('#signup_error').show();
                        $('#signup_error').text(data['error']);
                    }
                    else
                    {
                        window.location = data['url'];
                    }
                },
                error: function()
                {
                    $('#signup_error').show();
                    $('#signup_error').text("Registration failed!");
                }
            });
    }
    else
    {
        $('#signup_error').show();
        $('#signup_error').text("Please fill up all required fields.");
    }
    return false;
}

function onForgotPasswordClick()
{           
    var username = escape( $('#login_username').val() );

    // Send the ajax request.
    jQuery.ajax(
        {
            type: "POST",
            url: "/data/forgot_password.php?email="+username,
            dataType: "json",
            success: function(data)
            {
                var error = data['error'];
                if( error == 0 )
                {
                    $('.instructions').text(data['msg']);
                    $('.login').hide();
                    $('.email_header').hide();
                    $('#login_username').hide();
                }
                else
                {           
                    $('#validate-login').html("<span class='ui-error'>Couldn't find your user. Please try again.</span>");                  
                    return false;
                }
            },
            error: function()
            {
                $('#validate-login').html("<span class='ui-error'>Couldn't find your user. Please try again.</span>");                   
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

