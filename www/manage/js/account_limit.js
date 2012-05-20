
function showAccountLimitPopup()
{
    $('#basic_limit_popup #email').val(g_artistData.email);

    showPopup('#basic_limit_popup');
}

function onAccountLimitSubmit()
{
    var email = $('#basic_limit_popup #email').val();
    
    if( !email.match(EMAIL_REGEX) )
    {
        window.alert("Please enter a valid email address.");
        return;
    }
    showSuccess("Someone will contact you shortly reguarding your account.");
    
    var args = { 
        "email": email,
        "artist_id": g_artistId
        };
    jQuery.ajax(
    {
        type: 'POST',
        url: '/manage/data/account_upgrade.php',
        data: args,
        success: function(data) 
        {
        },
        error: function()
        {
        }
    });
}


