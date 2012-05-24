
var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;

var IS_IE = false;
var IS_OLD_IE = false;
(function() {
    var ie_match = navigator.userAgent.match(/IE ([^;]*);/);
    if( ie_match != null && ie_match.length > 1 )
    {
        IS_IE = true;
        var ie_version = parseFloat(ie_match[1]);
        if( ie_version < 9.0 )
            IS_OLD_IE = true;
    }
})();


var g_bottomOpen = false;
var g_mediaContent = "music";

function toggleBottom()
{
    if( g_bottomOpen )
        closeBottom();
    else
        openBottom();
}

function openBottom()
{
    g_bottomOpen = true;
    $('#bottom_container').animate({ height: '275px' });
}

function closeBottom()
{
    g_bottomOpen = false;
    $('#bottom_container').animate({ height: '55px' });
}

function maybeAskForEmail()
{
    
}

function clickMusicIcon()
{
    clickBottomIcon("music",clickMusicMediaButton);
}
function clickVideoIcon()
{
    clickBottomIcon("video",clickVideoMediaButton);
}
function clickBottomIcon(name,callback)
{
    if( g_bottomOpen && g_mediaContent == name )
    {
        closeBottom();
    }
    else 
    {
        if( !g_bottomOpen )
            openBottom();
        
        callback();
    }
}

function clickMusicMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#music_list').show();
    g_mediaContent = "music";
}
function clickVideoMediaButton()
{
    $('#media_content_lists .media_list').hide();
    $('#video_list').show();
    g_mediaContent = "video";
}


