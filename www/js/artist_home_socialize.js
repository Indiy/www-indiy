
var g_socialMinimized = true;
var g_currentSocialTab = '';

function toggleSocialTab() 
{
    if( g_socialMinimized )
    {
        if( g_currentSocialTab == '' )
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
    $(".socialize .body .tab").hide();
    $(".socialize .body #facebook").show();
    
    if( g_currentSocialTab == 'facebook' ) 
    {
        if( g_socialMinimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        g_currentSocialTab = 'facebook';
        openSocialTab();
    }
}

function toggleSocialTW() 
{
    $(".buttons div").removeClass("active");
    $(".socialize .body .tab").hide();
    $(".socialize .body #twitter").show();
    
    if( g_currentSocialTab == 'twitter' ) 
    {
        if( g_socialMinimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        g_currentSocialTab = 'twitter';
        openSocialTab();
    }
}

function toggleSocialEmail()
{
    $(".buttons div").removeClass("active");
    $(".socialize .body .tab").hide();
    $(".socialize .body #email").show();
    
    if( g_currentSocialTab == 'email' ) 
    {
        if( g_socialMinimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        g_currentSocialTab = 'email';
        openSocialTab();
    }
    
}

function toggleSocialShare() 
{
    $(".buttons div").removeClass("active");
    $(".socialize .body .tab").hide();
    $(".socialize .body #share").show();
    
    if( g_currentSocialTab == 'share' ) {
        if( g_socialMinimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        g_currentSocialTab = 'share';
        openSocialTab();
    }
    
}

function openSocialTab() 
{
    if( g_socialMinimized ) 
    {
        g_socialMinimized = false;
        if( g_currentSocialTab != '' )
            $(".socialize ." + g_currentSocialTab).addClass("active");
        $(".socialize").animate({ bottom: "0" }, 300);
    }
}

function closeSocialTab() {
    $(".buttons div").removeClass("active");
    if( !g_socialMinimized ) 
    {
        g_socialMinimized = true;
        $(".socialize").animate({ bottom: "-361px" }, 300);
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

