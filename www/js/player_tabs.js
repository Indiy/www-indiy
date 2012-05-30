
var g_currentUserPageIndex = false;

function showUserPage(i)
{
    if( g_currentUserPageIndex == i && g_showingContentPage )
    {
        hideContentPage();
        g_currentUserPageIndex = false;
        $('#user_tab').hide();
    }
    else
    {
        showContentPage();
        g_currentUserPageIndex = i;
        
        var page = g_tabList[i];
        
        $('#user_tab .title').html(page.title);
        if( page.image )
        {
            $('#page_image').attr('src',page.image);
            $('#page_image_holder').show();
        }
        else
        {
            $('#page_image_holder').hide();
        }
        $('#page_content').html(page.content);
        $('#user_tab').show();
    }
}

function hideAllTabs()
{
    g_currentUserPageIndex = false;
    $('#user_tab').hide();
    $('#contact_tab').hide();
}

var g_showingContactPage = false;

function showContact()
{
    if( g_showingContactPage )
    {
        hideContentPage();
        $('#contact_tab').hide();
    }
    else
    {
        showContentPage();
        $('#contact_tab').show();
    }
}

