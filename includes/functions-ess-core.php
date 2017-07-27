<?php
/**
 * EasySocialSharing Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  EasySocialSharing/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include core functions (available in both admin and frontend).
include( 'functions-ess-sharing.php' );
include( 'functions-ess-formatting.php' );

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function ess_enqueue_js( $code ) {
	global $ess_queued_js;

	if ( empty( $ess_queued_js ) ) {
		$ess_queued_js = '';
	}

	$ess_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function ess_print_js() {
	global $ess_queued_js;

	if ( ! empty( $ess_queued_js ) ) {
		// Sanitize.
		$ess_queued_js = wp_check_invalid_utf8( $ess_queued_js );
		$ess_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $ess_queued_js );
		$ess_queued_js = str_replace( "\r", '', $ess_queued_js );

		$js = "<!-- EasySocialSharing JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $ess_queued_js });\n</script>\n";

		/**
		 * easy_social_sharing_queued_js filter.
		 *
		 * @param string $js JavaScript code.
		 */
		echo apply_filters( 'easy_social_sharing_queued_js', $js );

		unset( $ess_queued_js );
	}
}

/**
 * Get all Custom Post Types Screen.
 * @return array
 */
function ess_get_screen_types() {
	global $wp_post_types;

	$post_types   = get_post_types( array( 'public' => true, 'show_in_menu' => true, '_builtin' => false ), 'names' );
	$screen_types = apply_filters( 'easy_social_sharing_screens_types', array(
		'post' => __( 'Posts', 'easy-social-sharing' ),
		'page' => __( 'Pages', 'easy-social-sharing' )
	) );

	// Fetch Public Custom Post Types.
	foreach ( $post_types as $post_type ) {
		$screen_types[ $post_type ] = $wp_post_types[ $post_type ]->labels->menu_name;
	}

	// Sort screens.
	if ( apply_filters( 'easy_social_sharing_sort_screens', true ) ) {
		asort( $screen_types );
	}

	return $screen_types;
}

/**
 * Get allowed specific Custom Post Types Screen.
 * @return array
 */
function ess_get_allowed_screen_types() {
	$screen_types = ess_get_screen_types();

	if ( 'all' === get_option( 'easy_social_sharing_allowed_screens' ) ) {
		return array_keys( $screen_types );
	}

	if ( 'all_except' === get_option( 'easy_social_sharing_allowed_screens' ) ) {
		$except_screens = get_option( 'easy_social_sharing_all_except_screens', array() );

		if ( ! $except_screens ) {
			return array_keys( $screen_types );
		} else {
			$all_except_screens = $screen_types;
			foreach ( $except_screens as $screen ) {
				unset( $all_except_screens[ $screen ] );
			}

			return apply_filters( 'easy_social_sharing_allowed_screen_types', array_keys( $all_except_screens ) );
		}
	}

	$screens     = array();
	$raw_screens = get_option( 'easy_social_sharing_specific_allowed_screens' );

	if ( $raw_screens ) {
		foreach ( $raw_screens as $key => $screen ) {
			$screens[ $key ] = $screen;
		}
	}

	return apply_filters( 'easy_social_sharing_allowed_screen_types', $screens );
}

/**
 * Get allowed specific screen locations.
 * @return array
 */
function ess_get_allowed_screen_locations() {
	return (array) apply_filters( 'easy_social_sharing_allowed_screen_locations', array(
		'inline'  => __( 'Inline', 'easy-social-sharing' ),
		'sidebar' => __( 'Sidebar', 'easy-social-sharing' )
	) );
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 *
 * @return bool
 */
function ess_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

/**
 * EasySocialSharing Core Supported Social Networks.
 * @return array
 */
function ess_get_core_supported_social_networks() {
	return apply_filters( 'easy_social_sharing_core_supported_social_networks', array(
		'facebook'    => __( 'Facebook', 'easy-social-sharing' ),
		'twitter'     => __( 'Twitter', 'easy-social-sharing' ),
		'googleplus'  => __( 'Google+', 'easy-social-sharing' ),
		'linkedin'    => __( 'LinkedIn', 'easy-social-sharing' ),
		'pinterest'   => __( 'Pinterest', 'easy-social-sharing' ),
		'stumbleupon' => __( 'StumbleUpon', 'easy-social-sharing' ),
		'tumblr'      => __( 'Tumblr', 'easy-social-sharing' ),
		'blogger'     => __( 'Blogger', 'easy-social-sharing' ),
		'myspace'     => __( 'Myspace', 'easy-social-sharing' ),
		'delicious'   => __( 'Delicious', 'easy-social-sharing' ),
		// 'printfriendly' => __( 'Print Friendly', 'easy-social-sharing' ),
		'yahoomail'   => __( 'Yahoo Mail', 'easy-social-sharing' ),
		'gmail'       => __( 'Gmail', 'easy-social-sharing' ),
		// 'aol'           => __( 'AOL', 'easy-social-sharing' ),
		'newsvine'    => __( 'Newsvine', 'easy-social-sharing' ),
		// 'hackernews'    => __( 'Hacker News', 'easy-social-sharing' ),
		// 'evernote'      => __( 'Evernote', 'easy-social-sharing' ),
		'digg'        => __( 'Digg', 'easy-social-sharing' ),
		// 'livejournal'   => __( 'LiveJournal', 'easy-social-sharing' ),
		'friendfeed'  => __( 'FriendFeed', 'easy-social-sharing' ),
		'buffer'      => __( 'Buffer', 'easy-social-sharing' ),
		'reddit'      => __( 'Reddit', 'easy-social-sharing' ),
		'vkontakte'   => __( 'VKontakte', 'easy-social-sharing' ),
	) );
}

/**
 * Social share networks with their configuration.
 * @return array
 */
function ess_get_core_supported_social_networks_config() {
	$all_networks              = ess_get_core_supported_social_networks();
	$all_network_configuration = array();

	foreach ( $all_networks as $network_key => $network_value ) {
		$all_network_configuration[ $network_key ] = array(
			"label"                => $network_value,
			"data"                 => "",
			"backgroundColor"      => "",
			"hoverBackgroundColor" => "",
			"hoverBorderWidth"     => 1,
			"hoverBorderColor"     => "lightgrey",
		);
	}

	$all_network_configuration['facebook']['backgroundColor']         = "#2F72CD";
	$all_network_configuration['facebook']['hoverBackgroundColor']    = "#4E84C0";
	$all_network_configuration['twitter']['backgroundColor']          = "#50C6F8";
	$all_network_configuration['twitter']['hoverBackgroundColor']     = "#4DA7DE";
	$all_network_configuration['googleplus']['backgroundColor']       = "#F55F46";
	$all_network_configuration['googleplus']['hoverBackgroundColor']  = "#DD4B39";
	$all_network_configuration['linkedin']['backgroundColor']         = "#129FE4";
	$all_network_configuration['linkedin']['hoverBackgroundColor']    = "#3371B7";
	$all_network_configuration['pinterest']['backgroundColor']        = "#EC661C";
	$all_network_configuration['pinterest']['hoverBackgroundColor']   = "#C92619";
	$all_network_configuration['stumbleupon']['backgroundColor']      = "#EA4B24";
	$all_network_configuration['stumbleupon']['hoverBackgroundColor'] = "#E64011";
	$all_network_configuration['tumblr']['backgroundColor']           = "#444343";
	$all_network_configuration['tumblr']['hoverBackgroundColor']      = "#45556C";
	$all_network_configuration['blogger']['backgroundColor']          = "#EC661C";
	$all_network_configuration['blogger']['hoverBackgroundColor']     = "#ef651a";
	$all_network_configuration['myspace']['backgroundColor']          = "#323232";
	$all_network_configuration['myspace']['hoverBackgroundColor']     = "#323232";
	$all_network_configuration['delicious']['backgroundColor']        = "#444343";
	$all_network_configuration['delicious']['hoverBackgroundColor']   = "#020202";
	$all_network_configuration['yahoomail']['backgroundColor']        = "#511295";
	$all_network_configuration['yahoomail']['hoverBackgroundColor']   = "#3D0975";
	$all_network_configuration['gmail']['backgroundColor']            = "#DD4B39";
	$all_network_configuration['gmail']['hoverBackgroundColor']       = "#BF3222";
	$all_network_configuration['newsvine']['backgroundColor']         = "#075B2F";
	$all_network_configuration['newsvine']['hoverBackgroundColor']    = "#075B2F";
	$all_network_configuration['digg']['backgroundColor']             = "#1D1D1B";
	$all_network_configuration['digg']['hoverBackgroundColor']        = "#1D1D1B";
	$all_network_configuration['friendfeed']['backgroundColor']       = "#2F72C4";
	$all_network_configuration['friendfeed']['hoverBackgroundColor']  = "#2F72C4";
	$all_network_configuration['buffer']['backgroundColor']           = "#333333";
	$all_network_configuration['buffer']['hoverBackgroundColor']      = "#000000";
	$all_network_configuration['reddit']['backgroundColor']           = "#FF4500";
	$all_network_configuration['reddit']['hoverBackgroundColor']      = "#E74A1E";
	$all_network_configuration['vkontakte']['backgroundColor']        = "#4C75A3";
	$all_network_configuration['vkontakte']['hoverBackgroundColor']   = "#92AAC4";

	return $all_network_configuration;
}

/**
 * Social share networks with API support.
 * @return array
 */
function ess_get_share_networks_with_api_support() {
	return array( 'facebook', 'linkedin', 'pinterest', 'googleplus', 'stumbleupon', 'vkontakte', 'reddit', 'buffer' );
}

/**
 * Display a EasySocialSharing help tip.
 *
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 *
 * @return string
 */
function ess_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = ess_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="easy-social-sharing-help-tip" data-tip="' . $tip . '"></span>';
}


/**
 * Return html attribute from array.
 * @param  array
 * @return string
 */
function ess_array_to_html_attribute( $attribute_array = array() ) {
	$attribute_string = ' ';

	if ( count( $attribute_array ) > 1 ) {
		return $attribute_string;
	}

	foreach ( $attribute_array as $attribute_key => $attribute_value ) {
		if ( 'string' === gettype( $attribute_value ) ) {
			$attribute_string .= $attribute_key . '="' . $attribute_value . '" ';
		}
	}

	return $attribute_string;
}

/**
 * Return default networks.
 * @return array
 */
function ess_get_default_networks() {
	return apply_filters( 'easy_social_sharing_default_social_networks', array(
		'facebook',
		'twitter',
		'linkedin',
		'googleplus',
		'stumbleupon',
		'pinterest',
	) );
}
