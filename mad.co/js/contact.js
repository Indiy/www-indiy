
function submitForm()
{
    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var subject = $('#contact_subject').val();
    
    var d = {
        name: name,
        email: email,
        subject: subject
    };
    var postData = JSON.stringify(d);
    jQuery.ajax(
    {
        type: 'POST',
        url: '/data/contact.php',
        contentType: 'application/json',
        data: postData,
        processData: false,
        success: function(data) 
        {
        },
        error: function()
        {
        }
    });
    
    window.alert("Thank you for your submission.");
    $('#contact_name').val('');
    $('#contact_email').val('');
    $('#contact_subject').val('');
}

