(function(){

window.defaultReady = defaultReady;
window.clickRemoteToggle = clickRemoteToggle;

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

})();