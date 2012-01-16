
function setupPageLinks()
{

    $(".dragger_container").fadeOut();
    
    
    // Pauses the audio player when a user opens a video                
    $("div.playlist_video img").click(function(event){
        $("#jquery_jplayer").jPlayer("pause");
    });
    
             

    $('#image').hide();
    $('.page').hide();
    $('.comments').hide();
    $('.contact').hide();
    $('.aClose').hide();
    $('.store').hide();
    $('.checkout').hide();
    $('.videos').hide();
    $('.store_Close').hide();
    $('.contact_Close').hide();
    
    /* Close */
    $('.aClose').click(function() {
        fadeAllPageElements();
    });

    $('.store_Close').click(function() {
                       fadeAllPageElements();
                       });

    $('.contact_Close').click(function() {
                            fadeAllPageElements();
                            });

    
    //<?=$pagesJava;?>
    
    /* Comment */
    $('.aComment').click(function() {
        fadeAllPageElements();
        setTimeout(function(){ 
            $('.comments').fadeIn();
            $('.aClose').fadeIn();
        }, 450);
    });
    
    /* Contact */
    $('.aContact').click(function() {
        fadeAllPageElements();
        setTimeout(function(){ 
            $('.contact').fadeIn();
            $('.contact_Close').fadeIn();
        }, 450);
    });
    
    /* Store */
    $('.aStore').click(function() {
        fadeAllPageElements();
        setTimeout(function(){ 
            $('.store').fadeIn();
            $('.store_Close').fadeIn();
        }, 450);
        var cart = "&paypal=<?=$paypalEmail;?>&cart=true&artist=<?=$artist_id;?>";
        $.post("jplayer/ajax.php", cart, function(items) {
              $(".cart").html(items);
              });
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
    
    // Shopping Cart Functionality
    $("div.addtocart").click(function(event){
        var pro = $(this).text();
        var cart = "&paypal=<?=$paypalEmail;?>&cart=true&artist=<?=$artist_id;?>&product="+pro;
        $.post("jplayer/ajax.php", cart, function(items) {
            $(".cart").html(items);
            showCart(false);
        });
    });
    
    $(".showstore").click(function(event){
        showProducts(true);
    });
    $(".showcart").click(function(event){
        showCart(true);
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
    
    // All new socialize tab functionality
    var socialize_minimized = true;
    var socialize_tab = '';
    $(".socialize .title").click(function() {
        if( socialize_minimized )
            open_socialize();
        else
            close_socialize();
    });
    
    $(".socialize .facebook").click(function() {
        $(".buttons div").removeClass("active");
        $(this).addClass("active");
        $(".socialize .body .tab").hide();
        $(".socialize .body #facebook").show();
        
        if( socialize_tab == 'facebook' ) {
            if( socialize_minimized )
                open_socialize();
            else
                close_socialize();
        }
        else {
            open_socialize();
            socialize_tab = 'facebook';
        }
        
    });

    $(".socialize .twitter").click(function() {
        $(".buttons div").removeClass("active");
        $(this).addClass("active");
        $(".socialize .body .tab").hide();
        $(".socialize .body #twitter").show();
        
        if( socialize_tab == 'twitter' ) {
            if( socialize_minimized )
                open_socialize();
            else
                close_socialize();
        }
        else {
            open_socialize();
            socialize_tab = 'twitter';
        }
        
    });
    
    $(".socialize .email").click(function() {
        $(".buttons div").removeClass("active");
        $(this).addClass("active");
        $(".socialize .body .tab").hide();
        $(".socialize .body #email").show();
        
        if( socialize_tab == 'email' ) {
            if( socialize_minimized )
                open_socialize();
            else
                close_socialize();
        }
        else {
            open_socialize();
            socialize_tab = 'email';
        }
        
    });
    
    $(".socialize .share").click(function() {
        $(".buttons div").removeClass("active");
        $(this).addClass("active");
        $(".socialize .body .tab").hide();
        $(".socialize .body #share").show();
        
        if( socialize_tab == 'share' ) {
            if( socialize_minimized )
                open_socialize();
            else
                close_socialize();
        }
        else {
            open_socialize();
            socialize_tab = 'share';
        }
        
    });
    
    function open_socialize() {
        if( socialize_minimized ) {
            $(".socialize").animate({ bottom: "0" }, 300);
            socialize_minimized = false;
        }
    }
    
    function close_socialize() {
        $(".buttons div").removeClass("active");
        if( !socialize_minimized ) {
            $(".socialize").animate({ bottom: "-361px" }, 300);
            socialize_minimized = true;
        }
    }
    
    $(".bottom").click(function(event)
    {
        if(!g_logoOpen) 
        {
            $('div#logo').css("background-position","right bottom");
            $('#makeroomforlogo').animate({ height: "160px" }, 300);
        } 
        else 
        {
            $('div#logo').css("background-position","left bottom");
            $('#makeroomforlogo').animate({ height: "0px" }, 300);
        }
        g_logoOpen = !g_logoOpen;
        //$('#logo').toggleClass('openlogo');
    }); 
    
    $(".submitform").click(function(event){
    
        
    });
    
    if( typeof g_userName != "undefined" && g_userName )
    {
        var html = "<a href='<?=trueSiteUrl();?>/manage/artist_management.php'>";
        html += g_userName;
        html += "</a>";
        html += " | ";
        html += "<a href='<?=trueSiteUrl();?>/manage/logout.php'>Logout</a>";
        $("#login_signup").html(html);
    }
}

$(document).ready(setupPageLinks);

function fadeAllPageElements()
{
    $('.dragger_container').fadeOut();
    $('.aClose').fadeOut();
    $('.comments').fadeOut();
    $('.contact').fadeOut();
    $('.store').fadeOut();
    $('.page').fadeOut();
    $('.videos').fadeOut();
    $('.store_Close').fadeOut();
    $('.contact_Close').fadeOut();
    hidePlaylist();
}

function sendContactForm()
{
    $('.contact table').hide();
    $('#contact_thanks').show();
    
    var artist_id = g_aristId;
    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var phone = $('#contact_phone').val();
    var comments = $('#contact_comments').val();
    
    var submit = "&form=send";
    submit += "&artist_id=" + artist_id;
    submit += "&name=" + escape(name);
    submit += "&email=" + escape(email);
    submit += "&phone=" + escape(phone);
    submit += "&comments=" + escape(comments);
    
    $.post("jplayer/ajax.php", submit, function(response) { });
}
function sendBookingForm()
{
    $('.contact table').hide();
    $('#contact_thanks').show();
    
    var artist_id = g_aristId;
    var name = $('#contact_name').val();
    var email = $('#contact_email').val();
    var date = $('#booking_date').val();
    var location = $('#booking_location').val();
    var budget = $('#booking_budget option:selected').val();
    var comments = $('#booking_comments').val();
    
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

