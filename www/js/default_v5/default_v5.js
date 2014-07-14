(function(){

window.defaultReady = defaultReady;
window.clickRemoteToggle = clickRemoteToggle;
window.clickRemoteShare = clickRemoteShare;
window.clickRemoteMenu = clickRemoteMenu;
window.clickRemotePrev = clickRemotePrev;
window.clickRemoteNext = clickRemoteNext;
window.clickRemoteNavPlayToggle = clickRemoteNavPlayToggle;

function defaultReady(show_social)
{

}
//defaultReady called from playerReady

function clickRemoteToggle()
{
    if( $('.remote_overlay').is(':visible') )
    {
        $('.remote_overlay').hide();
    }
    else
    {
        $('.remote_overlay').show();
        $('.remote_paused').hide();
    }
}
function clickRemoteShare()
{
}
function clickRemoteMenu()
{
}
function clickRemotePrev()
{
    playlistPrevious();
}
function clickRemoteNext()
{
    playlistNext();
}
function clickRemoteNavPlayToggle()
{
    playlistPlayPause();
}

})();