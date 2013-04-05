
var g_showingStore = false;
var g_storeCurrentProductIndex = false;

$(document).ready(storeReady);
function storeReady()
{
    $('#store_tab').scrollbar();
}

function showStore(product_id)
{
    $('#popup_tab_list').hide();
    if( g_showingStore && !product_id )
    {
        hideTab();
        updateAnchor({product_id: ""});
    }
    else
    {
        hideAllTabs();
        showContentPage();
        storeShowProductList();
        g_showingStore = true;
        $('#store_tab').show();
        
        $('#store_tab').scrollbar("repaint");
        
        if( product_id )
            storeShowProductId(product_id);
    }
}

function hideStore()
{
    g_showingStore = false;
    $('#store_tab').hide();
    updateAnchor({product_id: ""});
}
function storeShowProductList()
{
    $('#store_tab #store_back').hide();
    $('#store_tab .store_content').hide();
    $('#store_tab #product_list').show();
    
    $('#store_tab').scrollbar("repaint");
    updateAnchor({product_id: ""});
}
function storeShowProductId(id)
{
    for( var i = 0 ; i < g_productList.length ; ++i )
    {
        var product = g_productList[i];
        if( product.id == id )
        {
            storeShowProduct(i);
            return;
        }
    }
}
function storeShowProduct(index)
{
    g_storeCurrentProductIndex = index;
    var product = g_productList[index];
    
    var html = "<span class='arrow'></span><span class='text' onclick='storeShowProductList();'>{0} Product List</span>".format(g_artistName);
    $('#product_info .store_title').html(html);
    $('#product_info .img_holder img').attr('src',product.image);
    $('#product_info .name').html(product.name);

    if( product.price > 0.0 )
    {
        $('#product_info .dollar_price').removeClass("free");
        $('#product_info .price').html(product.price);
        $('#product_info .buy_free').hide();
        $('#product_info .buy').show();
    }
    else
    {
        $('#product_info .dollar_price').addClass("free");
        $('#product_info .price').html("FREE");
        $('#product_info .buy').hide();
        $('#product_info .buy_free').show();
    }
    $('#product_info .description').html(product.description);
    
    $('#store_tab .store_content').hide();
    $('#store_tab #store_back').show();
    $('#store_tab #product_info').show();
    
    $('#store_tab').scrollbar("repaint");
    updateAnchor({product_id: product.id});
}

function storeBuyProduct()
{
    var product = g_productList[g_storeCurrentProductIndex];
    
    if( typeof product.extra !== 'undefined'
       && typeof product.extra.size_to_url !== 'undefined' )
    {
        var size = $('#product_size').val();
        var url = product.extra.size_to_url[size];
        
        window.open(url,'_blank');
    }
    else if( typeof product.extra !== 'undefined'
            && typeof product.extra.reserve_url !== 'undefined')
    {
        var url = product.extra.reserve_url;
        window.open(url,'_blank');
    }
    else
    {
        storeBuyProductId(product.id);

        $('#buy_now_result .store_title').html("{0} > {1}".format(g_artistName,product.name));
        $('#buy_now_result .name').html(product.name);

        $('#store_tab .store_content').hide();
        $('#store_tab #store_back').show();
        $('#store_tab #buy_now_result').show();
            
        $('#store_tab').scrollbar("repaint");
        updateAnchor({product_id: ""});
    }
}
function storeBuyProductId(product_id)
{
    var cart = "";

    cart += "&method=post";
    cart += "&artist_id=" + g_artistId;
    cart += "&product_id=" + product_id;
    cart += "&quantity=1";
    
    var url = "{0}/data/cart.php".format(g_cartBaseUrl);
    
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
function storeBuyFreeProduct()
{
    var product = g_productList[g_storeCurrentProductIndex];
    
    var args = {
        artist_id: g_artistId,
        product_id: product.id
    };
    
    var url = "{0}/data/free_product.php".format(g_cartBaseUrl);
    
    jQuery.ajax(
    {
        type: 'GET',
        url: url,
        data: args,
        dataType: 'jsonp',
        success: function(data) 
        {
            if( data.success )
            {
                $('#free_product_success .store_title').html("{0} > {1}".format(g_artistName,product.name));
                $('#free_product_success .name').html(product.name);
                $('#store_tab .store_content').hide();
                $('#store_tab #store_back').show();
                $('#store_tab #free_product_success').show();
            }
            else
            {
                $('#free_product_need_fan .store_title').html("{0} > {1}".format(g_artistName,product.name));
                $('#free_product_need_fan .name').html(product.name);
                $('#store_tab .store_content').hide();
                $('#store_tab #store_back').show();
                $('#store_tab #free_product_need_fan').show();
            }
            $('#store_tab').scrollbar("repaint");
            updateAnchor({product_id: ""});
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

function storeGoToFan()
{
    var url = "{0}/fan/".format(g_fanBaseUrl);
    window.open(url,'_blank');
}
function storeFanSignup()
{
    var url = "{0}/signup.php".format(g_fanBaseUrl);
    window.open(url,'_blank');
}
function storeFanLogin()
{
    var url = "{0}/login.php".format(g_fanBaseUrl);
    window.open(url,'_blank');
}

