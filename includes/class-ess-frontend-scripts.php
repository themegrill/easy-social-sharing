<?php
/**
 * Handle frontend scripts.
 *
 * @class    ESS_Frontend_Scripts
 * @version  1.0.0
 * @package  EasySocialSharing/Classes
 * @category Class
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_Frontend_Scripts Class
 */
class ESS_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered by ESS.
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by ESS.
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by ESS.
	 * @var array
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hooks in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Get styles for the frontend.
	 * @access private
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'easy_social_sharing_enqueue_styles', array(
			'fontawesome'                 => array(
				'src'     => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css',
				'deps'    => '',
				'version' => ESS_VERSION,
				'media'   => 'all'
			),
			'easy-social-sharing-general' => array(
				'src'     => self::get_asset_url( 'assets/css/easy-social-sharing.css' ),
				'deps'    => '',
				'version' => ESS_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
			)
		) );
	}

	/**
	 * Return protocol relative asset URL.
	 *
	 * @param string $path
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'easy_social_sharing_get_asset_url', plugins_url( $path, ESS_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = ESS_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = ESS_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 * @param  boolean  $has_rtl
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = ESS_VERSION, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );

		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 * @param  boolean  $has_rtl
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = ESS_VERSION, $media = 'all', $has_rtl = false ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register/enqueue frontend scripts.
	 */
	public static function load_scripts() {
		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', ESS()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';

		// Register any scripts for later use, or used as dependencies
		self::register_script( 'jquery-tiptip', $assets_path . 'js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), '3.5.4' );
		self::register_script( 'jquery-idletimer', $assets_path . 'js/jquery-idletimer/idle-timer' . $suffix . '.js', array( 'jquery' ), '1.1.0' );

		// Global frontend scripts
		self::enqueue_script( 'easy-social-sharing', $frontend_script_path . 'easy-social-sharing' . $suffix . '.js', array(
			'jquery',
			'jquery-tiptip',
			'jquery-idletimer'
		) );

		// CSS Styles
		if ( $enqueue_styles = self::get_styles() ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}

		// Inline Styles
		if ( 'yes' == get_option( 'easy_social_sharing_custom_colors_enabled' ) ) {
			self::create_inline_styles();
		}
	}

	/**
	 * Enqueues front-end CSS for inline styles.
	 *
	 * @uses   wp_add_inline_style()
	 * @access private
	 *
	 * @param  string $default_color
	 */
	private static function create_inline_styles() {
		$bg       = get_option( 'easy_social_sharing_background_color' );
		$bg_hover = get_option( 'easy_social_sharing_hover_background_color' );

		// Darker background colors.
		$bg_darker        = ess_hex_darker( $bg, 20 );
		$bg_lighter       = ess_hex_lighter( $bg, 20 );
		$bg_hover_darker  = ess_hex_darker( $bg_hover, 20 );
		$bg_hover_lighter = ess_hex_lighter( $bg_hover, 20 );

		// Base text colors.
		$base_text       = ess_light_or_dark( $bg, '#202020', '#ffffff' );
		$base_text_hover = ess_light_or_dark( $bg_hover, '#202020', '#ffffff' );

		$inline_css = '
			#ess-wrap-sidebar-networks .socicon,
			#ess-wrap-inline-networks .ess-social-sharing,
			#ess-wrap-inline-networks.ess-inline-layout-two .socicon,
			#ess-wrap-inline-networks.ess-inline-layout-two .ess-social-count,
			#ess-wrap-inline-networks.ess-inline-layout-two .ess-more-networks {
				background: %1$s;
				color: %7$s !important;
			}

			#ess-wrap-sidebar-networks .socicon:hover,
			#ess-wrap-inline-networks .ess-social-sharing:hover,
			#ess-wrap-inline-networks.ess-inline-layout-two .ess-social-sharing:hover .socicon,
			#ess-wrap-inline-networks.ess-inline-layout-two .ess-social-sharing:hover .ess-social-count,
			#ess-wrap-inline-networks.ess-inline-layout-two .ess-social-sharing:hover .ess-more-networks {
				background: %2$s;
				color: %8$s !important;
			}

			#ess-wrap-inline-networks.ess-inline-layout-two .ess-social-sharing:hover .socicon,
			#ess-wrap-inline-networks.ess-inline-layout-two .ess-social-sharing:hover .ess-social-count,
			#ess-wrap-inline-networks.ess-inline-layout-two .ess-social-sharing:hover .ess-more-networks {
				color:#ffffff !important;
			}

			#ess-wrap-inline-networks.ess-inline-layout-one .socicon,
			#ess-wrap-inline-networks.ess-inline-layout-one .ess-social-count,
			#ess-wrap-inline-networks.ess-inline-layout-one .ess-more-networks,
			.ess-popup-wrapper .inline-networks {
				background: %3$s;
				color: %7$s !important;
			}

			#ess-wrap-inline-networks.ess-inline-layout-one .ess-social-sharing:hover .socicon,
			#ess-wrap-inline-networks.ess-inline-layout-one .ess-social-sharing:hover .ess-social-count {
				background: %4$s;
				color: %8$s !important;
			}

			#ess-wrap-inline-networks.ess-no-network-label .ess-social-sharing .ess-social-count {
				background: %5$s;
			}

			#ess-wrap-inline-networks.ess-no-network-label .ess-social-sharing:hover .ess-social-count {
				background: %6$s;
			}
		';

		wp_add_inline_style( 'easy-social-sharing-general', sprintf( $inline_css, esc_attr( $bg ), esc_attr( $bg_hover ), esc_attr( $bg_darker ), esc_attr( $bg_hover_darker ), esc_attr( $bg_lighter ), esc_attr( $bg_hover_lighter ), esc_attr( $base_text ), esc_attr( $base_text_hover ) ) );
	}

	/**
	 * Localize a ESS script once.
	 * @access private
	 * @since  2.3.0 this needs less wp_script_is() calls due to https://core.trac.wordpress.org/ticket/28404 being added in WP 4.0.
	 *
	 * @param  string $handle
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts ) && wp_script_is( $handle ) && ( $data = self::get_script_data( $handle ) ) ) {
			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles.
	 * @access private
	 *
	 * @param  string $handle
	 *
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {

		switch ( $handle ) {
			case 'easy-social-sharing' :
				return array(
					'ajax_url'                       => ESS()->ajax_url(),
					'page_url'                       => is_singular( get_post_types() ) ? get_permalink() : '',
					'update_share_nonce'             => wp_create_nonce( 'update-share' ),
					'shares_count_nonce'             => wp_create_nonce( 'shares-count' ),
					'all_network_shares_count_nonce' => wp_create_nonce( 'all-network-shares-count' ),
					'total_counts_nonce'             => wp_create_nonce( 'total-counts' ),
					'i18n_no_img_message'            => esc_attr__( 'No images found.', 'easy-social-sharing' ),
					'network_data'                   => self::get_ess_registered_networks_data()
				);
				break;
		}

		return false;
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}

	public static function get_ess_registered_networks_data() {

		global $wpdb;

		$network_data_object = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ess_social_networks" );

		$network_data = array();

		foreach ( $network_data_object as $network ) {

			$network_data[ $network->network_name ] = $network;

		}

		return ( $network_data );
	}
}

ESS_Frontend_Scripts::init();
