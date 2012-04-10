
var g_backgroundCount = 0;
var g_uploadCount = 0;

function showDismissableProcessing()
{
    showMessagePopup('#dismissable_processing');
}
function showProgress(text)
{
    showMessagePopup('#progress',text);
}
function showSuccess(text)
{
    $('#message_popup #success_msg .social_success').hide();
    showMessagePopup('#success',text);
    return true;
}
function showFailure(text)
{
    showMessagePopup('#failure',text);
}
function showProcessing()
{
    showMessagePopup('#processing');
}
function showUploading()
{
    $('#upload_progress_bar').css("width","0%");
    $('#upload_percent').text("0");
    showMessagePopup('#uploading');
}

function showMessagePopup(selector,text)
{
    showPopup('#message_popup',true);
    $('#message_popup .status_container').hide();
    if( text )
        $('#message_popup ' + selector + ' .status').text(text);
    $('#message_popup ' + selector).show();
}

function checkPopupNumber(popupNumber)
{
    if( popupNumber == g_popupNumber )
    {
        return true;
    }
    else
    {
        console.log("popupNumber({0}) != g_popupNumber{{1})".format(popupNumber,g_popupNumber));
        return false;
    }
}

function onUploadProgress(evt,xhr)
{
    if( evt.lengthComputable )
    {
        if( checkPopupNumber(xhr.popupNumber) )
        {
            var percentage = evt.loaded / evt.total * 100.0;
            $('#upload_progress_bar').css("width","{0}%".format(percentage));
            $('#upload_percent').text(percentage.toFixed(0));
        }
    }
    else
    {
        console.log("progress event but can't calculate percent");
    }
}
function onUploadDone(evt,xhr)
{
    g_uploadCount--;
    if( checkPopupNumber(xhr.popupNumber) )
        showDismissableProcessing();
}
function onUploadFailed(evt,xhr)
{
    g_uploadCount--;
    if( checkPopupNumber(xhr.popupNumber) )
        showFailure("Update Failed");
}
function uploadReadyStateChange(xhr)
{
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
                    if( checkPopupNumber(xhr.popupNumber) )
                        showSuccess(data['upload_error']);
                }
                else
                {
                    if( checkPopupNumber(xhr.popupNumber) ) 
                    {
                        showSuccess("Update Success");
                        if( 'fb_update' in data )
                            $('#success_msg .social_success.facebook').show();
                        if( 'tw_update' in data )
                            $('#success_msg .social_success.twitter').show();
                    }
                    
                    if( xhr.successCallback )
                        xhr.successCallback(data);
                }
            }
            else
            {
                if( checkPopupNumber(xhr.popupNumber) )
                    showFailure("Update Failed");
            }
        }
        catch(e)
        {
            if( checkPopupNumber(xhr.popupNumber) )
                showFailure("Update Failed");
        }
    }
}

function startAjaxUpload(url,fillForm,successCallback)
{
    g_popupNumber++;
    showProgress();
    try
    {
        var xhr = new XMLHttpRequest();
        function makeCallback(callback)
        {
            return function(evt) { callback(evt,xhr); }
        }
        xhr.popupNumber = g_popupNumber;
        xhr.successCallback = successCallback;
        xhr.onreadystatechange = function() { uploadReadyStateChange(this); };
        var upload = xhr.upload;
        if( upload )
        {
            upload.addEventListener('progress',makeCallback(onUploadProgress),false);
            upload.addEventListener('load',makeCallback(onUploadDone),false);
            upload.addEventListener('error',makeCallback(onUploadFailed),false);
        }
        
        var form_data = new FormData();
        fillForm(form_data);
        
        xhr.open("POST",url);
        xhr.send(form_data);
        showUploading();
        g_backgroundCount++;
        g_uploadCount++;
        return false;
    }
    catch(e)
    {
        showProgress();
        return true;
    }
    
}

window.onbeforeunload = function() 
{
    if( g_uploadCount > 0 )
        return 'You have files uploading in the background, are you sure you want to cancel them?';
    if( g_backgroundCount > 0 )
        return 'You have uploads processing in the background.';
};

