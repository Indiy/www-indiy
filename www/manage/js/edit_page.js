

function updatePages()
{
    for( var i = 0 ; i < g_pageList.length ; ++i )
    {
        var page = g_pageList[i];
        var sel = '#page_playlist_list_ul_' + i;
        $(sel).empty();
        for( var j = 0 ; j < page.playlists.length ; ++j )
        {
            var page_playlist = page.playlists[j];
            var class_name = i % 2 == 0 ? 'odd' : '';

            var html = "";
            html += "<li id='arrayorder_{0}' class='{1}'>".format(page_playlist.page_playlist_id,class_name);
            html += "<span class='title'>";
            html += page_playlist.playlist_name;
            html += "</span>";
            html += "<span class='delete'><a onclick='deletePagePlaylist({0},{1});'></a></span>".format(i,j);
            html += "</li>";
            $(sel).append(html);
        }
        
        sel = '#page_tab_list_ul_' + i;
        $(sel).empty();
        for( var j = 0 ; j < page.tabs.length ; ++j )
        {
            var page_tab = page.tabs[j];
            var class_name = i % 2 == 0 ? 'odd' : '';

            var html = "";
            html += "<li id='arrayorder_{0}' class='{1}'>".format(page_tab.page_tab_id,class_name);
            html += "<span class='title'>";
            html += page_tab.tab_name;
            html += "</span>";
            html += "<span class='delete'><a onclick='deletePageTab({0},{1});'></a></span>".format(i,j);
            html += "</li>";
            $(sel).append(html);
        }

    }
    setupSortableList('ul.page_playlist_list_sortable',"/manage/data/page_playlists.php");
    setupSortableList('ul.page_tab_list_sortable',"/manage/data/page_tabs.php");
}
$(document).ready(updatePages);

var g_pageIndex = false;
function showPagePopup(index)
{
    var template_id = false;
    if( index !== false )
    {
        var page = g_pageList[index];
        g_pageIndex = index;
        template_id = page.template_id;
        $('#edit_page #uri').val(page.uri);
        fillArtistFileIdSelect('#edit_page #favicon_id','ALL',page.favicon_id);
    }
    else
    {
        g_pageIndex = false;
        $('#edit_page #uri').val("");
        fillArtistFileIdSelect('#edit_page #favicon_id','ALL',false);
    }
    
    $('#edit_page #template_list').empty();
    var html = "<option value='0'>DEFAULT</option>";
    $('#edit_page #template_list').append(html);
    for( var i = 0 ; i < g_templateList.length ; ++i )
    {
        var template = g_templateList[i];
        var schema = TEMPLATE_SCHEMA[template.type];
        if( schema.type == 'PLAYER' )
        {
            var selected = "";
            if( template_id == template.id )
            {
                selected = "selected=selected";
            }
            var desc = template.id + ": " + template.name;

            var html = "<option value='{0}' {1}>{2}</option>".format(template.id,selected,desc);
            $('#edit_page #template_list').append(html);
        }
    }
    showPopup('#edit_page');
}

function onPageSubmit()
{
    showProgress("Adding page...");

    var uri = $('#edit_page #uri').val();
    var template_id = $('#edit_page #template_list').val();
    var favicon_id = $('#edit_page #favicon_id').val();
    
    if( uri.length == 0 )
    {
        window.alert("Please enter a uri for your page.");
        return;
    }
    
    var url = "/manage/data/pages.php";
    var data = {
        artist_id: g_artistId,
        uri: uri,
        template_id: template_id,
        favicon_id: favicon_id
    };
    if( g_pageIndex !== false )
    {
        var page = g_pageList[g_pageIndex];
        data.page_id = page.page_id;
    }
    
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            window.location.reload();
        },
        error: function()
        {
            showFailure("Page update failed.  Please choose a unique URI for your page.");
        }
    });
    return false; 
}

function showPageItemPopup(page_index)
{
    g_pageIndex = page_index;
    
    var html = "<option value='0'>None</option>";
    $('#add_page_item #playlist_list').html(html);
    for( var i = 0 ; i < g_playlistList.length ; ++i )
    {
        var playlist = g_playlistList[i];
        var html = "<option value='{0}'>{1}</option>".format(playlist.playlist_id,playlist.name);
        $('#add_page_item #playlist_list').append(html);
    }
    
    var html = "<option value='0'>None</option>";
    $('#add_page_item #tab_list').html(html);
    for( var i = 0 ; i < g_tabList.length ; ++i )
    {
        var tab = g_tabList[i];
        var html = "<option value='{0}'>{1}</option>".format(tab.id,tab.name);
        $('#add_page_item #tab_list').append(html);
    }
    
    showPopup('#add_page_item');
}

function onPageItemSubmit()
{
    var page = g_pageList[g_pageIndex];
    
    var playlist_id = $('#add_page_item #playlist_list').val();
    if( playlist_id > 0 )
    {
        var url = "/manage/data/page_playlists.php";
        var data = {
            page_id: page.page_id,
            playlist_id: playlist_id
        };
        
        jQuery.ajax(
        {
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) 
            {
                page.playlists.unshift(data.page_playlist);
                updatePages();
                showSuccess("Playlist added to page.");
            },
            error: function()
            {
                showFailure("Page playlist add failed.  You can only add a playlist once.");
            }
        });
    }
    
    var tab_id = $('#add_page_item #tab_list').val();
    if( tab_id > 0 )
    {
        var url = "/manage/data/page_tabs.php";
        var data = {
            page_id: page.page_id,
            tab_id: tab_id
        };
        
        jQuery.ajax(
        {
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) 
            {
                page.tabs.unshift(data.page_tab);
                updatePages();
                showSuccess("Tab added to page.");
            },
            error: function()
            {
                showFailure("Page tab add failed.  You can only add a tab once.");
            }
        });
    }
}

function deletePage(i)
{
    var page = g_pageList[i];
    
    var url = "/manage/data/pages.php";
    var data = {
        page_id: page.page_id
    };
    
    var r = window.confirm("Are you sure you want to delete this page?");
    if( r )
    {
        jQuery.ajax(
        {
            type: 'DELETE',
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) 
            {
                window.location.reload();
            },
            error: function()
            {
                window.alert("Delete failed.");
            }
        });
    }
}

function deletePagePlaylist(i,j)
{
    var page_playlist = g_pageList[i].playlists[j];
    
    var url = "/manage/data/page_playlists.php";
    var data = {
        page_playlist_id: page_playlist.page_playlist_id
    };
    
    jQuery.ajax(
    {
        type: 'DELETE',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            g_pageList[i].playlists.splice(j,1);
            updatePages();
        },
        error: function()
        {
            window.alert("Delete failed.");
        }
    });
}
function deletePageTab(i,j)
{
    var page_tab = g_pageList[i].tabs[j];
    
    var url = "/manage/data/page_tabs.php";
    var data = {
        page_tab_id: page_tab.page_tab_id
    };
    
    jQuery.ajax(
    {
        type: 'DELETE',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            g_pageList[i].tabs.splice(j,1);
            updatePages();
        },
        error: function()
        {
            window.alert("Delete failed.");
        }
    });
}

