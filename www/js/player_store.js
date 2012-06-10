

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
    $('#store_tab .store_content').hide();
    $('#store_tab #product_list').show();
}
function storeShowProduct(index)
{
    var product = g_productList[index];
    
    $('#product_info .store_title').html("{0} > {1}".format(g_artistName,product.name));
    $('#product_info .img_holder img').attr(src,product.image);
    $('#product_info .name').html(product.name);
    $('#product_info .price').html(product.price);
    $('#product_info .description').html(product.description);
    
    $('#store_tab .store_content').hide();
    $('#store_tab #product_info').show();
}

