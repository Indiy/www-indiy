
function closeNewsletter()
{
    $('#newsletter_wrapper').hide();
    $('#newsletter_mask').hide();    
}

function showNewsletter()
{
    $('#newsletter_wrapper').show();
    $('#newsletter_mask').show();
}

function submitNewsletter()
{
    var email = $('#newsletter_email').val();
    
    $('#newsletter_form').hide();
    $('#newsletter_success').show();
    
    var url = "/data/add_mad_newsletter.php?add_email=";
    url += escape(email);

    jQuery.ajax(
        {
            type: "POST",
            url: url,
            dataType: "text",
            success: function(data) 
            {
                var foo = data;
            },
            error: function() { }
        });
}

