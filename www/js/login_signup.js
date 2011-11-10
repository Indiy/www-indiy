
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

    if( agree && name.length > 0 && email.length > 0 && username.length > 0 && password.length > 0 )
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


