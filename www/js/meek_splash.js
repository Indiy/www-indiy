

var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;
var IS_IPHONE = navigator.userAgent.match(/iPhone/i) != null;
var IS_IPOD = navigator.userAgent.match(/iPod/i) != null;
var IS_IOS = IS_IPAD || IS_IPHONE || IS_IPOD;

var IS_ANDROID = navigator.userAgent.match(/Android/i) != null;
var IS_ANDROID_PHONE = navigator.userAgent.match(/Android.*Mobile/i) != null;
var IS_ANDROID_TABLET = IS_ANDROID && !IS_ANDROID_PHONE;

var IS_PHONE = IS_IPOD || IS_IPHONE || IS_ANDROID_PHONE;
var IS_TABLET = IS_ANDROID_TABLET || IS_IPAD;

var IS_WINDOWS = navigator.userAgent.match(/Windows/i) != null;
var IS_CHROME = navigator.userAgent.match(/Chrome/i) != null;
var IS_MOBILE = navigator.userAgent.match(/Mobile/i) != null;
var IS_DESKTOP = !IS_MOBILE;

var IS_IE = false;
var IS_OLD_IE = false;
(function() {
    var ie_match = navigator.userAgent.match(/IE ([^;]*);/);
    if( ie_match != null && ie_match.length > 1 )
    {
        IS_IE = true;
        var ie_version = parseFloat(ie_match[1]);
        if( ie_version < 9.0 )
            IS_OLD_IE = true;
    }
})();

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');
var PHONE_REGEX = new RegExp('^[0-9]{3}-[0-9]{3}-[0-9]{4}$');

var g_updateInterval = false;
var g_videoPlayer = false;
var g_videoPlaying = false;

var g_loadTime = false;

var g_backgroundList = [];

function splashReady()
{
    g_loadTime = new Date();

    if( IS_CHROME )
        $('body').addClass('chrome');
    if( IS_IPAD )
        $('body').addClass('ipad');
    if( IS_IPHONE )
        $('body').addClass('iphone');
    if( IS_IOS )
        $('body').addClass('ios');
    if( IS_MOBILE )
        $('body').addClass('mobile');
    if( IS_DESKTOP )
        $('body').addClass('desktop');

    if( IS_IPHONE )
    {
        $('#input_name').attr('placeholder',"Name");
        $('#input_email').attr('placeholder',"Email Address");
        $('#input_phone').attr('placeholder',"Phone Number");
    }

    if( g_templateParams.bg_file )
    {
        g_backgroundList = [ g_templateParams.bg_file ];

        imageLoadItem(g_backgroundList[0],0,'#splash_bg');
        splashResize();
        $(window).resize(splashResize);
    }

    g_updateInterval = window.setInterval(updateCountdown,250);
    updateCountdown();
    
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


function secsUntilEvent()
{
    var now = new Date();
    
    var time_left = g_eventDate - now;
    
    if( time_left < 0.0 )
    {
        if( time_left > -10*60*1000 )
        {
            maybeReload();
        }
        return 0;
    }
    
    
    return Math.floor(time_left/1000);
}

function maybeReload()
{
    var now = new Date();
    
    var time_since_load = now - g_loadTime;
    
    if( time_since_load > 30000 )
    {
        window.location.reload(true);
    }
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

