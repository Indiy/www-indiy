
function clickJoinNewsletter()
{    
    $('#social_email .input_button').hide();
    $('#social_email .success').show();
    
    var email = $('#social_email input').val();
    
    var submited = "";
    submited += "&newsletter=true"
    submited += "&artist=" + g_artistId;
    submited += "&email=" + escape(email);
    
    $.post("/jplayer/ajax.php", submited, function(repo) {});
}

