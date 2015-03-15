
function tabUpdateViews(id)
{
    return genericUpdateViews('tab',id,false);
}

var g_genericViewsUpdated = {
    tab: {},
    media: {}
};

function genericUpdateViews(type,id,item)
{
    if( id in g_genericViewsUpdated[type] )
        return false;

    g_genericViewsUpdated[type][id] = true;

    var args = {
        artist_id: g_artistId,
        type: type,
        id: id
    };
    
    var url = g_trueSiteUrl + "/data/element_views.php?method=POST";
    jQuery.ajax(
    {
        type: 'GET',
        url: url,
        data: args,
        dataType: 'jsonp',
        success: function(data) 
        {
            var total_views = data['total_views'];
            var element_views = data['element_views'];
            if( item )
            {
                item.views = element_views;
                
                playerUpdateTotalViewCount(total_views);
                playerUpdateElementViews(element_views);
            }
        },
        error: function()
        {
        }
    });
    return true;
}

