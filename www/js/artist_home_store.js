
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

function showStore()
{
    var w = $('#store').width();
    //$('#store_wrapper').width(w);

    fadeAllPageElements();
    $('#store_wrapper').fadeIn();
}
function closeStore()
{
    $('#store_wrapper').fadeOut();
}
var g_currentStoreScroll = 0;
function scrollStoreRight()
{
    g_currentStoreScroll += 3;
    g_currentStoreScroll = Math.min(g_currentStoreScroll,g_productList.length-3);
    $('#store .product_list').animate({ scrollLeft: 298*g_currentStoreScroll });
}
function scrollStoreLeft()
{
    g_currentStoreScroll -= 3;
    g_currentStoreScroll = Math.max(0,g_currentStoreScroll);
    $('#store .product_list').animate({ scrollLeft: 298*g_currentStoreScroll });
}

