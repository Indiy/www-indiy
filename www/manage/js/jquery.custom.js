jQuery(document).ready(function($) {

	$( '#slide-scroller .items .item' ).live( 'hover', function() {
		$( this ).toggleClass( 'hover' );
	}, function() {
		$( this ).removeClass( 'hover' );
	});


	$( '#slide-scroll' ).scrollable({
		circular: true,
		next: '.nextPage',
		prev: '.prevPage'
	}).navigator();
	

	$( '.prevPage, .nextPage' ).click(function() { return false; } );
});


