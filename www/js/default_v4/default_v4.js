

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
// default_ready is run from player_ready
