<script type="text/javascript"> 
$(document).ready(function(){
	
//Set default open/close settings
$('.list').hide(); //Hide/close all containers
$('.heading:first').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container
 
//On Click
$('.heading').hover(function(){
	if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
		$('.heading').removeClass('active').next().slideUp(); //Remove all .heading classes and slide up the immediate next container
		$(this).toggleClass('active').next().slideDown(); //Add .heading class to clicked trigger and slide down the immediate next container
	}
	return false; //Prevent the browser jump to the link anchor
});
 
});
</script>