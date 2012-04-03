
var g_removeImage = false;

function onReady()
{
    setupRichTextEditor();
    setupQuestionTolltips();
}

$(document).ready(onReady);

function onImageRemove()
{
    var result = window.confirm("Remove image from page?");
    if( result )
    {
        g_removeImage = true;
        $('.image_image').hide();
    }
    return false;
}


