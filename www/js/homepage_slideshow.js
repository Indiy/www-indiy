frame_speed = 4000;
fade_speed = 1300;
var max_images = 0;

$(document).ready(function(){
	
	$('.header_wrapper div').hide();
	$(".header_1").show();
	
	$('.header_wrapper div').each(function(index) {
		max_images = max_images + 1;
	});

	if ( $(".header_1").length>0) {
		$(".header_1").show();
		setTimeout("advance_slideshow(2)", frame_speed);
	}
	
});


function advance_slideshow(image_num) {
	//alert( image_num );
	var next_img_num;

	if( image_num == 1 ){
		for( y=2; y<max_images; ++y) {
			$(".header_" + y).hide();
		}
			$(".header_" + max_images).fadeOut(fade_speed);
		next_img_num = 2;
	}
	else {
		$(".header_" + image_num).fadeIn(fade_speed);
		if( image_num + 1 > max_images )
			next_img_num = 1;
		else
			next_img_num = image_num + 1;
	}

	setTimeout("advance_slideshow(" + next_img_num + ")", frame_speed);

}

/*
if( image_num > 1) {
		$(".header_1").fadeIn(fade_speed);
		for(x=2; x<=1; x++) {
			$(".header_" + x).delay(fade_speed + 200).show(0);
		}
		setTimeout("advance_slideshow(2)",frame_speed);
	}
	else {
		$(".header_0" + (image_num-1) ).fadeOut(fade_speed);
		setTimeout("advance_slideshow(" + (image_num+1) + ")",frame_speed);
	}
}*/

function convert_title_into_img(title) {
	img_name = title.replace("/", "_");
	img_name = img_name.replace(" ", "_");
	img_name = img_name.toLowerCase();
	img_name += ".jpg";
	document.write("<img src='http://fleurishllp.com/wp-content/themes/twentyten/images/bigcartel/" + img_name + "' width='705' eight='44px' />");
}