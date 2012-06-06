

var g_searchData = false;
var g_lastSearch = false;
var g_searchResults = false;

var g_artistMap = {};

$(document).ready(searchOnReady);
function searchOnReady()
{
    $('#search input').bind("propertychange keyup input paste",searchChange);

    jQuery.ajax(
    {
        type: 'GET',
        url: "/data/search.php",
        dataType: 'json',
        success: function(data) 
        {
            g_searchData = data;
            g_artistMap = {};
            for( var i = 0 ; i < data.artists.length ; ++i )
            {
                var artist = data.artists[i];
                g_artistMap[artist.id] = artist;
            }
        },
        error: function()
        {
        }
    });
}

function closeSearch()
{
    $('#search').fadeOut();
    $('#search input').val("");
}
function openSearch()
{
    $('#search').fadeIn();
    $('#search input').val("");
}

function searchChange()
{
    var s = $('#search input').val();
    if( s === g_lastSearch )
        return;
    g_lastSearch = s;
    
    if( !s || s.length == 0 )
    {
        g_searchResults = false;
    }
    else
    {
        searchRun(s);
    }
    searchRenderResults();
}

function searchRun(s)
{
    var re = new RegExp("($|\s)" + s);

    var artists = searchTestList(g_searchData.artists,re,true,"/artist/images/");
    var songs = searchTestList(g_searchData.songs,re,false,"/artist/images/");
    var videos = searchTestList(g_searchData.videos,re,false,"/artist/images/");
    var photos = searchTestList(g_searchData.photos,re,false,"/artist/photos/");
    
    var results = {
        'artists': artists,
        'songs': songs,
        'videos': videos,
        'photos': photos
    };
    
    g_searchResults = results;
}

function searchTestList(re,list,is_artist,type,image_path)
{
    var ret = [];
    
    function createFromArtist(a)
    {
        var r = {
            'artist': a.artist,
            'url': g_trueSiteUrl.replace("http://www.","http://" + a.url)
        };
        return r;
    }

    for( var i = 0 ; i < list.length ; ++i )
    {
        var item = list[i];
        if( is_artist )
        {
            if( item.artist.match(re) )
            {
                var r = createFromArtist(item);
                r['type'] = "artist";
                r['image'] = image_path + item.logo;
                r['value'] = a.artist;
                ret.push(r);
            }
        }
        else
        {
            if( item.name.match(re) && item.artist_id in g_artistMap )
            {
                var artist = g_artistMap[item.artist_id];
                var r = createFromArtist(artist);
                r['type'] = type;
                r['value'] = item.name;
                r['image'] = image_path + item.image;
                r['url'] += "#{0}_id={1}".format(type,item.id);
                ret.push(r);
            }
        }
    }
    return ret;
}

function searchRenderResults()
{
    function renderList(list,title)
    {
        if( list.length == 0 )
            return;
        
        var html = "";
        
        html += "<div class='result_group'>";
        html += " <div class='title'>{0}</div>".format(title);
        
        for( var i = 0 ; i < list.length ; ++i )
        {
            var item = list[i];
            html += "<a href='{0}'>".format(item.url);;
            html += " <div class='item'>";
            html += "  <img url='{0}'/>".format(item.image);
            html += "  <div class='value'>{0}</div>".format(item.value);
            html += " </div>";
            html += "</a>";
        }
        
        html += "</div>";
        $('#search_results').append(html);
    }
    
    $('#search_results').empty();
    renderList(g_searchResults.artists,"Artists");
}


