

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