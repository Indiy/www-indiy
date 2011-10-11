

// Helper functions for artist home page

function artistHomeReady()
{
    $('#login_dialog_close').click(closeLogin);
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
            alert('Failed to get listens!');
        }
    });
}

function showLogin()
{
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();
	
    $('#mask').css({'width':maskWidth,'height':maskHeight});
    
    $('#mask').fadeIn(600);	
    $('#mask').fadeTo("slow",0.5);	
	
    var winH = $(window).height();
    var winW = $(window).width();
    
    var dialogHeight = $('#login_dialog').height();
    var dialogWidth = $('#login_dialog').width();
    
    var top = winH / 2 - dialogHeight / 2;
    var left = winW / 2 - dialogWidth / 2;
    
    $('#login_dialog').css('top',top);
    $('#login_dialog').css('left',left);
	
    $('#login_dialog').fadeIn(600); 
}

function closeLogin()
{
    $('#login_dialog').fadeOut(100);
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
	   dataType: "html",
	   success: function(msg){			   	   
		if(msg==0)	{	
			$("#validate-login").html("");
			$("#validate-login").html("<span class='ui-error'>Wrong username or password. Please try again.</span>");					 
			 return false;
		}else if(msg==1){			
			window.location.href='index.php/?p=home';	
			return true;
		 }
		 else{
 			$("#validate-login").html("");
			 $("#validate-login").html("<span class='ui-error'>Please enter the username and password.</span>");					 
			 return false;
		 }
	   }
	  
	 });
}




$(document).ready(artistHomeReady);


