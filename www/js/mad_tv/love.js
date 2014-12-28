


var g_loveMap = {};

$(document).ready(loadLoved);

function toggleLoveTrack(video)
{
    var title = video.title;
    var ret = false;
    if( title in g_loveMap )
    {
        delete g_loveMap[title];
        ret = false;
    }
    else 
    {
        addLoved(title);
        showLoved(video);
        ret = true;
    }
    saveLoved();
    return ret;
}
function toggleLoveCurrent()
{
    var video = getCurrentVideo();
    if( toggleLoveTrack(video) )
    {
        $('#player .heart').addClass('love');
    }
    else
    {
        $('#player .heart').removeClass('love');
    }
}
function toggleLoveHistory(self,i)
{
    var video_list = getPreviousVideoList();
    var video = video_list[i];
    if( toggleLoveTrack(video) )
    {
        $('#history_loved_' + i).addClass('love');
    }
    else
    {
        $('#history_loved_' + i).removeClass('love');
    }
}

function addLoved(title)
{
    g_loveMap[title] = true;
    saveLoved();
}

function saveLoved()
{
    try
    {
        var json = JSON.stringify(g_loveMap);
        window.localStorage["love_map"] = json;
    }
    catch(e) {}
}

function loadLoved()
{
    try 
    {
        var json = window.localStorage["love_map"];
        var map = JSON.parse(json);
        if( map )
        {
            g_loveMap = map;
        }
    }
    catch(e) {}
}

function showLoved(video)
{
    //hideGenrePicker();
    
    var artist = g_artistName;
    var title = video.title;
    var quoted_title = "\"{0}\"".format(title);
    
    $('#video_love .dialog .header .title span').text(quoted_title);
    
    var link_url = "http://www.myartistdna.tv"
    var host = "www.myartistdna.tv"
    var msg = "Check out {0}'s video {1} on MyArtistDNA.TV".format(artist,quoted_title);
    var name = "MyArtistDNA.TV";
    
    $('#fb_link').attr('href','http://www.facebook.com/sharer/sharer.php?u=' + host);
    $('#tw_link').attr('href','http://twitter.com/?status=' + encodeURIComponent(msg));
    
    var url = "http://www.tumblr.com/share/link?url=" + encodeURIComponent(link_url);
    url += "&name=" + encodeURIComponent(name);
    url += "&description=" + encodeURIComponent(msg);
    $('#tumblr_link').attr('href',url);
    
    var url = "http://pinterest.com/pin/create/button/?url=" + encodeURIComponent(link_url);
    url += "&description=" + encodeURIComponent(msg);
    $('#pin_link').attr('href',url);
    
    var url = "https://plusone.google.com/_/+1/confirm?hl=en&url=" + encodeURIComponent(link_url);
    $('#google_link').attr('href',url);
    
    var url = "mailto:?&subject=" + encodeURIComponent(msg);
    $('#email_link').attr('href',url);
    
    $('#video_love').fadeIn();
}
function hideLoved()
{
    $('#video_love').fadeOut();
}

function loveIsLoved(title)
{
    return title in g_loveMap;
}


