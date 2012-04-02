
var g_removeVideo = false;
var g_removeVideoImage = false;
var g_videoIndex = false;

function showVideoPopup(video_index)
{
    g_videoIndex = video_index;
    g_removeVideo = false;
    g_removeVideoImage = false;
    
    $('#edit_video #artist_id').val(g_artistId);
    clearFileElement('#edit_video #video_image_file');
    clearFileElement('#edit_video #video_file');
    
    $('#edit_video #artist_id').val(g_artistId);
    if( video_index !== false )
    {
        var video = g_videoList[video_index];
        $('#edit_video #song_id').val(video.id);
        $('#edit_video #video_name').val(video.name);
        $('#edit_video #video_tags').val(video.tags);
        
        if( video.image )
        {
            var html = "<img src='{0}' />".format(video.image);
            html += "<button onclick='return onVideoImageRemove();'></button>";
            $('#edit_video .image_image').html(html);
        }
        else
        {
            $('#edit_video .image_image').empty();
        }
        var html = "<div>(We only accept .mov and .mp4 files)</div>";
        if( video.video != '' )
        {
            if( video.upload_video_filename )
                html = "<div>{0}</div>".format(video.upload_video_filename);
            else
                html = "<div>{0}</div>".format(video.video);
            html += "<button onclick='return onVideoRemove();'></button>";
        }
        $('#edit_video .filename').html(html);
    }
    else
    {
        $('#edit_video #song_id').val(''.id);
        $('#edit_video #video_name').val('');
        $('#edit_video #video_tags').val('');
    }
    showPopup('#edit_video');
    return false;
}

function onVideoRemove()
{
    var result = window.confirm("Remove video?");
    if( result )
    {
        g_removeVideo = true;
        $('#edit_video .filename').hide();
    }
    return false;
}

function onVideoImageRemove()
{
    var result = window.confirm("Remove image?");
    if( result )
    {
        g_removeVideoImage = true;
        $('#edit_video .image_image').hide();
    }
    return false;
}


function onAddVideoSubmit()
{
    var needs_image = false;

    var video_image_file = $('#edit_video #video_image_file')[0];
    if( needs_image && ( !video_image_file || !video_image_file.value || video_image_file.value.length == 0 ) )
    {
        window.alert("Please upload a poster image for the video.");
        return false;
    }
    
    var needs_video = false;
    
    var video_file = $('#edit_video #video_file')[0];
    if( needs_video && ( !video_file || !video_file.value || video_file.value.length == 0 ) )
    {
        window.alert("Please upload a video.");
        return false;
    }
    var video_name = $('#edit_video #video_name').val();
    if( video_name.length == 0 )
    {
        window.alert("Please enter a name for your video.");
        return false;
    }
    
    function fillVideoForm(form_data)
    {
        var artist_id = $('#edit_video #artist_id').val();
        var song_id = $('#edit_video #song_id').val();
        var video_name = $('#edit_video #video_name').val();
        var video_image_file = $('#edit_video #video_image_file')[0].files[0];
        var video_file = $('#edit_video #video_file')[0].files[0];
        var tags = $('#edit_video #video_tags').val();
        
        form_data.append('artistid',artist_id);
        form_data.append('id',song_id);
        form_data.append('name',video_name);
        form_data.append('logo',video_image_file);
        form_data.append('video',video_file);
        form_data.append('remove_video',g_removeVideo);
        form_data.append('remove_video_image',g_removeVideoImage);
        form_data.append('tags',tags);
        form_data.append('ajax',true);
    }
    var url = '/manage/data/video.php';
    return startAjaxUpload(url,fillVideoForm,onVideoSuccess);
}

function onVideoSuccess(data)
{
    if( g_videoIndex !== false )
    {
        g_videoList[g_videoIndex] = data.video_data;
    }
    else
    {
        g_videoList.unshift(data.video_data);
    }
    updateVideoList();
    
}


