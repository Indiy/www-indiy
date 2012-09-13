
function signupCheckBox(signup_type)
{
    if( signup_type == 'fan' )
    {
        $('#signup .check_item.fan input').attr('checked','checked');
        $('#signup .check_item.artist input').removeAttr('checked');
        $('#signup #artist_items').hide();
    }
    else
    {
        $('#signup .check_item.fan input').removeAttr('checked');
        $('#signup .check_item.artist input').attr('checked','checked');
        $('#signup #artist_items').show();
    }
}


function signupClickInput(input,default_text)
{
    
}
function signupBlurInput(input,default_text)
{
    
}

