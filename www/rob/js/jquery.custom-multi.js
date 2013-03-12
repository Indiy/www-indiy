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
	}).navigator() .autoscroll({ interval: 3000 });
	
	$( '#slide-scroller2 .items .item' ).live( 'hover', function() {
		$( this ).toggleClass( 'hover' );
	}, function() {
		$( this ).removeClass( 'hover' );
	});


	$( '#slide-scroll2' ).scrollable({
		circular: true,
		next: '.nextPage2',
		prev: '.prevPage2'
	}).navigator();
	
	$( '#slide-scroller3 .items .item' ).live( 'hover', function() {
		$( this ).toggleClass( 'hover' );
	}, function() {
		$( this ).removeClass( 'hover' );
	});


	$( '#slide-scroll3' ).scrollable({
		circular: true,
		next: '.nextPage3',
		prev: '.prevPage3'
	}).navigator() .autoscroll({ interval: 3000 });

	$( '.prevPage, .nextPage' ).click(function() { return false; } );
	$( '.prevPage2, .nextPage2' ).click(function() { return false; } );
	$( '.prevPage3, .nextPage3' ).click(function() { return false; } );
});


