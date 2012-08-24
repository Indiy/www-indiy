
var g_musicListenUpdated = {};
function musicUpdateListens(song_id,index)
{
    if( song_id in g_musicListenUpdated )
        return false;

    g_musicListenUpdated[song_id] = true;

    var args = {
        artist_id: g_artistId,
        song_id: song_id
    };

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
            var track_listens = data['element_views'];
            g_musicList[index].listens = track_listens;
            playerUpdateTotalViewCount();
            playerTrackInfo(false,track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}

function videoUpdateViews(id,index)
{
    return genericUpdateViews('video',g_videoList,id,index);
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

    g_videoViewsUpdated[type][id] = true;

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

