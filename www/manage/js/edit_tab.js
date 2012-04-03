
var g_removeTabImage = false;
var g_tabIndex = false;

$(document).ready(setupRichTextEditor);

function onTabImageRemove()
{
    var result = window.confirm("Remove image from page?");
    if( result )
    {
        g_removeTabImage = true;
        $('#edit_tab .image_image').hide();
    }
    return false;
}

function showTabPopup(tab_index)
{
    g_tabIndex = tab_index;
    g_removeTabImage = false;
    
    $('#edit_tab #artist_id').val(g_artistId);
    clearFileElement('#edit_tab #content_image');
    
    if( tab_index !== false )
    {
        var tab = g_tabList[tab_index];
        
        $('#edit_tab #content_id').val(tab.id);
        $('#edit_tab #name').val(tab.name);
        var body = getEncodedBody(tab.body)
        g_editor.setEditorHTML(body);
    }
    else
    {
        $('#edit_tab #content_id').val("");
        $('#edit_tab #name').val("");
        g_editor.setEditorHTML("");
    }
    showPopup('#edit_tab');
    return false;
}

function onAddContentSubmit()
{
    var name = $('#edit_tab #name').val();
    if( name.length == 0 )
    {
        window.alert("Please enter a name for your tab.");
        return false;
    }
    function fillContentForm(form_data)
    {
        if( g_rawEditorState == "off" )
        {
            g_editor.saveHTML();
            var body = $('#edit_tab #body').val();
            body = getRawBody(body);
        }
        else
        {
            var body = $('#edit_tab #body').val();
        }
        var artist_id = $('#edit_tab #artist_id').val();
        var content_id = $('#edit_tab #content_id').val();
        var name = $('#edit_tab #name').val();
        
        form_data.append('artistid',artist_id);
        form_data.append('id',content_id);
        form_data.append('name',name);
        form_data.append('body',body);
        form_data.append('remove_image',g_removeImage);
        
        var content_image = $('#edit_tab #content_image')[0];
        if( content_image.files && content_image.files.length > 0 )
        {
            form_data.append('logo',content_image.files[0]);
        }
        form_data.append('submit','submit');
    }
    
    var url = '/manage/data/tab.php';
    return startAjaxUpload(url,fillContentForm,onTabSuccess);
}
function onTabSuccess(data)
{
    if( g_tabIndex !== false )
    {
        g_tabList[g_tabIndex] = data.tab_data;
    }
    else
    {
        g_tabList.unshift(data.tab_data);
    }
    updateTabList();
}

