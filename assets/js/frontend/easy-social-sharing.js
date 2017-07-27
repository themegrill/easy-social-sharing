/* global easy_social_sharing_params */
jQuery(document).ready(function ( $ ) {
	// Run tipTip
	function runTipTip () {
		// Remove any lingering tooltips
		$('#tiptip_holder').removeAttr('style');
		$('#tiptip_arrow').removeAttr('style');
		$('.socicon').tipTip({
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		});
	}

	runTipTip();
	// Open Share Window
	$(document.body).on('click', '.ess-social-share', function () {
		var top = $(window).height() / 2 - 450 / 2, left = $(window).width() / 2 - 550 / 2,
			new_window = window.open($(this).attr('href'), '', 'scrollbars=1, height=450, width=550, top=' + top + ', left=' + left);
		if ( window.focus ) {
			new_window.focus();
		}
		return false;
	});
	// Update single shares
	$(document.body).on('click', '.ess-social-share', function () {
		var $this_el = $(this);
		var data = {
			post_id: $this_el.data('post-id'),
			network: $this_el.data('social-name'),
			page_url: '' !== easy_social_sharing_params.page_url ? easy_social_sharing_params.page_url : window.location.href,
			action: 'easy_social_sharing_update_single_share',
			security: easy_social_sharing_params.update_share_nonce,
			location: $this_el.closest('li').find('a').attr('data-location')
		};
		$.ajax({
			url: easy_social_sharing_params.ajax_url,
			data: data,
			type: 'POST',
			success: function ( response ) {
				if ( response.success && response.data.counts ) {
					$this_el.find('span.ess-social-count').text(get_network_data($this_el.data('social-name'), response.data.counts));
				}
			}
		});
	});
	// Shares Count
	$(document.body).on('ess-init-shares-count', function () {
		var data = {
			page_url: '' !== easy_social_sharing_params.page_url ? easy_social_sharing_params.page_url : window.location.href,
			action: 'easy_social_sharing_get_all_network_shares_count',
			security: easy_social_sharing_params.all_network_shares_count_nonce,
			post_id: $('.ess-total-share').eq(0).attr('data-post-id')
		};
		$.ajax({
			url: easy_social_sharing_params.ajax_url,
			data: data,
			type: 'POST',
			success: function ( response ) {
				setFlyInNetwork(response);
				setInlineNetwork(response);
				setChoosenNetwork(response);
				setAvailableNetworks(response);
				setSidebarNetwork(response);
			}
		});
	}).trigger('ess-init-shares-count');
	function setAvailableNetworks ( response ) {
		$.each($('.ess-popup-share-wrapper').find('ul.ess-available-networks').find('li'), function () {
			var network_name = $(this).find('a').attr('data-social-name');
			if ( typeof response[ network_name ] !== 'undefined' ) {
				$(this).find('.ess-social-count').html(get_network_data(network_name, (response[ network_name ])));
			}
		});
	}

	function setChoosenNetwork ( response ) {
		$.each($('.ess-popup-block-wrapper').find('ul.ess-choosen-networks').find('li'), function () {
			var network_name = $(this).find('a').attr('data-social-name');
			if ( typeof response[ network_name ] !== 'undefined' ) {
				$(this).find('.ess-social-count').html(get_network_data(network_name, (response[ network_name ])));
			}
		});
	}

	function setFlyInNetwork ( response ) {
		$.each($('.ess-fly-share-wrapper').find('ul.ess-clear').find('li'), function () {
			var network_name = $(this).find('a').attr('data-social-name');
			if ( typeof response[ network_name ] !== 'undefined' ) {
				$(this).find('.ess-social-count').html(get_network_data(network_name, (response[ network_name ])));
			}
		});
	}

	function setInlineNetwork ( response ) {
		var total_share = 0;

		$.each($('#ess-wrap-inline-networks').find('ul.ess-social-network-lists').find('li'), function () {
			var network_name = $(this).find('a').attr('data-social-name');


			if ( typeof response[ network_name ] !== 'undefined' ) {
				total_share += parseInt(response[ network_name ], 0);
				$(this).find('.ess-social-count').html(get_network_data(network_name, (response[ network_name ])));
			}
		});

		$('.ess-inline-networks-container').find('.ess-total-share').find('.ess-total-count').html(formatNumber(total_share));

	}

	function setSidebarNetwork ( response ) {
		var total_share = 0;
		$.each($('.ess-sidebar-icon-count-wrapper').find('ul.ess-social-network-lists').find('li'), function () {
			var network_name = $(this).find('a').attr('data-social-name');
			if ( typeof response[ network_name ] !== 'undefined' ) {
				total_share += parseInt(response[ network_name ], 0);
				$(this).find('.ess-social-count').html(get_network_data(network_name, (response[ network_name ])));
			}
		});
		$('.ess-sidebar-icon-count-wrapper').find('.ess-total-share').find('.ess-total-count').html(formatNumber(total_share));
	}

	// Pop-Up Modal
	$(document.body).on('ess-init-network-modal', function () {
		// All Social Networks.
		$('.ess-popup-close, .ess-popup-overlay').click(function () {
			$('body').removeClass('ess-popup-enable');
		});
		$('.ess-all-networks').filter(':not(.enhanced)').click(function () {
			$('body').toggleClass('ess-popup-enable');
		});
		// Pinterest Image Picker.
		$('.ess-social-share-pinterest').click(function ( e ) {
			e.preventDefault();
			$('body').toggleClass('ess-pinterest-popup-enable');
		});
		$('.ess-pinterest-popup-close, .ess-pinterest-popup-overlay').click(function () {
			$('body').removeClass('ess-pinterest-popup-enable');
		});
	}).trigger('ess-init-network-modal');
	// Pinterest Image Picker.
	$(document.body).on('ess-pinterest-image-picker', function () {
		if ( $('.ess-social-pin-images').length && ($('.ess-all-networks').length || $('.ess-social-share-pinterest').length) ) {
			var pin_container = $('.ess-social-pin-images'), permalink = pin_container.data('permalink'),
				title = pin_container.data('title'), post_id = pin_container.data('post_id'), $i = 0;
			$('img').each(function () {
				// Do not include comment avatar into the Modal
				if ( !$(this).hasClass('avatar') ) {
					var this_img = $(this).attr('src'), this_alt = $(this).attr('alt');
					if ( '' !== this_img ) {
						var pin_link = 'http://www.pinterest.com/pin/create/button/?url=' + permalink + '&media=' + this_img + '&description=' + title,
							this_img_container = '<div class="ess-social-pin-image"><img src="' + this_img + '" alt="' + this_alt + '"/><a href="' + pin_link + '" rel="nofollow" class="ess-pinterest-tag ess-social-share" data-social_name="pinterest" data-post_id="' + post_id + '" data-social_type="share"><i class="socicon socicon-pinterest"></i></a></div>';
						$('.ess-social-pin-images').append(this_img_container);
						$i++;
					}
				}
			});
			// Append error message if no images found on page
			if ( 0 === $i ) {
				$('.ess-social-pin-images').append('<div class="ess-no-pinterest-img-found">' + easy_social_sharing_params.i18n_no_img_message + '</div>');
			}
		}
	}).trigger('ess-pinterest-image-picker');
	// Centering Sidebar Networks
	var top = $('#ess-wrap-sidebar-networks').height() / 2;
	$('#ess-wrap-sidebar-networks').css('margin-top', -top);
	// Sidebar Network - Open/close
	$('.ess-all-networks-toggle').click(function () {
		$('#ess-wrap-sidebar-networks').toggleClass('ess-sidebar-enable');
		$('.ess-all-networks-toggle i').toggleClass('fa-chevron-right');
	});
	$('.ess-right-layout .ess-all-networks-toggle i').removeClass('fa-chevron-left');
	$('.ess-right-layout .ess-all-networks-toggle i').addClass('fa-chevron-right');
	$('.ess-right-layout .ess-all-networks-toggle').click(function () {
		$('.ess-all-networks-toggle i').toggleClass('fa-chevron-left');
	});
	// Mobile Bottom Share
	$('.ess-mobile-share-toggle').click(function () {
		$(this).parent().toggleClass('ess-mobile-share-enable');
		$('.ess-mobile-share-overlay').toggleClass('active');
	});
	$('.ess-mobile-share-toggle .ess-close-mob-share').click(function () {
		$('.ess-mobile-bottom-share').hide();
		$('.ess-mobile-share-collection').toggleClass('active');
	});
	$('.ess-mobile-share-collection').click(function () {
		$('.ess-mobile-bottom-share').show();
		$(this).removeClass('active');
	});
	// Close and Remove Pop-Up Modal
	$(document.body).on('ess-close-popup-modal', function () {
		$('.ess-popup-layout-wrapper').removeClass('ess-social-visible');
		setTimeout(function () {
			$('.ess-popup-layout-wrapper').remove();
		}, 200);
		return false;
	});
	$(document.body).on('keydown', function ( e ) {
		var button = e.keyCode || e.which;
		// ESC key
		if ( 27 === button ) {
			$(document.body).trigger('ess-close-popup-modal');
		}
	});
	$('.ess-popup-layout-close, .ess-popup-layout-wrapper .ess-popup-layout-overlay').click(function () {
		$(document.body).trigger('ess-close-popup-modal');
	});
	// Close and Remove Fly-In Modal
	$('.ess-fly-layout-close').click(function () {
		$('#ess-wrap-fly-networks').removeClass('ess-social-visible');
		setTimeout(function () {
			$('#ess-wrap-fly-networks').remove();
		}, 1000);
	});
	// Display all networks within popup layout.
	$('.ess-all-networks--popup').click(function () {
		$('.ess-choosen-networks').hide();
		$('.ess-available-networks').show();
	});
	/**
	 * Pop-Up Visibility.
	 */
	function auto_popup ( this_el, delay, is_idle_timer ) {
		var $current_popup = this_el;
		if ( !$current_popup.hasClass('ess-social-animated') ) {
			var cookie_duration = $current_popup.data('cookie_duration') ? $current_popup.data('cookie_duration') : false;
			if ( false !== cookie_duration && !checkCookieValue('essCookie', 'true') || false === cookie_duration ) {
				var idle_timeout = '' !== $current_popup.data('idle_timeout') ? $current_popup.data('idle_timeout') * 1000 : 30000;
				// Check for idle timer status?
				if ( true === is_idle_timer ) {
					$(document.body).idleTimer(idle_timeout).on('idle.idleTimer', function () {
						make_popup_visible($current_popup, 0);
					});
				} else {
					make_popup_visible($current_popup, delay);
				}
				if ( false !== cookie_duration ) {
					set_cookie(cookie_duration);
				}
			}
		}
	}

	function make_popup_visible ( $popup, $delay ) {
		setTimeout(function () {
			$popup.addClass('ess-social-visible ess-social-animated');
		}, $delay);
	}

	function get_url_paramater ( param_name ) {
		var page_url = window.location.search.substring(1), url_variables = page_url.split('&');
		for ( var i = 0; i < url_variables.length; i++ ) {
			var curr_param_name = url_variables[ i ].split('=');
			if ( curr_param_name[ 0 ] === param_name ) {
				return curr_param_name[ 1 ];
			}
		}
	}

	$('.ess-social-auto-popup').each(function () {
		var $current_popup = $(this);
		auto_popup($current_popup, '' !== $current_popup.data('delay') ? $current_popup.data('delay') * 1000 : 0, false);
	});
	$('.ess-social-trigger-idle').each(function () {
		var $current_popup = $(this);
		auto_popup($current_popup, 0, true);
	});
	if ( $('.ess-trigger-after-order').length ) {
		$('.ess-social-after-purchase').each(function () {
			var $current_popup = $(this);
			auto_popup($current_popup, 0, false);
		});
	}
	if ( 'true' === get_url_paramater('ess_popup') ) {
		$('.ess-social-after-comment').each(function () {
			var $current_popup = $(this);
			auto_popup($current_popup, 0, false);
		});
	}
	/**
	 * Scroll trigger.
	 */
	function scroll_trigger ( this_el, is_bottom_trigger ) {
		var scroll_trigger, current_popup_bottom = this_el;
		if ( !current_popup_bottom.hasClass('ess-social-animated') ) {
			var page_scroll_position = this_el.data('scroll_position') > 100 ? 100 : this_el.data('scroll_position'),
				cookies_expire_bottom = current_popup_bottom.data('cookie_duration') ? current_popup_bottom.data('cookie_duration') : false;
			// Check if bottom scroll trigger?
			if ( true === is_bottom_trigger ) {
				scroll_trigger = $('.ess-social-bottom-trigger').length ? $('.ess-social-bottom-trigger').offset().top : $(document).height() - 500;
			} else {
				scroll_trigger = 100 === page_scroll_position ? $(document).height() - 10 : $(document).height() * page_scroll_position / 100;
			}
		}
		$(window).scroll(function () {
			if ( false !== cookies_expire_bottom && !checkCookieValue('essCookie', 'true') || false === cookies_expire_bottom ) {
				if ( $(window).scrollTop() + $(window).height() > scroll_trigger ) {
					current_popup_bottom.addClass('ess-social-visible ess-social-animated');
					if ( false !== cookies_expire_bottom ) {
						set_cookie(cookies_expire_bottom);
					}
				}
			}
		});
	}

	if ( $('.ess-social-bottom-trigger').length ) {
		$('.ess-social-bottom-trigger').each(function () {
			scroll_trigger($(this), true);
		});
	}
	if ( $('.ess-social-trigger-scroll').length ) {
		$('.ess-social-trigger-scroll').each(function () {
			scroll_trigger($(this), false);
		});
	}
	/**
	 * Cookie related functions.
	 */
	function parseCookies () {
		var cookies = document.cookie.split('; ');
		var ret = {};
		for ( var i = cookies.length - 1; i >= 0; i-- ) {
			var el = cookies[ i ].split('=');
			ret[ el[ 0 ] ] = el[ 1 ];
		}
		return ret;
	}

	function setCookieExpire ( days ) {
		var ms = days * 24 * 60 * 60 * 1000;
		var date = new Date();
		date.setTime(date.getTime() + ms);
		return '; expires=' + date.toUTCString();
	}

	function checkCookieValue ( cookieName, value ) {
		return parseCookies()[ cookieName ] === value;
	}

	function set_cookie ( $expire ) {
		var cookieExpire = setCookieExpire($expire);
		document.cookie = 'essCookie=true' + cookieExpire;
	}
});
(function ( $ ) {
	$.fn.textTooltip = function () {
		'use strict';
		return this.each(function () {
			var $this = this;
			var tooltipClass = 'ess-text-tooltip';
			var toolTip = {
				init: function () {
					$($this).bind('mouseup', function ( e ) {
						var selection;
						if ( window.getSelection ) {
							selection = window.getSelection();
						} else if ( document.selection ) {
							selection = document.selection.createRange();
						}
						if ( selection.toString() !== '' ) {
							toolTip.tooltip(e, selection);
						}  //selection.toString() !== '' && alert('"' + selection.toString() + '" was selected at ' + e.pageX + '/' + e.pageY);
					});
				},
				tooltip: function ( e, selection ) {
					toolTip.renderHtml(e, selection);
				},
				renderHtml: function ( e, selection ) {
					var toolTipContainner = $(e.target).parent();
					if ( $('body').find('.' + tooltipClass).length > 0 ) {
						$('body').find('.' + tooltipClass).remove();
					}
					var toolTipMinWidth = toolTip.getToolTipMinWidth(e, selection);
					var toolTipMinHeight = toolTip.getToolTipMinHeight(e, selection);
					var toolTipPositionTop = toolTip.getToolTipPositionTop(e, selection);
					var toolTipPositionLeft = toolTip.getToolTipPositionLeft(e, selection);
					var toolTipNode = $('<div class="' + tooltipClass + '"/>');
					toolTipNode.css({
						'min-height': toolTipMinHeight + 'px',
						'min-width': toolTipMinWidth + 'px',
						'background': 'red',
						'position': 'fixed',
						'top': toolTipPositionTop + 'px',
						'left': toolTipPositionLeft + 'px',
						'z-index': '1000000'
					});
					toolTipContainner.append(toolTipNode);
				},
				getToolTipPositionTop: function ( e ) {
					return e.pageY;
				},
				getToolTipPositionLeft: function ( e ) {
					return e.pageX;
				},
				getToolTipMinHeight: function () {
					return '400';
				},
				getToolTipMinWidth: function () {
					return '400';
				},
				getToolTipArrowPosition: function () {
				}
			};
			toolTip.init();
		});
	};
}(jQuery));
function get_network_data ( network_name, total_count ) {
	var network_data = easy_social_sharing_params.network_data;
	if ( network_data[ network_name ] !== undefined && network_data[ network_name ] !== 'undefined' ) {
		total_count = typeof total_count === 'string' ? parseInt(total_count, 0) : total_count;
		var network_count_number = typeof network_data[ network_name ].network_count === 'string' ? parseInt(network_data[ network_name ].network_count, 0) : network_data[ network_name ].network_count;
		if ( network_count_number < total_count ) {
			return formatNumber(total_count);
		}
	}
	return '';
}
function formatNumber ( value ) {
	value = parseInt(value, 0);
	var suffixes = [
		'',
		'K',
		'M',
		'B',
		'T'
	];
	if ( 1000 > value ) {

		return value;

	} else if ( 1000000 > value ) {

		value = parseFloat((value / 1000).toFixed(2), 2);

		return value + suffixes[ 1 ];

	} else if ( 1000000000 > value ) {

		value = parseFloat((value / 1000000).toFixed(2), 2);

		return value + suffixes[ 2 ];

	} else if ( 1000000000 > value ) {

		value = parseFloat((value / 1000000000).toFixed(2), 2);

		return value + suffixes[ 3 ];

	} else if ( 1000000000000 > value ) {

		value = parseFloat((value / 1000000000).toFixed(2), 2);

		return value + suffixes[ 4 ];

	}
	value = parseFloat((value / 1000000000).toFixed(2), 2);

	return value + suffixes[ 4 ];

}
