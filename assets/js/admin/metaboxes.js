jQuery(document).ready(function() {   
    if ( jQuery( '#disable_ess' ).is(':checked') ) {
            jQuery( '.ess_metabox_description' ).hide();
        }
    
    jQuery( '#disable_ess' ).on( 'change', function () {
        if ( jQuery( '#disable_ess' ).is(':checked') ) {
            jQuery( '.ess_metabox_description' ).hide();
        }
        else{
            jQuery( '.ess_metabox_description' ).show();
        }
    });
});
