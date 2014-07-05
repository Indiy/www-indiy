(function(){

window.defaultReady = defaultReady;
window.clickMenu = clickMenu;
window.clickClose = clickClose;
window.clickPlus = clickPlus;
window.clickPlaylist = clickPlaylist;
window.clickPlaylistItem = clickPlaylistItem;
window.clickShowTab = clickShowTab;
window.clickShowStore = clickShowStore;
window.clickShowSocial = clickShowSocial;
window.clickShowShare = clickShowShare;
window.clickStoreItem = clickStoreItem;
window.clickSize = clickSize;
window.clickBuyProduct = clickBuyProduct;

function defaultReady(show_social)
{
    if( IS_IOS || IS_PHONE || IS_TABLET )
    {
        g_mediaAutoStart = false;
    }
    if( IS_EMBED )
    {
        $('body').addClass('embed');
        g_mediaAutoStart = false;
    }

    twitterInsert();
}
// defaultReady is run from generalOnReady

function clickMenu()
{
    $('.content_tab').hide();
    $('.playlist_tab.content_tab').show();
}
function clickClose()
{
    $('.content_tab').hide();
    $('.home_tab.content_tab').show();
}
function clickPlus()
{
    $('.home_tab .right_menu .extended_menu').toggle();
}
function clickPlaylist(i)
{
    $('.playlist_tab .playlist_list .playlist').removeClass('active');
    var sel = ".playlist_tab .playlist_list #playlist_{0}".format(i);
    $(sel).addClass('active');
}
function clickPlaylistItem(i,j)
{
    $('.playlist_tab .playlist_list .playlist .track_name').removeClass('active');
    var sel = ".playlist_tab .playlist_list #playlist_{0} #track_{1}".format(i,j);
    $(sel).addClass('active');
}

function clickShowTab(i)
{
    $('.content_tab').hide();
    $('#user_tab_' + i).show();
}
function clickShowStore()
{
    $('.content_tab').hide();
    $('.store_list_tab.content_tab').show();
}
function clickShowSocial()
{
    $('.content_tab').hide();
    $('.social_tab.content_tab').show();
}
function clickShowShare()
{
    $('.content_tab').hide();
    $('.share_tab.content_tab').show();
}

function clickStoreItem(i)
{
    $('.content_tab').hide();
    $('#product_tab_' + i).show();
    window.scrollTo(0,0);
}

function clickSize(ele)
{
    $(ele).siblings().removeClass('active');
    $(ele).addClass('active');
}

function clickBuyProduct(ele,i)
{
    var product = g_productList[i];
    var size = false;
    if( product.sizes )
    {
        var sel = "#product_tab_{0} .size_list .active".format(i);
        size = $(sel).text();
        if( !size )
        {
            window.alert("Please select a size.");
            return;
        }
    }
    $(ele).attr('disabled',true);
    storeBuyProductId(product.id,size,function(err)
    {
        $(ele).attr('disabled',false);
        
        $('.content_tab').hide();
        $('.store_add_success_tab.content_tab').show();
    });
}

})();