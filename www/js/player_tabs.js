
var g_currentUserPageIndex = false;

function showUserPage(i)
{
    if( g_currentUserPageIndex == i && g_showingContentPage )
    {
        hideTab();
    }
    else
    {
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
    }
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
}

var g_showingContactPage = false;
function showContact()
{
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
    }
}
var g_showingCommentPage = false;
function showComments()
{
    if( g_showingCommentPage )
    {
        hideTab();
    }
    else
    {
        hideAllTabs();
        showContentPage();
        g_showingCommentPage = true;
        $('#comment_tab').show();
    }
}

function clickContactContact()
{
    $('#contact_tab .booking_container').hide();
    $('#contact_tab .contact_container').show();
    $('#contact_tab .title').removeClass('active');
    $('#contact_tab .title.contact').addClass('active');
}
function clickContactBooking()
{
    $('#contact_tab .contact_container').hide();
    $('#contact_tab .booking_container').show();
    $('#contact_tab .title').removeClass('active');
    $('#contact_tab .title.booking').addClass('active');
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
        
        var submit = "&form=send";
        submit += "&artist_id=" + artist_id;
        submit += "&name=" + escape(name);
        submit += "&email=" + escape(email);
        submit += "&comments=" + escape(comments);
        
        $.post("jplayer/ajax.php", submit, function(response) { });
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

