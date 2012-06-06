

var g_searchData = false;
var g_lastSearch = false;

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
        },
        error: function()
        {
        }
    });
}

function closeSearch()
{
    $('#search').fadeOut();
}
function openSearch()
{
    $('#search').fadeIn();
}

function searchChange()
{
    var s = $('#search input').val();
    if( s === g_lastSearch )
        return;
    g_lastSearch = s;

    console.log("search: " + s);    
}

