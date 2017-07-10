/**
 * EasySocialSharing Admin JS
 */
jQuery(function ( $ ) {

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

	// Checkbox availability
	$( '.show_if_checked' ).change(function () {
		if ( $( this ).is( ':checked') ) {
			$( this ).closest( 'tr' ).next( 'tr' ).show();
		} else {
			$( this ).closest( 'tr' ).next( 'tr' ).hide();
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


	// Show Setting preview popup
	$( '.easy-social-sharing .ess-enhanced-select' ).change( function() {
		var select = $( this ),
			data_class = select.find( 'option:selected' ).attr( 'data-class' );

		$.each( select.find( 'option' ), function () {
			var data_class_new = $( this ).attr('data-class');

			if ( data_class_new !== 'undefined' && data_class_new !== undefined ) {
				$( '#ess-main-wrapper .ess-preview-icon-container' ).removeClass( data_class_new );
			}
		});


		if ( 'undefined' !== data_class && undefined !== data_class ) {
			$( '#ess-main-wrapper .ess-preview-icon-container' ).addClass( data_class );
		}
	}).change();
});
