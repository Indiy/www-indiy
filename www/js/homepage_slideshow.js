
frame_speed = 4000;
fade_speed = 1300;
var max_images = 0;

$(document).ready(function()
{
	
	$('.header_wrapper div').hide();
	$(".header_1").show();
	
	$('.header_wrapper div').each(function(index) {
		max_images = max_images + 1;
	});

	if ( $(".header_1").length>0) 
    {
		$(".header_1").show();
		window.setTimeout(function() { advance_slideshow(2); }, frame_speed);
	}
	
});

function schedule_next(image_num)
{
    window.setTimeout(function() { advance_slideshow(image_num); },frame_speed);
}

function advance_slideshow(image_num) 
{
	if( image_num == 1 )
    {
		for( y = 2 ; y < max_images ; ++y ) 
        {
			$(".header_" + y).hide();
		}
        
        $(".header_" + max_images).fadeOut(fade_speed,function(){ schedule_next(2); });
	}
	else 
    {
        var next_img_num;
		if( image_num >= max_images )
			next_img_num = 1;
		else
			next_img_num = image_num + 1;
        
		$(".header_" + image_num).fadeIn(fade_speed,function(){ schedule_next(next_img_num); });
	}
}

function convert_title_into_img(title) 
{
	img_name = title.replace("/", "_");
	img_name = img_name.replace(" ", "_");
	img_name = img_name.toLowerCase();
	img_name += ".jpg";
	document.write("<img src='http://fleurishllp.com/wp-content/themes/twentyten/images/bigcartel/" + img_name + "' width='705' eight='44px' />");
}

