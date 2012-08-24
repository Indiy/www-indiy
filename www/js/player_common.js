
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

var g_genericViewsUpdated = {
    video: {},
    photo: {},
    song: {}
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

    var url = "/data/element_views.php";
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            g_totalPageViews = data['total_views'];
            var element_views = data['element_views'];
            list[index].views = element_views;
            playerUpdateTotalViewCount();
            playerTrackInfo(false,element_views);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}

