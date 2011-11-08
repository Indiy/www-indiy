
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
    var name = $('#signup_name').val() or "";
    var email = $('#signup_email').val() or "";
    var username = $('#signup_username').val() or "";
    var password = $('#signup_password').val() or "";
    var checkbox = $('#checkBox').attr('checked');

    if( checkbox && name.length > 0 && email.length > 0 && username.length > 0 && password.length > 0 )
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
                dataType: 'json',
                success: function(data) 
                {
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


