
var g_currentUserPageIndex = false;

$(document).ready(userTabReady);
function userTabReady()
{
    $('#user_tab').scrollbar();
    $('#contact_tab').scrollbar();
    $('#comment_tab').scrollbar( { measureTag: "#comment_tab .fb_container" } );
    maybeShowMoreTabsButton();
    $(window).resize(maybeShowMoreTabsButton);
}

function maybeShowMoreTabsButton()
{
    if( $('#top_bar .content_tabs .tab:first').position() )
    {
        var top_first = $('#top_bar .content_tabs .tab:first').position().top;
        var top_last = $('#top_bar .content_tabs .tab:last').position().top;
        
        if( top_first == top_last )
        {
            $('#more_tabs_button').hide();
        }
        else
        {
            $('#more_tabs_button').show();
        }
    }
}
function showMoreTabs()
{
    $('#popup_tab_list').show();
}
function closeMoreTabs()
{
    $('#popup_tab_list').hide();
}

function showUserPage(i)
{
    closeMoreTabs();
    hideAllTabs();
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
    $('#user_tab').scrollbar("repaint");
    window.setTimeout(function() { $('#user_tab').scrollbar("repaint"); },100);
    
    tabUpdateViews(page.id);
}
function hideTab()
{
    hideAllTabs();
    hideContentPage();    
}

function hideAllTabs()
{
    $('#user_tab').hide();
    g_currentUserPageIndex = false;

    $('#contact_tab').hide();
    g_showingContactPage = false;

    $('#comment_tab').hide();
    g_showingCommentPage = false;
    
    hideStore();
    
    $('#embed_tab').hide();
}

var g_showingContactPage = false;
function showContact()
{
    $('#popup_tab_list').hide();
    if( g_showingContactPage )
    {
        hideTab();
    }
    else
    {
        hideAllTabs();
        showContentPage();
        g_showingContactPage = true;
        clickContactContact();
        $('#contact_tab').show();
        $('#contact_tab').scrollbar("repaint");
    }
}

function clickContactContact()
{
    $('#contact_tab .booking_container').hide();
    $('#contact_tab .contact_container').show();
    $('#contact_tab .title').removeClass('active');
    $('#contact_tab .title.contact').addClass('active');

    $('#contact_tab').scrollbar("repaint");
}
function clickContactBooking()
{
    $('#contact_tab .contact_container').hide();
    $('#contact_tab .booking_container').show();
    $('#contact_tab .title').removeClass('active');
    $('#contact_tab .title.booking').addClass('active');

    $('#contact_tab').scrollbar("repaint");    
}

function submitContact()
{
    var artist_id = g_artistId;
    var name = $('#contact_tab .contact_container #name').val();
    var email = $('#contact_tab .contact_container #email').val();
    var comments = $('#contact_tab .contact_container #message').val();
    
    if( name.length == 0 
       || comments.length == 0 
       || !email.match(EMAIL_REGEX) 
       )
    {
        window.alert("Please enter all required fields.");
    }
    else
    {
        $('#contact_tab .contact_container .contact_form').hide();
        $('#contact_tab .contact_container .success').show();
        
        var args = {
            artist_id: g_artistId,
            name: name,
            email: email,
            comments: comments
        };
        
        var url = "/data/artist_contact.php";
        jQuery.ajax(
        {
            type: 'POST',
            url: url,
            data: args,
            dataType: 'json',
            success: function(data) {},
            error: function() {}
        }); 
    }
}
function submitBooking()
{
    var artist_id = g_artistId;
    var name = $('#contact_tab .booking_container #name').val();
    var email = $('#contact_tab .booking_container #email').val();
    var date = $('#contact_tab .booking_container #event_date').val();
    var location = $('#contact_tab .booking_container #event_location').val();
    var budget = $('#contact_tab .booking_container #event_budget').val();
    var comments = $('#contact_tab .booking_container #message').val();
    
    if( name.length == 0 
       || comments.length == 0 
       || !email.match(EMAIL_REGEX) 
       || location.length == 0
       || date.length == 0
       )
    {
        window.alert("Please enter all required fields.");
    }
    else
    {
        $('#contact_tab .booking_container .booking_form').hide();
        $('#contact_tab .booking_container .success').show();
        
        var submit = "";
        submit += "&artist_id=" + artist_id;
        submit += "&name=" + escape(name);
        submit += "&email=" + escape(email);
        submit += "&date=" + escape(date);
        submit += "&location=" + escape(location);
        submit += "&budget=" + escape(budget);
        submit += "&comments=" + escape(comments);
        
        $.post("/data/booking.php", submit, function(response) { });
    }
}

function showEmbedTab()
{
    $('#popup_tab_list').hide();
    hideAllTabs();
    showContentPage();
    $('#embed_tab').show();
    $('#embed_tab').scrollbar("repaint");
}


