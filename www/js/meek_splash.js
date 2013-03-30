

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

var PHONE_REGEX = new RegExp('^[0-9]{3}-[0-9]{3}-[0-9]{4}$');

var g_updateInterval = false;

function splashReady()
{
    g_updateInterval = window.setInterval(updateCountdown,250);
    updateCountdown();
}
$(document).ready(splashReady);


function secsUntilEvent()
{
    var now = new Date();
    var event = new Date("Sat, 06 Apr 2013 00:00:00 GMT");

    var time_left = event - now;
    return Math.floor(time_left/1000);
}

function updateCountdown()
{
    var secs_left = secsUntilEvent();

    var seconds = Math.floor(secs_left % 60);
    var minutes = Math.floor( (secs_left / 60) % 60 );
    var hours = Math.floor( (secs_left / (60*60)) % 24 );
    var days = Math.floor( (secs_left / (24*60*60)) );
    
    $('#top_container .top_line .days .time').html(getDigitHtml(days));
    $('#top_container .top_line .hours .time').html(getDigitHtml(hours));
    $('#top_container .top_line .minutes .time').html(getDigitHtml(minutes));
    $('#top_container .top_line .seconds .time').html(getDigitHtml(seconds));
}

function getDigitHtml(value)
{
    var tens = Math.floor(value / 10);
    var ones = value % 10;
    
    var html = "<div>{0}</div><div>{1}</div>".format(tens,ones);
    return html;
}

function onKeyPressPhone(input,event)
{
    var val = input.value;
    var key = String.fromCharCode(event.keyCode);
    
    if( "1234567890-".indexOf(key) == -1 )
        return false;
    
    if( val.length == 3 )
    {
        if( key != '-' )
            val += '-';
    }
    else if( val.length == 7 )
    {
        if( key != '-' )
            val += '-';
    }
    else
    {
        if( key == '-' )
            return false;
    }
    input.value = val;
    return true;
}

function submitSplash()
{
    var name = $('#input_name').val();
    var email = $('#input_email').val();
    var phone = $('#input_phone').val();
    
    if( name.length == 0 || email.length == 0 || phone.length == 0 )
    {
        window.alert("Please fill out all form fields.");
        return;
    }
    
    if( !email.match(EMAIL_REGEX) )
    {
        window.alert("Please enter a valid email address.");
        return;
    }
    
    if( !phone.match(PHONE_REGEX) )
    {
        window.alert("Please enter a valid phone number.");
        return;
    }
    
    var args = {
        artist_id: g_artistId,
        form_tag: g_formTag,
        name: name,
        email: email,
        phone: phone
    };
    
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

