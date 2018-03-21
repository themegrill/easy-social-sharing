<?php
/**
 * Plugin Name: Easy Social Sharing
 * Plugin URI: http://themegrill.com/plugins/easy-social-sharing
 * Description: Easy Social Sharing provides you with an easy way to display various popular social share buttons.
 * Version: 1.3.1
 * Author: ThemeGrill
 * Author URI: https://themegrill.com
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * Text Domain: easy-social-sharing
 * Domain Path: /languages/
 * @package  EasySocialSharing
 * @category Core
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'EasySocialSharing' ) ) :

	/**
	 * Main EasySocialSharing Class.
	 *
	 * @class   EasySocialSharing
	 * @version 1.2.0
	 */
	final class EasySocialSharing {

		/**
		 * EasySocialSharing version.
		 *
		 * @var string
		 */
		public $version = '1.3.1';

		/**
		 * The single instance of the class.
		 *
		 * @var EasySocialSharing
		 */
		protected static $_instance = null;

		/**
		 * Main EasySocialSharing Instance.
		 *
		 * Ensure only one instance of EasySocialSharing is loaded or can be loaded.
		 *
		 * @static
		 * @see    ESS()
		 * @return EasySocialSharing - Main instance.
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 * @since 1.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'easy-social-sharing' ), '1.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 * @since 1.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'easy-social-sharing' ), '1.0' );
		}

		/**
		 * EasySocialSharing Constructor.
		 */
		private function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'easy_social_sharing_loaded' );
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'ESS_Install', 'install' ) );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		}

		/**
		 * Define ESS Constants.
		 */
		private function define_constants() {
			$this->define( 'ESS_PLUGIN_FILE', __FILE__ );
			$this->define( 'ESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'ESS_VERSION', $this->version );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string      $name
		 * @param string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin or frontend.
		 *
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Check if the device is mobile?
		 * @return bool
		 */
		private function is_mobile() {
			if ( 'yes' !== get_option( 'easy_social_sharing_handheld_enable' ) && wp_is_mobile() ) {
				return true;
			}

			return false;
		}

		/**
		 * Includes the required core files used in admin and on the frontend.
		 */
		private function includes() {
			include_once( 'includes/functions-ess-core.php' );
			include_once( 'includes/class-ess-autoloader.php' );
			include_once( 'includes/class-ess-install.php' );
			include_once( 'includes/class-ess-ajax.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'includes/admin/class-ess-admin.php' );
			}

			if ( $this->is_request( 'frontend' ) && ! $this->is_mobile() ) {
				$this->frontend_includes();
			}
		}

		/**
		 * Include required frontend files.
		 */
		public function frontend_includes() {
			include_once( 'includes/class-ess-frontend-scripts.php' );  // Frontend Scripts
			include_once( 'includes/class-ess-social-networks.php' );   // Social Networks class
			include_once( 'includes/class-ess-share-handler.php' );     // Social Share Handler class
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Locales found in:
		 *      - WP_LANG_DIR/easy-social-sharing/easy-social-sharing-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/easy-social-sharing-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'easy-social-sharing' );

			load_textdomain( 'easy-social-sharing', WP_LANG_DIR . '/easy-social-sharing/easy-social-sharing-' . $locale . '.mo' );
			load_plugin_textdomain( 'easy-social-sharing', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get Ajax URL.
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}
	}

endif;

if ( ! function_exists( 'ESS' ) ) {

	/**
	 * Main instance of EasySocialSharing.
	 *
	 * Returns the main instance of ESS to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return EasySocialSharing
	 */
	function ESS() {
		return EasySocialSharing::get_instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['easy_social_sharing'] = ESS();
}
