


var g_loveMap = {};

$(document).ready(loadLoved);

function toggleLoveTrack(track)
{
    var title = track.title;
    if( title in g_loveMap )
    {
        delete g_loveMap[title];
        return false;
    }
    else 
    {
        addLoved(title);
        showLoved(track);
        return true;
    }
}
function toggleLoveCurrent()
{
    var track = g_videoHistory[0];
    if( toggleLoveTrack(track) )
        $('#player .heart').addClass('love');
    else
        $('#player .heart').removeClass('love');
}
function toggleLoveHistory(self,i)
{
    var track = g_videoHistory[i];
    if( toggleLoveTrack(track) )
        $('#history_loved_' + i).addClass('love');
    else
        $('#history_loved_' + i).removeClass('love');
}

function addLoved(title)
{
    try
    {
        g_loveMap[title] = true;
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

function showLoved(track)
{
    //hideGenrePicker();
    
    var artist = track.artist;
    var video = track.name;
    
    $('#video_love .dialog .header .title span').text('"' + video + '"');
    
    var link_url = "http://www.myartistdna.tv"
    var host = "www.myartistdna.tv"
    var msg = 'Check out ' + artist + '\'s video "' + video + '" on MyArtistDNA.TV';
    var name = 'MyArtistDNA.FM';
    
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



