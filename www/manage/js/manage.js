


var HOSTNAME_REGEX = new RegExp('^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?(?:\\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\\b-){0,61}[0-9A-Za-z])?)*\\.?$');

var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

function checkElementFileExtensions(file,extensions,error_string)
{
    if( file.files && file.files.length > 0 )
    {
        var file_name = file.files[0].name;
        if( file_name && file_name.length > 0 )
        {
            file_name = file_name.toLowerCase();
            var file_name_parts = file_name.split('.');
            var ext = file_name_parts[file_name_parts.length - 1];
            for( var k in extensions )
            {
                var valid_ext = extensions[k];
                if( valid_ext == ext )
                    return false;
            }
            window.alert(error_string);
            return true;
        }
    }
    return false;
}
function checkElementSize(file,size_limit,error_string)
{
    if( file.files && file.files.length > 0 )
    {
        var size = file.files[0].size;
        if( size && size > size_limit )
        {
            window.alert(error_string);
        }
    }
}
function checkFileExtensions(element_id,extensions,error_string)
{
    var file = $(element_id)[0];
    checkElementFileExtensions(file,extensions,error_string);
}
function onVideoChange()
{
    checkFileExtensions('#video_file',['mov','mp4'],"Please upload video in MP4 or MOV format.");
}

function onImageChange(file)
{
    if( checkElementFileExtensions(file,['png','jpg','gif','jpeg'],"Please upload images in PNG, JPG, or GIF format.") )
        return;
    checkElementSize(file,2*1024*1024,"Please upload images 1280x800 and less than 2MB in size.");
}

function deleteAccount(id)
{
    var ret = window.confirm("Are you sure you want to delete your account?");
    if( ret )
        ret = window.confirm("This action can not be undone, are you sure?");
    if( ret )
    {
        var url = "/manage/delete_account.php?artist_id=" + id;
        url += "&confirm=true";
        window.location.href = url;
    }
}

function showFanConnections()
{
    showPopup('#fan_connections');
}
function showFirstInstructions()
{
    showPopup('#first_instructions');
}

function closeFirstInstructions()
{
    if( $('#first_instructions #dont_show_again').is(':checked') )
    {
        var args = {
            method: "clear_first_instructions",
            artist_id: g_artistId
        };
    
        jQuery.ajax(
        {
            type: 'POST',
            url: "/manage/data/profile.php",
            data: args,
            dataType: 'json',
            success: function(data) 
            {
            },
            error: function()
            {
            }
        });
    }

    closePopup();
}
