
var socialize_minimized = true;
var socialize_tab = '';

function toggleSocialTab() 
{
    if( socialize_minimized )
    {
        if( socialize_tab == '' )
        {
            if( $(".socialize .facebook").is(':visible') )
            {
                toggleSocialFB();
            }
            else if(  $(".socialize .twitter").is(':visible') )
            {
                toggleSocialTW();
            }
            else
            {
                toggleSocialEmail();
            }
        }
        else
        {
            openSocialTab();
        }
    }
    else
    {
        closeSocialTab();
    }
}

function toggleSocialFB() 
{
    $(".buttons div").removeClass("active");
    $(".socialize .facebook").addClass("active");
    $(".socialize .body .tab").hide();
    $(".socialize .body #facebook").show();
    
    if( socialize_tab == 'facebook' ) 
    {
        if( socialize_minimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        openSocialTab();
        socialize_tab = 'facebook';
    }
}

function toggleSocialTW() 
{
    $(".buttons div").removeClass("active");
    $(".socialize .twitter").addClass("active");
    $(".socialize .body .tab").hide();
    $(".socialize .body #twitter").show();
    
    if( socialize_tab == 'twitter' ) 
    {
        if( socialize_minimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        openSocialTab();
        socialize_tab = 'twitter';
    }
}

function toggleSocialEmail()
{
    $(".buttons div").removeClass("active");
    $(".socialize .email").addClass("active");
    $(".socialize .body .tab").hide();
    $(".socialize .body #email").show();
    
    if( socialize_tab == 'email' ) 
    {
        if( socialize_minimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        openSocialTab();
        socialize_tab = 'email';
    }
    
}

function toggleSocialShare() 
{
    $(".buttons div").removeClass("active");
    $(".socialize .share").addClass("active");
    $(".socialize .body .tab").hide();
    $(".socialize .body #share").show();
    
    if( socialize_tab == 'share' ) {
        if( socialize_minimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else {
        openSocialTab();
        socialize_tab = 'share';
    }
    
}

function openSocialTab() {
    if( socialize_minimized ) {
        $(".socialize").animate({ bottom: "0" }, 300);
        socialize_minimized = false;
    }
}

function closeSocialTab() {
    $(".buttons div").removeClass("active");
    if( !socialize_minimized ) {
        $(".socialize").animate({ bottom: "-361px" }, 300);
        socialize_minimized = true;
    }
}

function submitNewsletter()
{    
    $('#news_form').hide();
    $('#news_success').show();

    var artist = g_artistId;
    var name = $('#news_name').val();
    var email = $('#news_email').val();
    var mobile = $('#news_mobile').val();
    var submited = "&newsletter=true&artist=" + artist;
    submited += "&name=" + escape(name);
    submited += "&email=" + escape(email);
    submited += "&mobile=" + escape(mobile);
    
    $.post("jplayer/ajax.php", submited, function(repo) {});
}

function sendToFriend()
{
    $('#send_friend_form').hide();
    $('#send_friend_success').show();

    var artist_id = g_artistId;
    var to = $('#send_friend_to').val();
    var from = $('#send_friend_from').val();
    var message = $('#send_friend_message').val();
    
    var d = {
        "artist_id": artist_id,
        "to": to,
        "from": from,
        "message": message
    };
    var postData = JSON.stringify(d);
    jQuery.ajax(
    {
        type: 'POST',
        url: '/data/send_friend.php',
        contentType: 'application/json',
        data: postData,
        processData: false,
        success: function(data) 
        {
        },
        error: function()
        {
        }
    });
}

