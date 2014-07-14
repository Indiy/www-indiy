(function(){

window.defaultReady = defaultReady;
window.clickRemoteToggle = clickRemoteToggle;
window.clickRemoteShare = clickRemoteShare;
window.clickRemoteMenu = clickRemoteMenu;
window.clickRemotePrev = clickRemotePrev;
window.clickRemoteNext = clickRemoteNext;
window.clickRemoteNavPlayToggle = clickRemoteNavPlayToggle;

window.playerShowPaused = playerShowPaused;
window.playerHidePaused = playerHidePaused;

window.catalogClickPlaylistMediaItem = catalogClickPlaylistMediaItem;

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

function playerShowPaused()
{
    g_mediaAutoStart = false;
    $('.remote_paused').show();
}
function playerHidePaused()
{
    g_mediaAutoStart = true;
    $('.remote_paused').hide();
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


})();