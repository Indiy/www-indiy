

var g_updateInterval = false;
var g_videoPlayer = false;
var g_videoPlaying = false;

var g_loadTime = false;

var g_backgroundList = [];

function splashReady()
{
    g_loadTime = new Date();

    if( g_templateParams.bg_file )
    {
        g_backgroundList = [ g_templateParams.bg_file ];

        imageLoadItem(g_backgroundList[0],0,'#splash_bg');
        splashResize();
        $(window).resize(splashResize);
    }

    if( IS_MOBILE )
    {
        window.scrollTo(0,1);
    }
}
$(document).ready(splashReady);

function splashResize()
{
    imageResizeBackgrounds(g_backgroundList,'#splash_bg');
}

function onKeyPressPhone(input,event,sel)
{
    var val = input.value;
    var key = String.fromCharCode(event.keyCode);
    
    if( "1234567890-".indexOf(key) == -1 )
        return false;
    
    if( val.length == 3 )
    {
        if( key != '-' )
            input.value += '-';
    }
    else if( val.length == 7 )
    {
        if( key != '-' )
            input.value += '-';
    }
    else
    {
        if( key == '-' )
            return false;
    }
    return true;
}

function submitSplash()
{
    var name = $('#input_name').val();
    var email = $('#input_email').val();
    var phone = $('#input_phone').val();
    
    if( email.length == 0 )
    {
        window.alert("Please fill out all form fields.");
        return;
    }
    
    if( !email.match(EMAIL_REGEX) )
    {
        window.alert("Please enter a valid email address.");
        return;
    }
    /*
    if( !phone.match(PHONE_REGEX) )
    {
        window.alert("Please enter a valid phone number.");
        return;
    }
    */
    
    var args = {
        artist_id: g_artistId,
        form_tag: g_formTag,
    };
    
    var form_data = {};
    if( name )
    {
        form_data.name = name;
    }
    if( email )
    {
        form_data.email = email;
    }
    if( phone )
    {
        form_data.phone = phone;
    }
    
    args.form_data_json = JSON.stringify(form_data);
    
    var url = "{0}/data/artist_form.php".format(g_apiBaseUrl);
    
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: args,
        dataType: 'jsonp',
        success: function(data) 
        {
            splashFormDone();
        },
        error: function()
        {
            window.alert("Form submission failed, please try again later.");
        }
    });
}

function splashFormDone()
{
    $('.form_hide').hide();
    $('.success_show').show();
}

