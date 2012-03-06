
var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

function setupPageLinks()
{
    $('.aClose').hide();
    $('.videos').hide();
    
    /* Close */
    $('.aClose').click(function() {
        fadeAllPageElements();
    });
    
    if( !IS_IPAD )
    {
        $('#right_box').mouseover(mouseoverRightBox);
        $('#right_box').mouseout(mouseoutRightBox);
    }
    
    if( typeof g_userName != "undefined" && g_userName )
    {
        $("#login_signup").hide();
        $("#back_to_admin").show();
    }
    
    if( !IS_IPAD )
    {
        $('#navigation').mouseover(mouseoverNavigation);
        $('#navigation').mouseout(mouseoutNavigation);
    }
    scrollNavigation();
    $(window).resize(scrollNavigation);
    
    if( IS_OLD_IE )
        $('#right_box .expand_box').hide();
}

$(document).ready(setupPageLinks);
var g_navigationOpen = false;
var g_navigationTimer = false;
function toggleNavigation()
{
    if( g_navigationTimer !== false )
    {
        window.clearTimeout(g_navigationTimer);
        g_navigationTimer = false;
    }
    if( g_navigationOpen )
        closeNavigation();
    else
        openNavigation();
}

function openNavigation()
{
    if( !g_navigationOpen )
    {
        g_navigationOpen = true;
        $('#navigation').animate({ top: "0px" }, 300);
    }
}
function closeNavigation()
{
    if( g_navigationOpen )
    {
        g_navigationOpen = false
        $('#navigation').animate({ top: "-40px" }, 300);
    }
}
function mouseoverNavigation()
{
    if( g_navigationTimer !== false )
    {
        window.clearTimeout(g_navigationTimer);
        g_navigationTimer = false;
    }
    openNavigation();
}
function mouseoutNavigation()
{
    g_navigationTimer = window.setTimeout(closeNavigation,700);
}
function scrollNavigation()
{
    $('#navigation .tab_bar').scrollLeft(200);
}

var g_rightBoxOpen = false;
function openRightBox()
{
    if( !g_rightBoxOpen )
    {
        g_rightBoxOpen = true;
        if( IS_OLD_IE )
            $('#right_box .expand_box').show();
        var height = $('#right_box .expand_box')[0].scrollHeight;
        $('#right_box .expand_box').animate({ height: height + "px" }, 300);
        $('#right_box .up_down_arrow').addClass('open');
    }
}
function closeRightBox()
{
    if( g_rightBoxOpen )
    {
        g_rightBoxOpen = false;
        $('#right_box .expand_box').animate({ height: "0px" }, 300);
        $('#right_box .up_down_arrow').removeClass('open');
        if( IS_OLD_IE )
            $('#right_box .expand_box').hide();
    }
}
function toggleRightBox()
{
    if( g_rightBoxOpen ) 
        closeRightBox();
    else
        openRightBox();
}
var g_rightBoxTimer = false;
function mouseoverRightBox()
{
    if( g_rightBoxTimer !== false )
    {
        window.clearTimeout(g_rightBoxTimer);
        g_rightBoxTimer = false;
    }
    openRightBox();
}
function mouseoutRightBox()
{
    g_rightBoxTimer = window.setTimeout(closeRightBox,500);
}

function fadeAllPageElements()
{
    $('.dragger_container').fadeOut();
    $('.aClose').fadeOut();
    $('.videos').fadeOut();
    $('#user_page_wrapper').fadeOut();
    $('#store_wrapper').fadeOut();
    $('#comments_wrapper').fadeOut();
    $('#contact_wrapper').fadeOut();
    hidePlaylist();
}

function internalShowUserPage(i)
{
    var page = g_pageList[i];
    $('#page_title').text(page['title']);
    $('#page_content').html(page['content']);
    if( page['image'] )
    {
        $('#page_image').attr('src',page['image']);
        $('#page_image_holder').show();
    }
    else
    {
        $('#page_image_holder').hide();
    }
    $('#user_page_wrapper').fadeIn();
}

function showUserPage(i)
{
    fadeAllPageElements();
    window.setTimeout(function() { internalShowUserPage(i); },300);
}
function closeUserPage()
{
    fadeAllPageElements();
}

function showComments()
{
    fadeAllPageElements();
    window.setTimeout(function() { $('#comments_wrapper').fadeIn(); },300);
}
function closeComments()
{
    fadeAllPageElements();
}
function showContact()
{
    fadeAllPageElements();
    window.setTimeout(function() { $('#contact_wrapper').fadeIn(); },300);
}
function showVideos()
{
    fadeAllPageElements();
    window.setTimeout(function() { $('.videos').fadeIn(); $('.aClose').fadeIn(); },300);
}

function showBookingsForm()
{
    $('#contact .contact').hide();
    $('#contact .bookings').fadeIn('fast');
}
function showContactForm()
{
    $('#contact .bookings').hide();
    $('#contact .contact').fadeIn('fast');
}

function sendContactForm()
{
    var artist_id = g_artistId;
    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var comments = $('#contact_message').val();

    if( name.length == 0 
       || comments.length == 0 
       || !email.match(EMAIL_REGEX) 
       )
    {
        window.alert("Please enter all required fields.");
    }
    else
    {
        closeContactTab();
        window.alert("Thank you.  Your message will be forwarded to the artist.");
        
        var submit = "&form=send";
        submit += "&artist_id=" + artist_id;
        submit += "&name=" + escape(name);
        submit += "&email=" + escape(email);
        submit += "&comments=" + escape(comments);
        
        $.post("jplayer/ajax.php", submit, function(response) { });
    }
}
function sendBookingForm()
{
    var artist_id = g_artistId;
    var name = $('#booking_name').val();
    var email = $('#booking_email').val();
    var date = $('#booking_date').val();
    var location = $('#booking_location').val();
    var budget = $('#booking_budget option:selected').val();
    var comments = $('#booking_message').val();
    
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
        closeContactTab();
        window.alert("Thank you for your request.  Someone will contact you within 7 days.");

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
function closeContactTab()
{
    showContactForm();

    $('#contact_name').val('');
    $('#contact_email').val('');
    $('#contact_message').val('');

    $('#booking_name').val('');
    $('#booking_email').val('');
    $('#booking_date').val('');
    $('#booking_location').val('');
    $('#booking_message').val('');

    $('#contact_wrapper').hide();
    fadeAllPageElements();
}

