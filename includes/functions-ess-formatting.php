<?php
/**
 * EasySocialSharing Formatting
 *
 * Functions for formatting data.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  EasySocialSharing/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 * @param  string|array $var
 * @return string|array
 */
function ess_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'ess_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  1.0.0  Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param  string $var
 * @return string
 */
function ess_sanitize_tooltip( $var ) {
	return htmlspecialchars( wp_kses( html_entity_decode( $var ), array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'small'  => array(),
		'span'   => array(),
		'ul'     => array(),
		'li'     => array(),
		'ol'     => array(),
		'p'      => array(),
	) ) );
}

/**
 * Format a full number into compat number.
 * @param  int    $full_number
 * @param  string $network
 * @return string
 */
function ess_format_compact_number( $full_number, $network = '' ) {
	$prefix = '';

	if ( 10000 == $full_number && 'googleplus' == $network ) {
		$prefix = '&gt';
	}

	if ( 1000000 <= $full_number ) {
		$full_number = floor( $full_number / 100000 ) / 10;
		$full_number .= esc_html_x( 'Mil', 'shortcut for the Million', 'easy-social-sharing' );
	} elseif ( 1000 < $full_number ) {
		$full_number = floor( $full_number / 100 ) / 10;
		$full_number .= esc_html_x( 'k', 'shortcut for the Thousands, i.e. 4k instead of 4000', 'easy-social-sharing' );
	}

	// Linkedin returns max 500 followers, so we need to add '+' sign if number is 500 and network is Linkedin
	if ( 500 === $full_number && 'linkedin' === $network ) {
		$full_number .= '+';
	}

	return $prefix . $full_number;
}

/**
 * Format a compact number into full number.
 * @param  float|string $compact_number
 * @return int
 */
function ess_format_full_number( $compact_number ) {
	// Support Google+ big numbers
	if ( false !== strrpos( $compact_number, '>9999' ) ) {
		$compact_number = 10000;
	}

	if ( false !== strrpos( $compact_number, esc_html_x( 'k', 'shortcut for the Thousands, i.e. 4k instead of 4000', 'easy-social-sharing' ) ) ) {
		$compact_number = floatval( str_replace( esc_html_x( 'k', 'shortcut for the Thousands, i.e. 4k instead of 4000', 'easy-social-sharing' ), '', $compact_number ) ) * 1000;
	}

	if ( false !== strrpos( $compact_number, esc_html_x( 'Mil', 'shortcut for the Million', 'easy-social-sharing' ) ) ) {
		$compact_number = floatval( str_replace( esc_html_x( 'Mil', 'shortcut for the Million', 'easy-social-sharing' ), '', $compact_number ) ) * 1000000;
	}

	return $compact_number;
}

if ( ! function_exists( 'ess_rgb_from_hex' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colours.
	 *
	 * @param  mixed $color
	 * @return string
	 */
	function ess_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb      = array();
		$rgb['R'] = hexdec( $color{0}.$color{1} );
		$rgb['G'] = hexdec( $color{2}.$color{3} );
		$rgb['B'] = hexdec( $color{4}.$color{5} );

		return $rgb;
	}
}

if ( ! function_exists( 'ess_hex_darker' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colours.
	 *
	 * @param  mixed $color
	 * @param  int   $factor (default: 30)
	 * @return string
	 */
	function ess_hex_darker( $color, $factor = 30 ) {
		$base  = ess_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = $v / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'ess_hex_lighter' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colours.
	 *
	 * @param  mixed $color
	 * @param  int   $factor (default: 30)
	 * @return string
	 */
	function ess_hex_lighter( $color, $factor = 30 ) {
		$base  = ess_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = 255 - $v;
			$amount      = $amount / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'ess_light_or_dark' ) ) {

	/**
	 * Detect if we should use a light or dark colour on a background colour.
	 *
	 * @param  mixed  $color
	 * @param  string $dark (default: '#000000')
	 * @param  string $light (default: '#FFFFFF')
	 * @return string
	 */
	function ess_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {

		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155 ? $dark : $light;
	}
}

if ( ! function_exists( 'ess_format_hex' ) ) {

	/**
	 * Format string as hex.
	 *
	 * @param  string $hex
	 * @return string
	 */
	function ess_format_hex( $hex ) {

		$hex = trim( str_replace( '#', '', $hex ) );

		if ( strlen( $hex ) == 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : null;
	}
}
