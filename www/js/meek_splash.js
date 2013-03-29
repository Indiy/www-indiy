

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

