( function( $ ) {
	wp.customize( 'h1_color', function( value ) {
		value.bind( function( newval ) {
			$( 'h1, .entry-content h1' ).css( 'color', newval );
		} );
	} );

	wp.customize( 'h2_color', function( value ) {
		value.bind( function( newval ) {
			$( 'h2, .entry-content h2' ).css( 'color', newval );
		} );
	} );

	wp.customize( 'h3_color', function( value ) {
		value.bind( function( newval ) {
			$( 'h3, .entry-content h3' ).css( 'color', newval );
		} );
	} );

	wp.customize( 'h4_color', function( value ) {
		value.bind( function( newval ) {
			$( 'h4, .entry-content h4' ).css( 'color', newval );
		} );
	} );

	wp.customize( 'h5_color', function( value ) {
		value.bind( function( newval ) {
			$( 'h5, .entry-content h5' ).css( 'color', newval );
		} );
	} );

} )( jQuery );