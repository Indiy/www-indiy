(function(){

window.defaultReady = defaultReady;
window.clickMenu = clickMenu;
window.clickClose = clickClose;
window.clickPlus = clickPlus;
window.clickPlaylist = clickPlaylist;
window.clickPlaylistItem = clickPlaylistItem;
window.catalogClickPlaylistMediaItem = catalogClickPlaylistMediaItem;
window.clickShowTab = clickShowTab;
window.clickShowStore = clickShowStore;
window.clickShowSocial = clickShowSocial;
window.clickShowShare = clickShowShare;
window.clickStoreItem = clickStoreItem;
window.clickSize = clickSize;
window.clickBuyProduct = clickBuyProduct;
window.showProductById = showProductById;
window.showContentTab = showContentTab;
window.clickSocial = clickSocial;

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
    instagramInsert();
}
// defaultReady is run from generalOnReady

function clickMenu()
{
    showContentTab('.playlist_tab');
}
function clickClose()
{
    showContentTab('.home_tab');
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
 
    var playlist = g_playlistList[i];
    playlistChangePlaylist(playlist,j);
    clickClose();
}
function catalogClickPlaylistMediaItem(playlist_index,child_playlist_index,playlist_item_index)
{
    $('#playlist_tab').hide();
    var playlist = g_playlistList[playlist_index];
    
    if( typeof playlist_item_index !== 'undefined'
        && playlist_item_index !== false )
    {
        playlist = playlist.items[child_playlist_index];
    }
    else
    {
        playlist_item_index = child_playlist_index;
    }
    playlistChangePlaylist(playlist,playlist_item_index);
}

function clickShowTab(i)
{
    showContentTab('#user_tab_' + i);
}
function clickShowStore()
{
    showContentTab('.store_list_tab');
}
function clickShowSocial()
{
    showContentTab('.social_tab');
}
function clickShowShare()
{
    showContentTab('.share_tab');
}

function clickStoreItem(i)
{
    showContentTab('#product_tab_' + i);
    window.scrollTo(0,0);
}
function showProductById(product_id)
{
    for( var i = 0 ; i < g_productList.length ; ++i )
    {
        var product = g_productList[i];
        if( product.id == product_id )
        {
            clickStoreItem(i);
            break;
        }
    }
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
        
        showContentTab('.store_add_success_tab');
    });
}

function clickSocial(type)
{
    $('.social_tab .social_item').hide();
    $('.social_tab .social_item.social_' + type).show();
}

function showContentTab(name)
{
    $('.content_tab').removeClass('open');
    $('.content_tab' + name).addClass('open');
}

})();