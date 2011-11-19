

// Helper functions for artist home page

function artistHomeReady()
{
    //$('#login_dialog_close').click(closeLogin);
}

function updateListens(image)
{
    var url = "/data/listens.php?image=" + image;

    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            g_totalListens = data['total_listens'];
            var track_listens = data['track_listens'];
            //$('#total_listens').text(g_totalListens);
            $('#current_track_listens').text(track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
}


function showCart(fade)
{
    $("ul.products").hide();
    if( fade )
        $(".cart").fadeIn();
    else
        $(".cart").show();
}

function showProducts(fade)
{
    $(".cart").hide();
    if( fade )
        $("ul.products").fadeIn();
    else
        $("ul.products").show();
}


$(document).ready(artistHomeReady);


