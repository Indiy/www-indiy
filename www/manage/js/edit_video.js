
var g_removeVideo = false;
var g_removeVideoImage = false;
var g_videoIndex = false;

function showVideoPopup(video_index)
{
    g_videoIndex = video_index;
    g_removeVideo = false;
    g_removeVideoImage = false;
    
    $('#edit_video #artist_id').val(g_artistId);
    
    if( video_index !== false )
    {
        var video = g_videoList[video_index];
        $('#edit_video #song_id').val(video.id);
        $('#edit_video #video_name').val(video.name);
        $('#edit_video #video_tags').val(video.tags);
        
        fillArtistFileSelect('#edit_video #image_drop','IMAGE',video.image);
        fillArtistFileSelect('#edit_video #video_drop','VIDEO',video.video);
    }
    else
    {
        if( g_artistData.account_type == 'REGULAR' 
           && g_videoList.length >= VIDEO_REGULAR_LIMIT )
        {
            showAccountLimitPopup();
            return;
        }

        fillArtistFileSelect('#edit_video #image_drop','IMAGE',false);
        fillArtistFileSelect('#edit_video #video_drop','VIDEO',false);
    
        $('#edit_video #song_id').val('');
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


