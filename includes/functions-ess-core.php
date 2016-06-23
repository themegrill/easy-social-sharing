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
		'post' => __( 'Post', 'easy-social-sharing' ),
		'page' => __( 'Page', 'easy-social-sharing' )
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
			foreach( $except_screens as $screen ) {
				unset( $all_except_screens[ $screen ] );
			}
			return apply_filters( 'easy_social_sharing_allowed_screen_types', array_keys( $all_except_screens ) );
		}
	}

	$screens    = array();
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
		'facebook'      => __( 'Facebook', 'easy-social-sharing' ),
		'twitter'       => __( 'Twitter', 'easy-social-sharing' ),
		'googleplus'    => __( 'Google+', 'easy-social-sharing' ),
		'linkedin'      => __( 'LinkedIn', 'easy-social-sharing' ),
		'pinterest'     => __( 'Pinterest', 'easy-social-sharing' ),
		'stumbleupon'   => __( 'StumbleUpon', 'easy-social-sharing' ),
		'tumblr'        => __( 'Tumblr', 'easy-social-sharing' ),
		'blogger'       => __( 'Blogger', 'easy-social-sharing' ),
		'myspace'       => __( 'Myspace', 'easy-social-sharing' ),
		'delicious'     => __( 'Delicious', 'easy-social-sharing' ),
		// 'printfriendly' => __( 'Print Friendly', 'easy-social-sharing' ),
		'yahoomail'     => __( 'Yahoo Mail', 'easy-social-sharing' ),
		'gmail'         => __( 'Gmail', 'easy-social-sharing' ),
		// 'aol'           => __( 'AOL', 'easy-social-sharing' ),
		'newsvine'      => __( 'Newsvine', 'easy-social-sharing' ),
		// 'hackernews'    => __( 'Hacker News', 'easy-social-sharing' ),
		// 'evernote'      => __( 'Evernote', 'easy-social-sharing' ),
		'digg'          => __( 'Digg', 'easy-social-sharing' ),
		// 'livejournal'   => __( 'LiveJournal', 'easy-social-sharing' ),
		'friendfeed'    => __( 'FriendFeed', 'easy-social-sharing' ),
		'buffer'        => __( 'Buffer', 'easy-social-sharing' ),
		'reddit'        => __( 'Reddit', 'easy-social-sharing' ),
		'vkontakte'     => __( 'VKontakte', 'easy-social-sharing' ),
	) );
}

/**
 * Display a EasySocialSharing help tip.
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
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
