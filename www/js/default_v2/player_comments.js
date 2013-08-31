
var g_currentCommentId = "";
var g_commentUpdateTimer = false;
var g_showingCommentPage = false;
var g_currentCommentCount = 0;

var g_commentReadMap = {};

function commentsReady()
{
    loadReadMap();
}
$(document).ready(commentsReady);

function commentChangedMedia(type,id)
{
    g_currentCommentCount = 0;
    var id_tag = "{0}_id_{1}".format(type,id);
    g_currentCommentId = id_tag;
    
    var comment_url = "{0}/#{1}_id={2}".format(g_artistBaseUrl,type,id);
    var url = "http://graph.facebook.com/{0}".format(escape(comment_url));
    jQuery.ajax(
        {
            type: 'POST',
            url: url,
            dataType: 'jsonp',
            success: function(data)
            {
                var count = 0;
                if( 'comments' in data )
                {
                    count = data.comments;
                }
                updateCommentIconCount(count,id_tag);
            },
            error: function()
            {
                updateCommentIconCount(0,id_tag);
            }
        });
}
function showComments()
{
    $('#popup_tab_list').hide();
    if( g_showingCommentPage )
    {
        hideTab();
        if( g_commentUpdateTimer )
            window.clearInterval(g_commentUpdateTimer);
    }
    else
    {
        hideAllTabs();
        showContentPage();
        g_showingCommentPage = true;
        $('#comment_tab .fb_container').hide();
        var id_tag = g_currentCommentId;
        
        updateReadMap(id_tag,g_currentCommentCount);
        $('#comment_badge_count').html("0");
        $('#comment_badge_count').hide();
        
        var sel = "#comment_tab #{0}".format(id_tag);
        $(sel).show();
        $('#comment_tab').show();
        $('#comment_tab').scrollbar("repaint");
        g_commentUpdateTimer = window.setInterval(periodicCommentTabCheck,500);
    }
}
function updateCommentIconCount(count,id_tag)
{
    g_currentCommentCount = count;
    
    var read_count = 0;
    if( id_tag in g_commentReadMap )
        read_count = g_commentReadMap[id_tag];
    
    var unread_count = count - read_count;
    if( unread_count > 99 )
    {
        $('#comment_badge_count').html("99+");
        $('#comment_badge_count').show();
    }
    else if( unread_count > 0 )
    {
        $('#comment_badge_count').html(unread_count);
        $('#comment_badge_count').show();
    }
    else
    {
        $('#comment_badge_count').hide();
    }
}

function periodicCommentTabCheck()
{
    $('#comment_tab').scrollbar("repaint");
    if( !g_showingCommentPage )
        window.clearInterval(g_commentUpdateTimer);
}

function updateReadMap(tag,count)
{
    try
    {
        g_commentReadMap[tag] = count;
        var json = JSON.stringify(g_commentReadMap);
        window.localStorage["comment_read_map"] = json;
    }
    catch(e) {}
}

function loadReadMap()
{
    try
    {
        var json = window.localStorage["comment_read_map"];
        var map = JSON.parse(json);
        if( map )
        {
            g_commentReadMap = map;
        }
    }
    catch(e) {}
}

function debugClearReadMap()
{
    try
    {
        g_commentReadMap = {};
        var json = JSON.stringify(g_commentReadMap);
        window.localStorage["comment_read_map"] = json;
    }
    catch(e) {}
}

