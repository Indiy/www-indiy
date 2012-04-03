
var g_removeSong = false;
var g_removeImage = false;

var g_songId = '';
var g_pageIndex = false;

$(document).ready(function() { jscolor.init(); });

function showPagePopup(page_index)
{
    g_pageIndex = page_index;
    g_removeSong = false;
    g_removeImage = false;

    $('#edit_page #artist_id').val(g_artistId);
    
    
    clearFileElement('#edit_page #song_audio');
    clearFileElement('#edit_page #song_image');
    
    if( page_index !== false )
    {
        var song = g_pageList[page_index];
        
        g_songId = song.id;
        $('#edit_page #song_id').val(song.id);
        $('#edit_page #song_name').val(song.name);
        if( song.audio )
        {
            var html = "";
            if( song.upload_audio_filename )
                html += "<div>{0}</div>".format(song.upload_audio_filename);
            else
                html += "<div>{0}</div>".format(song.audio);
            html += "<button onclick='return onPageSongRemove();'></button>";
            $('#edit_page #song_filename_container').html(html);
        }
        if( song.image )
        {
            var html = "<img src='{0}' />".format(song.image);
            html += "<button onclick='return onPageImageRemove();'></button>";
            $('#edit_page #image_filename_container').html(html);
        }
        else
        {
            $('#edit_page #image_filename_container').empty();
        }
        
        $('#edit_page #bg_style').val(song.bg_style);
        $('#edit_page #song_bgcolor').val(song.bgcolor);
        if( song.download )
        {
            $('#edit_page input[name=download]:eq(0)').attr('checked','checked');
            clickFree(1);
        }
        else
        {
            $('#edit_page input[name=download]:eq(1)').attr('checked','checked');
            clickFree(0);
        }
        $('#edit_page #amazon_url').val(song.amazon);
        $('#edit_page #itunes_url').val(song.itunes);
        $('#edit_page #mad_store').prop('checked',song.product_id);
        $('#edit_page #audio_tags').val(song.tags);
    }
    else
    {
        g_songId = '';
        $('#edit_page #song_id').val('');
        $('#edit_page #song_name').val('');
        $('#edit_page #song_filename_container').empty();
        $('#edit_page #image_filename_container').empty();
        $('#edit_page #bg_style').val('STRETCH');
        $('#edit_page input[name=download]:eq(1)').attr('checked','checked');
        clickFree(0);
        $('#edit_page #amazon_url').val('');
        $('#edit_page #itunes_url').val('');
        $('#edit_page #mad_store').prop('checked',false);
        $('#edit_page #audio_tags').val('');
    }
    showPopup('#edit_page');
}

function onPageSongRemove()
{
    var result = window.confirm("Remove song from page?");
    if( result )
    {
        g_removeSong = true;
        $('#edit_page #song_filename_container').empty();
    }
    return false;
}
function onPageImageRemove()
{
    var result = window.confirm("Remove image from page?");
    if( result )
    {
        g_removeImage = true;
        $('#edit_page #image_filename_container').hide();
    }
    return false;
}
function clickFree(yes)
{
    if( yes )
    {
        $('#edit_page #amazon_url').attr('disabled',true);
        $('#edit_page #itunes_url').attr('disabled',true);
        $('#edit_page #mad_store').attr('disabled',true);
    }
    else
    {
        $('#edit_page #amazon_url').removeAttr('disabled');
        $('#edit_page #itunes_url').removeAttr('disabled');
        $('#edit_page #mad_store').removeAttr('disabled');
    }
}
function clickMadStore()
{
    if( g_paypalEmail.length == 0 )
    {
        window.alert("You will need to add a Paypal Email address in Monetize settings to sell music in the MyArtistDNA Store.");
    }
}


function onAddMusicSubmit()
{
    var needs_image = false;
    if( g_pageIndex === false )
    {
        needs_image = true;
    }
    else
    {
        var song  = g_pageList[g_pageIndex];
        if( !song.image )
            needs_image = true;
    }   
    
    var song_image = $('#edit_page #song_image')[0];
    if( needs_image && ( !song_image || !song_image.value || song_image.value.length == 0 ) )
    {
        window.alert("Please upload an image for the page.");
        return false;
    }
    var song_name = $('#edit_page #song_name').val();
    if( song_name.length == 0 )
    {
        window.alert("Please enter a name for your page.");
        return false;
    }
    
    function fillMusicForm(form_data)
    {
        var song_id = g_songId;
        var song_name = $('#edit_page #song_name').val();
        var song_bgcolor = $('#edit_page #song_bgcolor').val();
        var bg_style = $('#edit_page #bg_style option:selected').val();
        var free_download = $('#edit_page input[@name=download]:checked').val();
        var amazon_url = $('#edit_page #amazon_url').val();
        var itunes_url = $('#edit_page #itunes_url').val();
        var mad_store = $('#edit_page #mad_store').is(':checked');
        var tags = $('#edit_page #audio_tags').val();
        
        form_data.append('artistid',g_artistId);
        form_data.append('id',song_id);
        form_data.append('name',song_name);
        form_data.append('bgcolor',song_bgcolor);
        form_data.append('bg_style',bg_style);
        form_data.append('download',free_download);
        form_data.append('amazon',amazon_url);
        form_data.append('itunes',itunes_url);
        form_data.append('mad_store',mad_store);
        form_data.append('remove_image',g_removeImage);
        form_data.append('remove_song',g_removeSong);
        form_data.append('tags',tags);
        form_data.append('ajax',true);
        
        var song_image = $('#edit_page #song_image')[0];
        if( song_image.files && song_image.files.length > 0 )
        {
            form_data.append('logo',song_image.files[0]);
        }
        var song_audio = $('#edit_page #song_audio')[0];
        if( song_audio.files && song_audio.files.length > 0 )
        {
            form_data.append('audio',song_audio.files[0]);
        }
        form_data.append('WriteTags','submit');
    }
    
    var url = '/manage/data/page.php';
    return startAjaxUpload(url,fillMusicForm,onPageSuccess);
}

function onPageSuccess(data)
{
    if( g_pageIndex !== false )
    {
        g_pageList[g_pageIndex] = data.page_data;
    }
    else
    {
        g_pageList.unshift(data.page_data);
    }
    updatePageList();
}

function onSongChange()
{
    checkFileExtensions('#edit_page #song_audio',['mp3'],"Please upload songs in MP3 format.");
    var fn_div = $('#edit_page #song_audio').parent().parent().children('.filename');
    if( fn_div.html().indexOf('<button>') == -1 )
        fn_div.append("<button onclick='return clearSongElement();'></button>");
    else
        fn_div.children("button").show();
}
function clearSongElement()
{
    var html = $('#edit_page #song_audio').parent().html();
    $('#edit_page #song_audio').parent().html(html);
    var fn_div = $('#edit_page #song_audio').parent().parent().children('.filename');
    fn_div.children("button").hide();
    return false;
}



