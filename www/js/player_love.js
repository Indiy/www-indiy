


var g_loveMap = {};
var g_currentLoveTag = false;
var g_currentLoveType = false;

$(document).ready(loveOnReady);

function loveOnReady()
{
    loadLoved();
    musicUpdatePlaylistLove();
}

function loveChangedMusic(id,track_name)
{
    updateLoveLinks("song",track_name);
    loveChangedTag("music",id);
}
function loveChangedVideo(id,track_name)
{
    updateLoveLinks("video",track_name);
    loveChangedTag("video",id);
}
function loveChangedPhoto(id,name)
{
    updateLoveLinks("photo",name);
    loveChangedTag("photo",id);
}
function loveChangedTag(type,id)
{
    var tag = type + "_" + id;
    g_currentLoveTag = tag;
    g_currentLoveType = type;
    loveToggleClass(tag in g_loveMap)
}

function loveToggleClass(is_loved)
{
    if( is_loved )
        $("#love_button").addClass("love_active");
    else
        $("#love_button").removeClass("love_active");
}

function clickLoveIcon()
{
    hideTooltip();

    var is_loved = toggleLoveTag(g_currentLoveTag);
    loveToggleClass(is_loved);

    if( is_loved )
    {
        openBottom();
        clickShareButton();
    }
    musicUpdatePlaylistLove();
}

function isMusicLoved(id)
{
   var tag = "music_" + id;
   return isTagLoved(tag); 
}

function toggleLoveMusic(id)
{
    var tag = "music_" + id;
    return toggleLoveTag(tag);
}

function toggleLoveVideo(id)
{
    var tag = "video_" + id;
    return toggleLoveTag(tag);
}

function isTagLoved(tag)
{
    return tag in g_loveMap;
}

function toggleLoveTag(tag)
{
    if( isTagLoved(tag) )
    {
        removeLoved(tag);
        return false;
    }
    else 
    {
        addLoved(tag);
        return true;
    }
}

function addLoved(tag)
{
    try
    {
        g_loveMap[tag] = true;
        var json = JSON.stringify(g_loveMap);
        window.localStorage["love_map"] = json;
    }
    catch(e) {}
}
function removeLoved(tag)
{
    try
    {
        delete g_loveMap[tag];
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

function updateLoveLinks(type,track_name)
{
    var artist = g_artistName;
    var link_url = g_artistUrl;
    var host = window.location.host;

    var msg = "Check out {0}'s {1} \"{2}\" on MyArtistDNA".format(artist,type,track_name);
    var name = host;
    
    var url = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(host);
    $('#love_fb_link').attr('href',url);
    
    var url = "http://twitter.com/?status=" + encodeURIComponent(msg);
    $('#love_tw_link').attr('href',url);
    
    var url = "http://www.tumblr.com/share/link?url=" + encodeURIComponent(link_url);
    url += "&name=" + encodeURIComponent(name);
    url += "&description=" + encodeURIComponent(msg);
    $('#love_tumblr_link').attr('href',url);
    
    var url = "http://pinterest.com/pin/create/button/?url=" + encodeURIComponent(link_url);
    url += "&description=" + encodeURIComponent(msg);
    $('#love_pin_link').attr('href',url);
    
    var url = "https://plusone.google.com/_/+1/confirm?hl=en&url=" + encodeURIComponent(link_url);
    $('#love_google_link').attr('href',url);
    
    var url = "mailto:?&subject=" + encodeURIComponent(msg);
    $('#love_email_link').attr('href',url);
}



