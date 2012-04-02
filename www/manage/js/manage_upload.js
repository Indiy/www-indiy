
function uploadProgress(percentage)
{
    var text = "" + percentage.toFixed(0);
    $('#upload_percent').text(text);
}

function onUploadProgress(evt)
{
    if( evt.lengthComputable )
    {
        var percentage = evt.loaded / evt.total * 100.0;
        //console.log("progress: " + percentage);
        uploadProgress(percentage);
    }
    else
    {
        console.log("progress event but can't calculate percent");
    }
}
function onUploadDone(evt)
{
    showProcessing();
}
function onUploadFailed(evt)
{
    showFailure("Update Failed");
}
function uploadReadyStateChange(xhr)
{
    if( xhr.readyState == 4 )
    {
        var status_code = xhr.status;
        var text = xhr.responseText;
        try
        {
            if( status_code == 200 && text.length > 0 )
            {
                var data = JSON.parse(text);
                if( 'upload_error' in data )
                {
                    showSuccess(data['upload_error']);
                }
                else
                {
                    showSuccess("Update Success");
                    if( 'fb_update' in data )
                        $('#success_msg .social_success.facebook').show();
                    if( 'tw_update' in data )
                        $('#success_msg .social_success.twitter').show();
                    
                    if( xhr.successCallback )
                        xhr.successCallback(data);
                }
            }
            else
            {
                showFailure("Update Failed");
            }
        }
        catch(e)
        {
            showFailure("Update Failed");
        }
    }
}

function startAjaxUpload(url,fillForm,successCallback)
{
    showProgress();
    try
    {
        var xhr = new XMLHttpRequest();
        xhr.successCallback = successCallback;
        xhr.onreadystatechange = function() { uploadReadyStateChange(this); };
        var upload = xhr.upload;
        if( upload )
        {
            upload.addEventListener('progress',onUploadProgress,false);
            upload.addEventListener('load',onUploadDone,false);
            upload.addEventListener('error',onUploadFailed,false);
        }
        
        var form_data = new FormData();
        fillForm(form_data);
        
        xhr.open("POST",url);
        xhr.send(form_data);
        showUploading();
        return false;
    }
    catch(e)
    {
        showProgress();
        return true;
    }
    
}


