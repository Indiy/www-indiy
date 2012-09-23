

var HOSTNAME_REGEX = new RegExp('^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?(?:[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?)*$');
var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');
var NAME_PLACEHOLDER = "Your Site Name";
var URL_PLACEHOLDER = "Your Site Link";

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


function signupClickInput(input,default_text)
{
    if( $(input).val() == default_text )
    {
        $(input).val("");
        $(input).removeClass("placeholder");
    }
}
function signupBlurInput(input,default_text)
{
    if( $(input).val() == '' )
    {
        $(input).val(default_text);
        $(input).addClass("placeholder");
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
    
    window.alert("Good form");
}
