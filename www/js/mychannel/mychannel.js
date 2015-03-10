(function(){

window.clickSignup1Next = clickSignup1Next;
window.clickSignup2Next = clickSignup2Next;
window.clickSignup3Next = clickSignup3Next;
window.clickSignup4Next = clickSignup4Next;
window.clickSignup5Next = clickSignup5Next;
window.clickSignup6Next = clickSignup6Next;
window.clickWelcome1Next = clickWelcome1Next;
window.clickWelcome2Next = clickWelcome2Next;

window.slideInRightTab = slideInRightTab;

window.clickMenu = clickMenu;
window.clickMenuClose = clickMenuClose;
window.clickCloseAll = clickCloseAll;

window.clickClose = clickClose;

window.clickContentListItem = clickContentListItem;

window.clickPlaylist = clickPlaylist;
window.clickPlaylistItem = clickPlaylistItem;
window.catalogClickPlaylistMediaItem = catalogClickPlaylistMediaItem;
window.clickShowTab = clickShowTab;
window.clickShowShare = clickShowShare;
window.showContentTab = showContentTab;

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

    var name = window.localStorage.signup_name;
    if( name )
    {
        $('.content_tab.welcome1').addClass('open instant_open');
        $('.user_first_name').html(name);
    }
    else
    {
        $('.signup.signup_bg').show();
        $('.content_tab.signup1').addClass('open instant_open');
        resizeSignupVideo();
        $(window).resize(resizeSignupVideo);
        startSignupVideo();
    }
    var date = moment().format("dddd MMMM DD YYYY");
    $('.today_date').html(date);
}
$(document).ready(defaultReady);

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
        }
        else
        {
            video_jq.css('width',"100%");
            video_jq.css('height',"auto");
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
function clickSignup1Next()
{
    startSignupVideo();
    slideInOutContentTab('.signup3');
}
function clickSignup2Next()
{
    slideInOutContentTab('.signup3');
}
function clickSignup3Next()
{
    slideInOutContentTab('.signup4');
}
function clickSignup4Next()
{
    var phone_number = $('.signup4 input').val();
    if( phone_number )
    {
        window.localStorage.signup_phone_number = phone_number;
        sendSMS(phone_number);
    }

    slideInOutContentTab('.signup5');
}
function clickSignup5Next()
{
    var name = $('.signup5 input').val();
    if( name )
    {
        window.localStorage.signup_name = name;
        $('.user_first_name').html(name);
    }
    slideInOutContentTab('.signup6');
}
function clickSignup6Next()
{
    slideInOutContentTab('.welcome1',function()
    {
        $('.signup.signup_bg').hide();
        stopSignupVideo();
    });
}
function clickWelcome1Next()
{
    slideInOutContentTab('.welcome2');
}
function clickWelcome2Next()
{
    slideInOutContentTab('.home_tab');
}
function clickMenu()
{
    $('.side_menu').removeClass('side_open');
    $('.side_menu').addClass('overlay_open');
}
function clickMenuClose()
{
    $('.side_menu').removeClass('overlay_open');
    $('.side_menu').removeClass('side_open');
}
function clickCloseAll()
{
    clickMenuClose();
    $('.right_tab').removeClass('open');
}
function slideInRightTab(name)
{
    $('.side_menu').addClass('side_open');
    $('.side_menu').removeClass('overlay_open');
    $('.right_tab').removeClass('open');
    $('.right_tab' + name).addClass('open');
}

function clickClose()
{
    console.log("Not implemented");
}

function clickContentListItem(i,j)
{
    if( IS_PHONE )
    {
        var sel = "video#video_{0}_{1}".format(i,j);
        $(sel)[0].play();
    }
    else
    {
        clickCloseAll();
        clickPlaylistItem(i,j);
    }
}

function clickPlaylist(i)
{
    $('.playlist_tab .playlist_list .playlist').removeClass('active');
    var sel = ".playlist_tab .playlist_list #playlist_{0}".format(i);
    $(sel).addClass('active');
}
function clickPlaylistItem(i,j)
{
    $('.playlist_tab .playlist_list .playlist .track_name').removeClass('active');
    var sel = ".playlist_tab .playlist_list #playlist_{0} #track_{1}".format(i,j);
    $(sel).addClass('active');
 
    var playlist = g_playlistList[i];
    playlistChangePlaylist(playlist,j);
    clickClose();
}
function catalogClickPlaylistMediaItem(playlist_index,child_playlist_index,playlist_item_index)
{
    $('#playlist_tab').hide();
    var playlist = g_playlistList[playlist_index];
    
    if( typeof playlist_item_index !== 'undefined'
        && playlist_item_index !== false )
    {
        playlist = playlist.items[child_playlist_index];
    }
    else
    {
        playlist_item_index = child_playlist_index;
    }
    playlistChangePlaylist(playlist,playlist_item_index);
}

function clickShowTab(i)
{
    showContentTab('#user_tab_' + i);
}
function clickShowShare()
{
    showContentTab('.share_tab');
}

function showContentTab(name)
{
    $('.content_tab').removeClass('instant_open');
    $('.content_tab').removeClass('open');
    $('.content_tab' + name).addClass('open');
}
function slideInOutContentTab(name,callback)
{
    $('.content_tab').removeClass('instant_open');
    $('.content_tab.open').addClass('closed');
    $('.content_tab' + name).addClass('open');
    if( callback )
    {
        window.setTimeout(callback,2*1000);
    }
}

function sendSMS(phone_number)
{
    var args = {
        to: phone_number
    };

    var url = g_trueSiteUrl + "/data/send_sms.php";
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
            console.log("SMS failed:",data);
        }
    });
}

})();