(function(){

window.default_ready = default_ready;
window.clickMenu = clickMenu;
window.clickClose = clickClose;
window.clickPlus = clickPlus;

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

})();