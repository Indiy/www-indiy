
function musicUpdateListens(id,index)
{
    return genericUpdateViews('song',g_musicList,id,index);
}

function videoUpdateViews(id,index)
{
    return genericUpdateViews('video',g_videoList,id,index);
}

function photoUpdateViews(id,index)
{
    return genericUpdateViews('photo',g_photoList,id,index);
}
function tabUpdateViews(id)
{
    return genericUpdateViews('tab',false,id,false);
}


var g_genericViewsUpdated = {
    song: {},
    video: {},
    photo: {},
    tab: {}
};
function genericUpdateViews(type,list,id,index)
{
    if( id in g_genericViewsUpdated[type] )
        return false;

    g_genericViewsUpdated[type][id] = true;

    var args = {
        artist_id: g_artistId
    };
    
    var arg_name = "{0}_id".format(type);
    args[arg_name] = id;

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
            if( list !== false )
            {
                list[index].views = element_views;
                
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

