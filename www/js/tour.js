

// Helper functions for tour page

function sendContactForm(image)
{
    var url = "/data/submit_contact.php";

    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var subject = $('#contact_subject').val();
    var body = $('#contact_body').val();
    
    var postData = {
        "name": name,
        "email": email,
        "subject": subject,
        "body": body
    };

    jQuery.ajax(
    {
        type: 'POST',
        url: '/data/submit_contact.php',
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
    $('#contact_form').hide();
    $('#contact_success').show();
}


