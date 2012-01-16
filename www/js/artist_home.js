

// Helper functions for artist home page

function artistHomeReady()
{
    //$('#login_dialog_close').click(closeLogin);
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

function buySong(product_id)
{
    var cart = "&cart=true";
    cart += "&paypal=" + g_paypalEmail;
    cart += "&artist=" + g_artistId;
    cart += "&product=" + product_id;
    
    $.post("jplayer/ajax.php", cart, function(items) 
    {
        $('.cart').html(items);
        showCart(false);
    });

    fadeAllPageElements();
    $('.store').fadeIn();
    $('.store_Close').fadeIn();
}

$(document).ready(artistHomeReady);


