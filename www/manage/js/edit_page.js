

function updatePages()
{
}

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
    $('#edit_page_item #playlist_list').html(html);
    for( var i = 0 ; i < g_playlistList.length ; ++i )
    {
        var playlist = g_playlistList[i];
        var html = "<option value='{0}'>{2}</option>".format(playlist.playlist_id,playlist.name);
        $('#edit_page_item #playlist_list').append(html);
    }
    showPopup('#edit_page_item');
    
    var html = "<option value='0'>None</option>";
    $('#edit_page_item #tab_list').html(html);
    for( var i = 0 ; i < g_tabList.length ; ++i )
    {
        var tab = g_tabList[i];
        var html = "<option value='{0}'>{2}</option>".format(tab.id,tab.name);
        $('#edit_page_item #tab_list').append(html);
    }
    showPopup('#edit_page_item');
}

function onPageItemSubmit()
{
    var page = g_pageList[g_pageIndex];
    
    var playlist_id = $('#edit_page_item #playlist_list').val();
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
                updatePages();
            },
            error: function()
            {
                showFailure("Page playlist add failed.  You can only add a playlist once.");
            }
        });
    }
    
    var tab_id = $('#edit_page_item #tab_list').val();
    if( tab_id > 0 )
    {
        
    }
}


