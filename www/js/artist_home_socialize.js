
var g_socialMinimized = true;
var g_currentSocialTab = '';

function setupSocialTab()
{
    $('#socialize').mouseover(mouseoverSocialTab);
    $('#socialize').mouseout(mouseoutSocialTab);
}

$(document).ready(setupSocialTab);

function toggleSocialTab() 
{
    if( g_socialMinimized )
    {
        if( g_currentSocialTab == '' )
        {
            if( $("#socalize_fb_holder").is(':visible') )
            {
                toggleSocialFB();
            }
            else if(  $("#socalize_tw_holder").is(':visible') )
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
function openSocialTab() 
{
    if( g_socialMinimized )
    {
        g_socialMinimized = false;
        $("#socialize .button").removeClass("active");
        $("#socialize .button." + g_currentSocialTab).addClass("active");
        $("#socialize").animate({ height: "400px" }, 300);
    }
}

function closeSocialTab() 
{
    if( !g_socialMinimized )
    {
        g_socialMinimized = true;
        $("#socialize .button").removeClass("active");
        $("#socialize").animate({ height: "40px" }, 300);
    }
}

function setActiveSocialTab(name)
{
    $("#socialize .tab").hide();
    $("#socialize .tab#" + name).show();  
    if( g_currentSocialTab == name ) 
    {
        if( g_socialMinimized )
            openSocialTab();
        else
            closeSocialTab();
    }
    else 
    {
        g_currentSocialTab = name;
        openSocialTab();
    }
}

function toggleSocialFB() 
{
    setActiveSocialTab('facebook');
}
function toggleSocialTW() 
{
    setActiveSocialTab('twitter');
}
function toggleSocialEmail()
{
    setActiveSocialTab('email');
}
function toggleSocialShare() 
{
    setActiveSocialTab('share');
}
var g_socialTabTimer = false;
function mouseoverSocialTab()
{
    if( g_socialTabTimer !== false )
    {
        window.cancelTimeout(g_socialTabTimer);
        g_socialTabTimer = false;
    }
    openSocialTab();
}
function mouseoutSocialTab()
{
    g_socialTabTimer = window.setTimeout(closeSocialTab,500);
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

