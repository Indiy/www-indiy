

// Helper functions for artist home page

function artistHomeReady()
{
    //$('#login_dialog_close').click(closeLogin);
}

function updateListens(image)
{
    var url = "/data/listens.php?image=" + image;

    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            g_totalListens = data['total_listens'];
            var track_listens = data['track_listens'];
            //$('#total_listens').text(g_totalListens);
            $('#current_track_listens').text(track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
}

function showPopup(name)
{
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();
	
    $('#mask').css({'width':maskWidth,'height':maskHeight});
    
    $('#mask').fadeIn(600);	
    $('#mask').fadeTo("slow",0.5);	
	
    var winH = $(window).height();
    var winW = $(window).width();
    
    var dialogHeight = $(name).height();
    var dialogWidth = $(name).width();
    
    var top = winH / 2 - dialogHeight / 2;
    var left = winW / 2 - dialogWidth / 2;
    
    $(name).css('top',top);
    $(name).css('left',left);
	
    $(name).fadeIn(600);     
}

function showLogin()
{
    showPopup('#login_dialog');
}

function closeLogin()
{
    $('#login_dialog').fadeOut(100);
    $('#mask').fadeOut(100);
}

function showSignup()
{
    $('#login_dialog').hide();
    showPopup('#signup_dialog');
}

function closeSignup()
{
    $('#signup_dialog').fadeOut(100);
    $('#mask').fadeOut(100);
}

function tryLogin()
{			
	var username = $('#login_username').val();
	var password = $('#login_password').val();

	// Send the ajax request.
	 $.ajax({
	   type: "POST",
	   url: "check_login.php?username="+username+"&password="+password,
	   dataType: "json",
	   success: function(data){
            var result = data['result'];
            if( result == 0 )
            {	
                $("#validate-login").html("<span class='ui-error'>Wrong username or password. Please try again.</span>");					 
                 return false;
            }
            else if( result == 1 )
            {
                window.location.href=data['url'];	
                return true;
            }
            else
            {			
                $("#validate-login").html("<span class='ui-error'>Please enter the username and password.</span>");					 
                return false;
            }
	   }
	  
	 });
}

function showCart(fade)
{
    $("ul.products").hide();
    $(".showcart").hide();
    $(".showstore").show();
    if( fade )
        $(".cart").fadeIn();
    else
        $(".cart").show();
}

function showProducts(fade)
{
    $(".cart").hide();
    $(".showstore").hide();
    $(".showcart").show();
    if( fade )
        $("ul.products").fadeIn();
    else
        $("ul.products").show();
}


$(document).ready(artistHomeReady);


