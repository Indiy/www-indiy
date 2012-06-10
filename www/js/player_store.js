

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

