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
	}).navigator();
	
	$( '#slide-scroller4 .items .item' ).live( 'hover', function() {
		$( this ).toggleClass( 'hover' );
	}, function() {
		$( this ).removeClass( 'hover' );
	});


	$( '#slide-scroll4' ).scrollable({
		circular: true,
		next: '.nextPage4',
		prev: '.prevPage4'
	}).navigator();
	
	$( '#slide-scroller5 .items .item' ).live( 'hover', function() {
		$( this ).toggleClass( 'hover' );
	}, function() {
		$( this ).removeClass( 'hover' );
	});


	$( '#slide-scroll5' ).scrollable({
		circular: true,
		next: '.nextPage5',
		prev: '.prevPage5'
	}).navigator();

	$( '.prevPage, .nextPage' ).click(function() { return false; } );
	$( '.prevPage2, .nextPage2' ).click(function() { return false; } );
	$( '.prevPage3, .nextPage3' ).click(function() { return false; } );
	$( '.prevPage4, .nextPage4' ).click(function() { return false; } );
	$( '.prevPage5, .nextPage5' ).click(function() { return false; } );
});


