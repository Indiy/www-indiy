
function updateFileList()
{
    $('#file_list').empty();
    
    for( var i = 0 ; i < g_fileList.length ; ++i )
    {
        var file = g_fileList[i];
        
        var filename = file.upload_filename;
        if( !filename )
            filename = file.filename;
        
        if( file.is_uploading )
        {
            filename += file.upload_status + " " + file.upload_progress + "%";
        }
        
        var odd = "";
        if( i % 2 == 0 )
            odd = " odd";
        
        var html = "";
        html += "<div class='item{0}'>".format(odd);
        html += " <div class='filename'>{0}</div>".format(filename);
        html += " <div class='delete'>";
        html += "  <div class='button' onclick='deleteFile({0});'></div>".format(i);
        html += " </div>";
        html += "</div>";
        
        $('#file_list').append(html);
    }
}
function updateFileListItem(file)
{
    updateFileList();
}
function deleteFile(i)
{
    var file = g_fileList[i];
    
    var args = {
        artist_id: g_artistId,
        method: "DELETE",
        file_id: file.id
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url: '/manage/data/artist_file.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            if( data['success'] )
            {
                g_fileList.splice(i,1);
                updateFileList();
            }
        },
        error: function()
        {
        }
    });
}


function showAddArtistFilePopup()
{
    clearFileElement('#add_artist_file #file');
    $('#add_artist_file #artist_id').val(g_artistId);
    
    showPopup('#add_artist_file');
    return false;
}

function onAddArtistFile()
{
    var file_input = $('#add_artist_file #file')[0];
    if( !file_input || !file_input.value || file_input.value.length == 0 )
    {
        window.alert("Please select a file for upload.");
        return false;
    }
    
    return startFileUpload(file_input);
}

function startFileUpload(file_input)
{
    try
    {
        var xhr = new XMLHttpRequest();
        var file = {
            xhr: xhr,
            upload_filename: file_input.files[0].name,
            upload_progress: 0.0,
            upload_status: 'uploading',
            is_uploading: true,
            type: 'MISC'
        };
    
        function makeCallback(callback)
        {
            return function(evt) { callback(evt,file); }
        }
        xhr.successCallback = successCallback;
        xhr.onreadystatechange = makeCallback(onArtistFileReadyStateChange)
        var upload = xhr.upload;
        if( upload )
        {
            upload.addEventListener('progress',makeCallback(onArtistFileUploadProgress),false);
            upload.addEventListener('load',makeCallback(onArtistFileUploadDone),false);
            upload.addEventListener('error',makeCallback(onArtistFileUploadFailed),false);
        }
        
        var form_data = new FormData();
        form_data.append('artist_id',g_artistId);
        form_data.append('file',file_input.files[0]);
        form_data.append('ajax','1');
        
        var url = "/manage/data/artist_file.php";
        
        xhr.open("POST",url);
        xhr.send(form_data);

        g_backgroundCount++;
        g_uploadCount++;
        
        closePopup();
        g_fileList.unshift(file);
        updateFileList();
        return false;
    }
    catch(e)
    {
        showProgress();
        return true;
    }
}

function onArtistFileUploadProgress(evt,file)
{
    if( evt.lengthComputable )
    {
        var percentage = evt.loaded / evt.total * 100.0;
        file.upload_progress = percentage;
        updateFileListItem(file);
    }
    else
    {
        console.log("progress event but can't calculate percent");
    }
}

function onArtistFileUploadDone(evt,file)
{
    g_uploadCount--;
    file.upload_status = 'processing';
    updateFileList();
}

function onArtistFileUploadFailed(evt,file)
{
    g_uploadCount--;
    file.upload_status = 'failed';
    updateFileList();
}

function onArtistFileReadyStateChange(evt,file)
{
    var xhr = file.xhr;
    if( xhr.readyState == 4 )
    {
        g_backgroundCount--;
        var status_code = xhr.status;
        var text = xhr.responseText;
        try
        {
            if( status_code == 200 && text.length > 0 )
            {
                var data = JSON.parse(text);
                if( 'upload_error' in data )
                {
                    file.upload_status = 'failed';
                    updateFileList();
                }
                else
                {
                    var new_file = data.file;
                    
                    file.filename = new_file.filename;
                    file.is_uploading = false;
                    file.upload_filename = new_file.filename;
                    file.id = new_file.id;
                    file.type = new_file.type;
                    updateFileList();
                }
            }
            else
            {
                file.upload_status = 'failed';
                updateFileList();
            }
        }
        catch(e)
        {
            file.upload_status = 'failed';
            updateFileList();
        }
    }
}
