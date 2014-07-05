(function(){

window.default_ready = default_ready;
window.clickMenu = clickMenu;
window.clickClose = clickClose;
window.clickPlus = clickPlus;
window.clickPlaylist = clickPlaylist;
window.clickPlaylistItem = clickPlaylistItem;
window.clickShowTab = clickShowTab;
window.clickShowStore = clickShowStore;
window.clickShowSocial = clickShowSocial;
window.clickShowShare = clickShowShare;

function default_ready(show_social)
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
    
}
// default_ready is run from generalOnReady

function clickMenu()
{
    $('.content_tab').hide();
    $('.playlist_tab.content_tab').show();
}
function clickClose()
{
    $('.content_tab').hide();
    $('.home_tab.content_tab').show();
}
function clickPlus()
{
    $('.home_tab .right_menu .extended_menu').toggle();
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
}

function clickShowTab(i)
{
    $('.content_tab').hide();
    $('#user_tab_' + i).show();
}
function clickShowStore()
{
    $('.content_tab').hide();
    $('.store_tab.content_tab').show();
}
function clickShowSocial()
{
    $('.content_tab').hide();
    $('.social_tab.content_tab').show();
}
function clickShowShare()
{
    $('.content_tab').hide();
    $('.share_tab.content_tab').show();
}

})();