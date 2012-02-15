
var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

var USERNAME_REGEX = new RegExp('^[A-Za-z0-9]*$'); 

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
    $('#login_dialog').hide();
    showPopup('#signup_dialog');
}

function closeSignup()
{
    $('#signup_dialog').fadeOut(100);
    $('#mask').fadeOut(100);
}


function onLoginClick()
{           
    var username = escape( $('#login_username').val() );
    var password = escape( $('#login_password').val() );

    // Send the ajax request.
    jQuery.ajax(
        {
            type: "POST",
            url: "/check_login.php?username="+username+"&password="+password,
            dataType: "json",
            success: function(data)
            {
                var result = data['result'];
                if( result == 0 )
                {   
                    $("#validate-login").html("<span class='ui-error'>Wrong username or password. Please try again.</span>");                    
                     return false;
                }
                else if( result == 1 )
                {
                    window.location.href=data['url'];   
                    return true;
                }
                else
                {           
                    $("#validate-login").html("<span class='ui-error'>Please enter the username and password.</span>");                  
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
    var email = $('#signup_email').val();
    var username = $('#signup_username').val();
    var password = $('#signup_password').val();
    var agree = $('#signup_agree').attr('checked');

    if( !EMAIL_REGEX.match(email) )
    {
        $('#signup_error').show();
        $('#signup_error').text("Please enter a valid email address.");
    }
    else if( !USERNAME_REGEX.match(username) )
    {
        $('#signup_error').show();
        $('#signup_error').text("Please enter a valid username, A-Z, a-z, 0-9 are allowed.");
    }
    else if( agree 
            && name.length > 0 
            && email.length > 0 
            && username.length > 0 
            && password.length > 0 
            )
    {
        var dict = {
            'name': name,
            'email': email,
            'username': username,
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

