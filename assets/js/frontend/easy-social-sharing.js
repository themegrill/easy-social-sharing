/* global easy_social_sharing_params */
jQuery( document ).ready( function( $ ) {

	// Open Share Window
	$( document.body ).on( 'click', '.ess-social-share', function() {
		var top = ( $( window ).height()/2 ) - ( 450/2 ),
			left = ( $( window ).width()/2 ) - ( 550/2 ),
			new_window = window.open( $( this ).attr( 'href' ), '', 'scrollbars=1, height=450, width=550, top=' + top + ', left=' + left );

		if ( window.focus ) {
			new_window.focus();
		}

		return false;
	});

	// Update single shares
	$( document.body ).on( 'click', '.ess-social-share', function() {
		var $this_el = $( this );

		var data = {
			post_id: $this_el.data( 'post-id' ),
			network:   $this_el.data( 'social-name' ),
			page_url:  '' !== easy_social_sharing_params.page_url ? easy_social_sharing_params.page_url : window.location.href,
			action:    'easy_social_sharing_update_single_share',
			security:  easy_social_sharing_params.update_share_nonce
		};

		$.ajax({
			url: easy_social_sharing_params.ajax_url,
			data: data,
			type: 'POST',
			success: function( response ) {
				if ( response.success && response.data.counts ) {
					$this_el.find( 'span.ess-social-count' ).text( response.data.counts );
				}
			}
		});
	});

	// Shares Count
	$( document.body ).on( 'ess-init-shares-count', function() {
		$( '.ess-display-counts' ).each( function() {
			var $this_el = $( this );

			var data = {
				post_id:   $this_el.data( 'post-id' ),
				min_count: $this_el.data( 'min-count' ),
				network:   $this_el.data( 'social-name' ),
				page_url:  '' !== easy_social_sharing_params.page_url ? easy_social_sharing_params.page_url : window.location.href,
				action:    'easy_social_sharing_get_shares_count',
				security:  easy_social_sharing_params.shares_count_nonce
			};

			$.ajax({
				url: easy_social_sharing_params.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if ( response ) {
						$this_el.find( 'span.ess-social-count' ).text( response ).show();
					} else {
						$this_el.find( 'span.ess-social-count' ).remove();
					}
				}
			});
		});
	}).trigger( 'ess-init-shares-count' );

	// Total Share Counts
	$( document.body ).on( 'ess-init-total-share-counts', function() {
		$( '.ess-total-share' ).each( function() {
			var $this_el = $( this );

			var data = {
				post_id:   $this_el.data( 'post-id' ),
				page_url:  '' !== easy_social_sharing_params.page_url ? easy_social_sharing_params.page_url : window.location.href,
				action:    'easy_social_sharing_get_total_counts',
				security:  easy_social_sharing_params.total_counts_nonce
			};

			$.ajax({
				url: easy_social_sharing_params.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if ( response.success && response.data.totals ) {
						$this_el.find( 'span.ess-total-count' ).text( response.data.totals );
					}
				}
			});
		});
	}).trigger( 'ess-init-total-share-counts' );

	// Pop-Up Modal
	$( document.body ).on( 'ess-init-network-modal', function() {
		// All Social Networks.
		$( '.ess-all-networks' ).click( function() {
			$( 'body' ).toggleClass( 'ess-popup-enable' );
		});
		$( '.ess-popup-close, .ess-popup-overlay' ).click( function() {
			$( 'body' ).removeClass( 'ess-popup-enable' );
		});

		// Pinterest Image Picker.
		$( '.ess-social-share-pinterest' ).click( function( e ) {
			e.preventDefault();
			$( 'body' ).toggleClass( 'ess-pinterest-popup-enable' );
		});
		$( '.ess-pinterest-popup-close, .ess-pinterest-popup-overlay' ).click( function() {
			$( 'body' ).removeClass( 'ess-pinterest-popup-enable' );
		});
	}).trigger( 'ess-init-network-modal' );

	// Pinterest Image Picker.
	$( document.body ).on( 'ess-pinterest-image-picker', function() {

		if ( $( '.ess-social-pin-images' ).length && ( $( '.ess-all-networks' ).length || $( '.ess-social-share-pinterest' ).length ) ) {
			var pin_container = $( '.ess-social-pin-images' ),
				permalink = pin_container.data( 'permalink' ),
				title = pin_container.data( 'title' ),
				post_id = pin_container.data( 'post_id' ),
				$i = 0;

			$( 'img' ).each( function(){
				// Do not include comment avatar into the Modal
				if ( ! $( this ).hasClass( 'avatar' ) ) {
					var this_img = $( this ).attr( 'src' ),
						this_alt = $( this ).attr( 'alt' );

					if ( '' !== this_img ) {
						var	pin_link = 'http://www.pinterest.com/pin/create/button/?url=' + permalink + '&media=' + this_img + '&description=' + title,
							this_img_container = '<div class="ess-social-pin-image"><img src="' + this_img + '" alt="' + this_alt + '"/><a href="' + pin_link + '" rel="nofollow" class="ess-pinterest-tag ess-social-share" data-social_name="pinterest" data-post_id="' + post_id + '" data-social_type="share"><i class="socicon socicon-pinterest"></i></a></div>';
							$( '.ess-social-pin-images' ).append( this_img_container );
						$i++;
					}
				}
			});

			//Append error message if no images found on page
			if ( 0 === $i ) {
				$( '.ess-social-pin-images' ).append( '<div class="ess-no-pinterest-img-found">' + easy_social_sharing_params.i18n_no_img_message + '</div>' );
			}
		}

	}).trigger( 'ess-pinterest-image-picker' );

	// Sidebar Network - Open/close
	$('.ess-all-networks-toggle').click(function() {
        $('#ess-wrap-sidebar-networks').toggleClass('ess-sidebar-enable');
        $('.ess-all-networks-toggle i').toggleClass('fa-chevron-right');
    });

    $('.ess-right-layout .ess-all-networks-toggle').click(function() {
        $('.ess-all-networks-toggle i').toggleClass('fa-chevron-left');
    });

	//centering sidebar
    var top = ($('#ess-wrap-sidebar-networks').height() / 2);
    $('#ess-wrap-sidebar-networks').css("margin-top", -top);

    //
    $('.ess-popup-layout-close').click(function() {
        $('.ess-popup-layout-wrapper').addClass('ess-popup-layout-wrapper-disable');
    });


    //fly in
    var stickyNavTop = $('#header').offset().top;

    var stickyNav = function() {
        var scrollTop = $(window).scrollTop();

        if (scrollTop > stickyNavTop) {
            $('.ess-fly-layout-wrapper').addClass('fly-in');
        } else {
            $('.ess-fly-layout-wrapper').removeClass('fly-in');
        }
    };

    stickyNav();

    $(window).scroll(function() {
        stickyNav();
    });
});
