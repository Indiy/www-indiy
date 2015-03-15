(function(){

window.clickSubmit = clickSubmit;

window.resizeSignupVideo = resizeSignupVideo;
window.catalogClickPlaylistMediaItem = function() {}

var g_signupBackgroundList = [];

function defaultReady(show_social)
{
    if( IS_IOS || IS_PHONE || IS_TABLET )
    {
        g_mediaAutoStart = false;
    }
    if( IS_EMBED )
    {
        $('body').addClass('embed');
        g_mediaAutoStart = false;
    }

    $('.signup.signup_bg').show();
    $('.content_tab.signup1').addClass('open instant_open');

    if( IS_ANDROID || IS_IOS )
    {
        $('.signup.signup_bg video').hide();
        if( g_templateParams.signup_image_bg )
        {
            g_signupBackgroundList = [ g_templateParams.signup_image_bg ];

            imageLoadItem(g_signupBackgroundList[0],0,'#signup_bg_image');
            signupSplashResize();
            $(window).resize(signupSplashResize);
        }
    }
    else
    {
        $('.signup.signup_bg video').on('play',onSignupVideoPlay);

        resizeSignupVideo();
        $(window).resize(resizeSignupVideo);
        startSignupVideo();

        $('#signup_bg_image').hide();
    }

    var date = moment().format("dddd MMMM DD YYYY");
    $('.today_date').html(date);
}
$(document).ready(defaultReady);

function fixScroll()
{
    if( window.pageXOffset != 0 )
    {
        window.scrollTo(0);
    }
}
function signupSplashResize()
{
    imageResizeBackgrounds(g_signupBackgroundList,'#signup_bg_image');
}

function onSignupVideoPlay()
{
    resizeSignupVideo();
    // Hack!
    window.setTimeout(resizeSignupVideo,1000);
}
function resizeSignupVideo()
{
    var video_jq = $('.signup.signup_bg video');
    if( video_jq.length )
    {
        var signup_bg_jq = $('.signup.signup_bg');
        var width = video_jq.width();
        var height = video_jq.height();

        var bg_width = signup_bg_jq.width();
        var bg_height = signup_bg_jq.height();

        var aspect = width / height;
        var bg_aspect = bg_width / bg_height;

        if( aspect > bg_aspect )
        {
            video_jq.css('width',"auto");
            video_jq.css('height',"100%");

            var delta = video_jq.width() - bg_width;
            var margin = "-{0}px".format(delta/2);
            video_jq.css('margin-left',margin);
            video_jq.css('margin-top',"");
        }
        else
        {
            video_jq.css('width',"100%");
            video_jq.css('height',"auto");
            var delta = video_jq.height() - bg_height;
            var margin = "-{0}px".format(delta/2);
            video_jq.css('margin-top',margin);
            video_jq.css('margin-left',"");
        }
    }
}

function startSignupVideo()
{
    try
    {
        var video_jq = $('.signup.signup_bg video');
        if( video_jq.length )
        {
            var video = video_jq[0];
            if( video.paused )
            {
                video.play();
            }
        }
    }
    catch(e)
    {}
}
function stopSignupVideo()
{
    try
    {
        var video_jq = $('.signup.signup_bg video');
        if( video_jq.length )
        {
            var video = video_jq[0];
            video.pause();
        }
    }
    catch(e)
    {}
}
function clickSubmit()
{
    var email = $('.signup1 input').val();
    if( email )
    {
        sendEmail(email);
        $('.signup1 .form').hide();
        $('.signup1 .success').show();
    }
}
function sendEmail(email)
{
    var args = {
        artist_id: g_artistId,
        email: email,
        comments: "MyChannel home email submission."
    };

    var url = g_trueSiteUrl + "/data/artist_contact.php";
    jQuery.ajax(
    {
        type: 'GET',
        url: url,
        data: args,
        dataType: 'jsonp',
        success: function(data)
        {
        },
        error: function(data)
        {
            console.log("Failed:",data);
        }
    });
}

})();