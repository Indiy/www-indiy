

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
}
function storeBuyProductId(product_id)
{
    var cart = "";
    cart += "&artist_id=" + g_artistId;
    cart += "&product_id=" + product_id;
    cart += "&quantity=1";
    
    jQuery.ajax(
    {
        type: 'POST',
        url: "/data/cart2.php",
        data: cart,
        dataType: 'json',
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
    window.open("/cart.php",'_blank');
}
function storeCheckout()
{
    window.open("/cart.php",'_blank');
}

