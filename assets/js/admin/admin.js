/**
 * EasySocialSharing Admin JS
 */
jQuery( function ( $ ) {

	// Tooltips
	$( document.body ).on( 'init_tooltips', function() {
		var tiptip_args = {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		};

		$( '.tips, .help_tip, .easy-social-sharing-help-tip' ).tipTip( tiptip_args );

	}).trigger( 'init_tooltips' );

	// Select availability
	$( 'select.availability' ).change( function() {
		if ( $( this ).val() === 'all' ) {
			$( this ).closest( 'tr' ).next( 'tr' ).hide();
		} else {
			$( this ).closest( 'tr' ).next( 'tr' ).show();
		}
	}).change();

	// Hidden options
	$( '.hide_options_if_checked' ).each( function() {
		$( this ).find( 'input:eq(0)' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).hide();
			} else {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).show();
			}
		}).change();
	});

	$( '.show_options_if_checked' ).each( function() {
		$( this ).find( 'input:eq(0)' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).show();
			} else {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).hide();
			}
		}).change();
	});
});
