jQuery( '#enable_disable' ).on( 'change', function () {
    if ( this.value == 'yes' ) {
            jQuery( '.description' ).hide();
        } else {
            jQuery( '.description' ).show();
        }
} );
