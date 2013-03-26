

var HOSTNAME_REGEX = new RegExp('^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?(?:[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?)*$');
var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');
var NAME_PLACEHOLDER = "Your Site Name";
var URL_PLACEHOLDER = "Your Site Link";

function signupReady()
{
    signupCheckBox('fan');
    $('#signup #artist_items .site_name input').val(NAME_PLACEHOLDER);
    $('#signup #artist_items .site_link input').val(URL_PLACEHOLDER);
}
$(document).ready(signupReady);

function signupCheckBox(signup_type)
{
    if( signup_type == 'fan' )
    {
        $('#signup .check_item.fan input').attr('checked','checked');
        $('#signup .check_item.artist input').removeAttr('checked');
        $('#signup #artist_items').hide();
    }
    else
    {
        $('#signup .check_item.fan input').removeAttr('checked');
        $('#signup .check_item.artist input').attr('checked','checked');
        $('#signup #artist_items').show();
    }
}


function signupFocusInput(input,default_text)
{
    if( $(input).val() == default_text )
    {
        $(input).val("");
    }
    $(input).removeClass("placeholder");
}
function signupBlurInput(input,default_text)
{
    var val = $(input).val();
    if( val == '' || val == default_text )
    {
        $(input).val(default_text);
        $(input).addClass("placeholder");
    }
    else
    {
        $(input).removeClass("placeholder");
    }
}

function signupSubmit()
{
    if( $('#signup .check_item.fan input').is(':checked') )
    {
        signupFan();
    }
    else
    {
        signupArtist();
    }
}

function signupArtist()
{
    var name = $('#signup #artist_items .site_name input').val();
    var url = $('#signup #artist_items .site_link input').val();
    var email = $('#signup .credentials .email input').val();
    var password = $('#signup .credentials .password input').val();
    
    if( name.length == 0 || name == NAME_PLACEHOLDER )
    {
        window.alert("Please enter a name for your site.");
        return;
    }
    if( url.length == 0 || url == URL_PLACEHOLDER )
    {
        window.alert("Please enter a URL for your site.");
        return;
    }
    if( !url.match(HOSTNAME_REGEX) )
    {
        window.alert("Please enter a valid URL.  A-Z, a-z, -, 0-9 are allowed.");
        return;
    }
    if( email.length == 0 || !email.match(EMAIL_REGEX) )
    {
        window.alert("Please enter a valid email address.");
        return;
    }
    if( password.length == 0 )
    {
        window.alert("Please enter a password for your account.");
        return;
    }
    
    var dict = {
            'name': name,
            'url': url.toLowerCase(),
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
                    window.alert(data['error']);
                }
                else
                {
                    window.location = data['url'];
                }
            },
            error: function()
            {
                window.alert("Registration failed!");
            }
        });
}
function signupFan()
{
    var email = $('#signup .credentials .email input').val();
    var password = $('#signup .credentials .password input').val();
    
    if( email.length == 0 || !email.match(EMAIL_REGEX) )
    {
        window.alert("Please enter a valid email address.");
        return;
    }
    if( password.length == 0 )
    {
        window.alert("Please enter a password for your account.");
        return;
    }
    
    var args = {
        'method': 'signup',
        'email': email,
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
                    window.alert(data['error']);
                }
                else
                {
                    window.location = data['url'];
                }
            },
            error: function()
            {
                window.alert("Registration failed!");
            }
        });
}

function signupFacebook()
{
    if( $('#signup .check_item.fan input').is(':checked') )
    {
        signupFanSocial('facebook');
    }
    else
    {
        signupArtistSocial('facebook');
    }
}
function signupTwitter()
{
    if( $('#signup .check_item.fan input').is(':checked') )
    {
        signupFanSocial('twitter');
    }
    else
    {
        signupArtistSocial('twitter');
    }
}
function signupArtistSocial(network)
{
    var name = $('#signup #artist_items .site_name input').val();
    var url = $('#signup #artist_items .site_link input').val();
    
    if( name.length == 0 || name == NAME_PLACEHOLDER )
    {
        window.alert("Please enter a name for your site.");
        return;
    }
    if( url.length == 0 || url == URL_PLACEHOLDER )
    {
        window.alert("Please enter a URL for your site.");
        return;
    }
    if( !url.match(HOSTNAME_REGEX) )
    {
        window.alert("Please enter a valid URL.  A-Z, a-z, -, 0-9 are allowed.");
        return;
    }
    var dict = {
            'name': name,
            'url': url,
            'network': network
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
                    window.alert(data['error']);
                }
                else
                {
                    window.location = data['url'];
                }
            },
            error: function()
            {
                window.alert("Registration failed!");
            }
        });
}
function signupFanSocial(network)
{
    var args = {
        'method': 'signup',
        'network': network
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
                    window.alert(data['error']);
                }
                else
                {
                    window.location = data['url'];
                }
            },
            error: function()
            {
                window.alert("Registration failed!");
            }
        });
}

