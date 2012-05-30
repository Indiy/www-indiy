

function showUserPage(i)
{
    if( toggleContentPage() )
    {
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
    else
    {
        $('#user_tab').hide();
    }
}

function hideAllTabs()
{
    $('#user_tab').hide();
}
