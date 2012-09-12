
function signupCheckBox(signup_type)
{
    if( signup_type == 'fan' )
    {
        $('#signup .check_item.fan').attr('checked','checked');
        $('#signup .check_item.artist').attr('checked','');
        $('#signup #artist_items').hide();
    }
    else
    {
        $('#signup .check_item.fan').attr('checked','');
        $('#signup .check_item.artist').attr('checked','checked');
        $('#signup #artist_items').show();
    }
}


function signupClickInput(input,default_text)
{
    
}
function signupBlurInput(input,default_text)
{
    
}

