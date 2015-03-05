(function(){

window.clickSignup1Next = clickSignup1Next;
window.clickSignup2Next = clickSignup2Next;
window.clickSignup3Next = clickSignup3Next;
window.clickSignup4Next = clickSignup4Next;
window.clickSignup5Next = clickSignup5Next;
window.clickSignup6Next = clickSignup6Next;
window.clickWelcome1Next = clickWelcome1Next;
window.clickWelcome2Next = clickWelcome2Next;

window.clickMenu = clickMenu;
window.clickClose = clickClose;
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
        $('.content_tab.signup1').addClass('open instant_open');
    }
}
$(document).ready(defaultReady);

function clickSignup1Next()
{
    slideInOutContentTab('.signup2');
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
    slideInOutContentTab('.signup5');
}
function clickSignup5Next()
{
    var name = $('.signup5 input').val();
    if( name )
    {
        window.localStorage.signup_name = name;
    }
    slideInOutContentTab('.signup6');
}
function clickSignup6Next()
{
    slideInOutContentTab('.welcome1');
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
    showContentTab('.playlist_tab');
}
function clickClose()
{
    showContentTab('.home_tab');
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
function slideInOutContentTab(name)
{
    $('.content_tab').removeClass('instant_open');
    $('.content_tab.open').addClass('closed');
    $('.content_tab' + name).addClass('open');
}

})();