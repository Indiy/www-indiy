
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

function buyProductId(product_id)
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
            renderCart();
            showCart();
        },
        error: function()
        {
            //window.alert("Error!");
        }
    });
}
function buySong(product_id)
{
    buyProductId(product_id);
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

function renderCart()
{
    $('#store_cart_body').empty();
    for( var k in g_cartList )
    {
        var c = g_cartList[k];
        var id = c['id'];
        var name = c['name'];
        var image = c['image'];
        var price = c['price'];
        var shipping = c['shipping'];
        
        var html = "";
        html += "<div class='cart_line'>";
        html += " <div class='image_holder'><img src='{0}'></div>".format(image);
        html += " <div class='name'>{0}</div>".format(name);
        html += " <div class='price_shipping'>";
        html += "  <div class='price'>${0}</div>".format(price.toFixed(2));
        if( shipping )
            html += "  <div class='shipping'>Shipping: ${0}</div>".format(shipping.toFixed(2));
        html += " </div>";
        html += " <div class='remove_holder'>";
        html += "  <div class='remove_button' onclick='deleteCart({0});'>remove</div>".format(id);
        html += " </div>";
        html += "</div>";
        $('#store_cart_body').append(html);
    }
}

function addToCart(i)
{
    var product = g_productList[i];
    buyProductId(product['id']);
}

