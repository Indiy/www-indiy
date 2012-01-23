
function showCart(fade)
{
    $("#store_products").hide();
    if( fade )
        $("#store_cart").fadeIn();
    else
        $("#store_cart").show();
}

function showProducts(fade)
{
    $("#store_cart").hide();
    if( fade )
        $("#store_products").fadeIn();
    else
        $("#store_products").show();
}

var g_cartList = false;
function buySong(product_id)
{
    var cart = "&artist_id=" + g_artistId;
    cart += "&product_id=" + product_id;
    
    jQuery.ajax(
    {
        type: 'POST',
        url: "/data/cart.php",
        data: cart,
        dataType: 'json',
        success: function(data) 
        {
            g_cartList = data;
            showCart();
        },
        error: function()
        {
            //window.alert("Error!");
        }
    });
    
    showStore();
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

