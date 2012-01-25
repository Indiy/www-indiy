
var EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

function setupPageLinks()
{
    $(".dragger_container").fadeOut();
    
    // Pauses the audio player when a user opens a video                
    $("div.playlist_video img").click(function(event){
        $("#jquery_jplayer").jPlayer("pause");
    });

    $('.aClose').hide();
    $('.videos').hide();
    
    /* Close */
    $('.aClose').click(function() {
        fadeAllPageElements();
    });

    /* Videos */
    $('.aVideos').click(function() {
        fadeAllPageElements();
        setTimeout(function(){ 
            $('.videos').fadeIn();
            $('.aClose').fadeIn();
        }, 450);
    });         
    
    /* Playlist Controller */
    $("#playlistaction").click(function(){
        $(this).parent("pauseOthers");
        $(this).parent(".jp-playlist").animate({"left": "0px"}, "fast");
        $(this).hide();
        $(this).parent(".jp-playlist").children("#playlisthide").show();
    });
    $("#playlisthide").click(function(){
        hidePlaylist();
    });
    
    $("a.jp-previous").mouseover(function(event){
        $(this).animate({
            left: "0px"
        }, 250);                
    });
    
    $("a.jp-previous").mouseout(function(event){
        $(this).animate({
            left: "-169px"
        }, 250);
    }); 
    
    $("a.jp-next").mouseover(function(event){
        $(this).animate({
            right: "0px"
        }, 250);    
    });
    
    $("a.jp-next").mouseout(function(event){
        $(this).animate({
            right: "-138px"
        }, 250);
    });
    
    if( typeof g_userName != "undefined" && g_userName )
    {
        var html = "<a href='" + g_siteUrl + "/manage/artist_management.php'>";
        html += g_userName;
        html += "</a>";
        html += " | ";
        html += "<a href='" + g_siteUrl + "/manage/logout.php'>Logout</a>";
        $("#login_signup").html(html);
    }
}

$(document).ready(setupPageLinks);

var g_rightBoxOpen = false;

function toggleRightBox()
{
    if( !g_rightBoxOpen ) 
    {
        var h = $('#right_box .logo_box img').height() + 10 + 6;
        var new_height = '' + h + 'px';
        $('#right_box .logo_box').animate({ height: new_height }, 300);
    } 
    else 
    {
        $('#right_box .logo_box').animate({ height: "10px" }, 300);
    }
    $('#right_box .up_down_arrow').toggleClass('open');
    g_rightBoxOpen = !g_rightBoxOpen;
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
function closeContact()
{
    fadeAllPageElements();
}

function sendContactForm()
{
    var artist_id = g_artistId;
    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var comments = $('#contact_comments').val();

    if( name.length == 0 
       || name == 'Name...'
       || comments.length == 0 
       || !email.match(EMAIL_REGEX) 
       || email == 'Email...'
       )
    {
        window.alert('Please enter all required fields.');
    }
    else
    {
        $('.contact table').hide();
        $('#contact_thanks').show();
        
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
    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var date = $('#booking_date').val();
    var location = $('#booking_location').val();
    var budget = $('#booking_budget option:selected').val();
    var comments = $('#booking_comments').val();
    
    if( name.length == 0 
       || name == 'Name...'
       || comments.length == 0 
       || !email.match(EMAIL_REGEX) 
       || email == 'Email...'
       || location.length == 0
       || date.length == 0
       )
    {
        window.alert('Please enter all required fields.');
    }
    else
    {
        $('.contact table').hide();
        $('#contact_thanks').show();

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
    $('.contact table').show();
    $('#contact_thanks').hide();
    $('#contact_name').val('');
    $('#contact_email').val('');
    $('#contact_comments').val('');
    $('#contact_name').val('');
    $('#contact_email').val('');
    $('#booking_date').val('');
    $('#booking_location').val('');
    $('#booking_comments').val('');
    fadeAllPageElements();
}

