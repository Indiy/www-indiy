
var g_photoRemove = false;

var g_photoId = '';
var g_photoIndex = false;

function showPhotoPopup(photo_index)
{
    g_photoIndex = photo_index;
    g_photoRemove = false;
    
    $('#edit_photo #artist_id').val(g_artistId);
    
    clearFileElement('#edit_photo #photo_image');
    
    if( photo_index !== false )
    {
        var photo = g_photoList[photo_index];
        
        g_photoId = photo.id;
        $('#edit_photo #song_id').val(photo.id);
        $('#edit_photo #photo_name').val(photo.name);
        $('#edit_photo #location').val(photo.location);
        if( photo.image_url )
        {
            var html = "<img src='{0}' />".format(photo.image_url);
            html += "<button onclick='return onPhotoImageRemove();'></button>";
            $('#edit_photo #image_filename_container').html(html);
        }
        else
        {
            $('#edit_photo #image_filename_container').empty();
        }
        
        $('#edit_photo #photo_bg_style').val(photo.bg_style);
        $('#edit_photo #photo_bg_color').val(photo.bg_color);
        $('#edit_photo #photo_tags').val(photo.tags);
    }
    else
    {
        if( g_artistData.account_type == 'REGULAR' 
           && g_photoList.length >= PHOTO_REGULAR_LIMIT )
        {
            showAccountLimitPopup();
            return;
        }
        
        g_photoId = '';
        $('#edit_photo #photo_id').val('');
        $('#edit_photo #photo_name').val('');
        $('#edit_photo #image_filename_container').empty();
        $('#edit_photo #photo_bg_style').val('LETTERBOX');
        $('#edit_photo #photo_bg_color').val('000000');
        $('#edit_photo #photo_tags').val('');
    }
    showPopup('#edit_photo');
}

function onPhotoImageRemove()
{
    var result = window.confirm("Remove image?");
    if( result )
    {
        $('#edit_photo #image_filename_container').hide();
        g_photoRemove = true;
    }
    return false;
}

function onAddPhotoSubmit()
{
    var needs_image = false;
    if( g_photoIndex === false || g_photoRemove )
    {
        needs_image = true;
    }
    else
    {
        var photo = g_photoList[g_photoIndex];
        if( !photo.image_url )
            needs_image = true;
    }   
    
    var photo_image = $('#edit_photo #photo_image')[0];
    if( needs_image && ( !photo_image || !photo_image.value || photo_image.value.length == 0 ) )
    {
        window.alert("Please upload an image.");
        return false;
    }
    var photo_name = $('#edit_photo #photo_name').val();
    if( photo_name.length == 0 )
    {
        window.alert("Please enter a name for your photo.");
        return false;
    }
    
    function fillPhotoForm(form_data)
    {
        var photo_id = g_photoId;
        var name = $('#edit_photo #photo_name').val();
        var location = $('#edit_photo #location').val();
        var bg_color = $('#edit_photo #photo_bg_color').val();
        var bg_style = $('#edit_photo #photo_bg_style option:selected').val();
        var tags = $('#edit_photo #photo_tags').val();
        
        form_data.append('artist_id',g_artistId);
        form_data.append('id',photo_id);
        form_data.append('name',name);
        form_data.append('location',location);
        form_data.append('bg_color',bg_color);
        form_data.append('bg_style',bg_style);
        form_data.append('tags',tags);
        form_data.append('ajax',true);
        
        var image = $('#edit_photo #photo_image')[0];
        if( image.files && image.files.length > 0 )
        {
            form_data.append('image',image.files[0]);
        }
        form_data.append('WriteTags','submit');
    }
    
    var url = '/manage/data/photo.php';
    return startAjaxUpload(url,fillPhotoForm,onPhotoSuccess);
}

function onPhotoSuccess(data)
{
    if( g_photoIndex !== false )
    {
        g_photoList[g_photoIndex] = data.photo_data;
    }
    else
    {
        g_photoList.unshift(data.photo_data);
    }
    updatePhotoList();
}

