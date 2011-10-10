

// Helper functions for artist home page

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
    //Get the screen height and width
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();
	
    //Set heigth and width to mask to fill up the whole screen
    $('#mask').css({'width':maskWidth,'height':maskHeight});
    
    //transition effect		
    $('#mask').fadeIn(600);	
    $('#mask').fadeTo("slow",0.5);	
	
    //Get the window height and width
    var winH = $(window).height();
    var winW = $(window).width();
    
    //Set the popup window to center
    $(id).css('top',  winH/2-$(id).height()/2);
    $(id).css('left', winW/2-$(id).width()/2);
	
    //transition effect
    $(id).fadeIn(600); 
}