
var g_removeSong = false;
var g_removeImage = false;

var g_songId = '';
var g_pageIndex = false;

function showPagePopup(page_index)
{
    $('.status_container').hide();
    $('#ajax_form').show();

    g_pageIndex = page_index;
    g_removeSong = false;
    g_removeImage = false;

    $('#artist_id').val(g_artistId);
    
    
    clearFileElement('#song_audio');
    clearFileElement('#song_image');
    
    if( page_index !== false )
    {
        var song = g_pageList[page_index];
        
        g_songId = song.id;
        $('#song_id').val(song.id);
        $('#song_name').val(song.name);
        if( song.audio )
        {
            var html = "";
            if( song.upload_audio_filename )
                html += "<div>{0}</div>".format(song.upload_audio_filename);
            else
                html += "<div>{0}</div>".format(song.audio);
            html += "<button onclick='return onSongRemove();'></button>";
            $('#song_filename_container').html(html);
        }
        if( song.image )
        {
            var html = "<img src='../artists/images/{0}' />".format(song.image);
            html += "<button onclick='return onImageRemove();'></button>";
            $('#image_filename_container').html(html);
        }
        
        $('#bg_style').val(song.bg_style);
        $('#song_bgcolor').val(song.bgcolor);
        if( song.download )
        {
            $('input[name=download]:eq(0)').attr('checked','checked');
            clickFree(1);
        }
        else
        {
            $('input[name=download]:eq(1)').attr('checked','checked');
            clickFree(0);
        }
        $('#amazon_url').val(song.amazon);
        $('#itunes_url').val(song.itunes);
        $('#mad_store').prop('checked',song.product_id);
        $('#audio_tags').val(song.tags);
    }
    else
    {
        g_songId = '';
        $('#song_id').val('');
        $('#song_name').val('');
        $('#song_filename_container').empty();
        $('#image_filename_container').empty();
        $('#bg_style').val('STRETCH');
        $('input[name=download]:eq(1)').attr('checked','checked');
        clickFree(0);
        $('#amazon_url').val('');
        $('#itunes_url').val('');
        $('#mad_store').prop('checked',false);
        $('#audio_tags').val('');
    }
    showPopup('edit_page_wrapper');
}

function onSongRemove()
{
    var result = window.confirm("Remove song from page?");
    if( result )
    {
        g_removeSong = true;
        $('#song_filename_container').empty();
    }
    return false;
}
function onImageRemove()
{
    var result = window.confirm("Remove image from page?");
    if( result )
    {
        g_removeImage = true;
        $('#image_filename_container').hide();
    }
    return false;
}
function clickFree(yes)
{
    if( yes )
    {
        $('#amazon_url').attr('disabled',true);
        $('#itunes_url').attr('disabled',true);
        $('#mad_store').attr('disabled',true);
    }
    else
    {
        $('#amazon_url').removeAttr('disabled');
        $('#itunes_url').removeAttr('disabled');
        $('#mad_store').removeAttr('disabled');
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
    
    var song_image = document.getElementById('song_image');
    if( needs_image && ( !song_image || !song_image.value || song_image.value.length == 0 ) )
    {
        window.alert("Please upload an image for the page.");
        return false;
    }
    var song_name = $('#song_name').val();
    if( song_name.length == 0 )
    {
        window.alert("Please enter a name for your page.");
        return false;
    }
    
    function fillMusicForm(form_data)
    {
        var song_id = g_songId;
        var song_name = $('#song_name').val();
        var song_bgcolor = $('#song_bgcolor').val();
        var bg_style = $('#bg_style option:selected').val();
        var free_download = $('input[@name=download]:checked').val();
        var amazon_url = $('#amazon_url').val();
        var itunes_url = $('#itunes_url').val();
        var mad_store = $('#mad_store').is(':checked');
        var tags = $('#audio_tags').val();
        
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
        
        var song_image = document.getElementById('song_image');
        if( song_image.files && song_image.files.length > 0 )
        {
            form_data.append('logo',song_image.files[0]);
        }
        var song_audio = document.getElementById('song_audio');
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
        g_pageList.append(data.page_data);
    }
    updatePageList();
}

function onSongChange()
{
    checkFileExtensions('song_audio',['mp3'],"Please upload songs in MP3 format.");
    var fn_div = $('#song_audio').parent().parent().children('.filename');
    if( fn_div.html().indexOf('<button>') == -1 )
        fn_div.append("<button onclick='return clearSongElement();'></button>");
    else
        fn_div.children("button").show();
}
function clearSongElement()
{
    var html = $('#song_audio').parent().html();
    $('#song_audio').parent().html(html);
    var fn_div = $('#song_audio').parent().parent().children('.filename');
    fn_div.children("button").hide();
    return false;
}



