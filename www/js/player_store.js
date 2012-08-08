
$(document).ready(storeReady);
function storeReady()
{
    $('#store_tab').scrollbar();
}

var g_showingStore = false;
function showStore()
{
    if( g_showingStore )
    {
        hideTab();
    }
    else
    {
        hideAllTabs();
        showContentPage();
        storeShowProductList();
        g_showingStore = true;
        $('#store_tab').show();
        
        $('#store_tab').scrollbar("repaint");
    }
}

function hideStore()
{
    g_showingStore = false;
    $('#store_tab').hide();
}
function storeShowProductList()
{
    $('#store_tab #store_back').hide();
    $('#store_tab .store_content').hide();
    $('#store_tab #product_list').show();
    
    $('#store_tab').scrollbar("repaint");
}
var g_storeCurrentProductIndex = false;
function storeShowProduct(index)
{
    g_storeCurrentProductIndex = index;
    var product = g_productList[index];
    
    $('#product_info .store_title').html("{0} > {1}".format(g_artistName,product.name));
    $('#product_info .img_holder img').attr('src',product.image);
    $('#product_info .name').html(product.name);
    $('#product_info .price').html(product.price);
    $('#product_info .description').html(product.description);
    
    $('#store_tab .store_content').hide();
    $('#store_tab #store_back').show();
    $('#store_tab #product_info').show();
    
    $('#store_tab').scrollbar("repaint");
}

function storeBuyProduct()
{
    var product = g_productList[g_storeCurrentProductIndex];
    
    storeBuyProductId(product.id);

    $('#buy_now_result .store_title').html("{0} > {1}".format(g_artistName,product.name));
    $('#buy_now_result .name').html(product.name);

    $('#store_tab .store_content').hide();
    $('#store_tab #store_back').show();
    $('#store_tab #buy_now_result').show();
        
    $('#store_tab').scrollbar("repaint");
}
function storeBuyProductId(product_id)
{
    var cart = "";

    cart += "&method=post";
    cart += "&artist_id=" + g_artistId;
    cart += "&product_id=" + product_id;
    cart += "&quantity=1";
    
    var url = "{0}/data/cart2.php".format(g_cartBaseUrl);
    
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: cart,
        dataType: 'jsonp',
        success: function(data) 
        {
            console.log(data);
        },
        error: function()
        {
            //window.alert("Error!");
        }
    });
}

function storeViewCart()
{
    var url = "{0}/cart.php?artist_id={1}".format(g_cartBaseUrl,g_artistId);
    window.open(url,'_blank');
}
function storeCheckout()
{
    storeViewCart();
}

function clickFreeDownload(index)
{
    $('#mask').show();
    $('#submit_email_popup').show();
}
function closePopup()
{
    $('#mask').hide();
    $('#submit_email_popup').hide();
}

