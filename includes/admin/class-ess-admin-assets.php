<?php
/**
 * EasySocialSharing Admin Assets
 *
 * Load Admin Assets.
 *
 * @class    ESS_Admin_Assets
 * @version  1.0.0
 * @package  EasySocialSharing/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_Admin_Assets Class
 */
class ESS_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts;

		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// Register admin styles
		wp_register_style( 'easy-social-sharing-menu', ESS()->plugin_url() . '/assets/css/menu.css', array(), ESS_VERSION );
		wp_register_style( 'easy-social-sharing-admin', ESS()->plugin_url() . '/assets/css/admin.css', array(), ESS_VERSION );
		wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

		// Add RTL support for admin styles
		wp_style_add_data( 'easy-social-sharing-menu', 'rtl', 'replace' );
		wp_style_add_data( 'easy-social-sharing-admin', 'rtl', 'replace' );

		// Sitewide menu CSS
		wp_enqueue_style( 'easy-social-sharing-menu' );

		// Admin styles for ESS pages only
		if ( in_array( $screen_id, ess_get_screen_ids() ) || in_array( $screen_id, ess_get_allowed_screen_types() ) ) {
			wp_enqueue_style( 'easy-social-sharing-admin' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register Scripts
		wp_register_script( 'easy-social-sharing-admin', ESS()->plugin_url() . '/assets/js/admin/admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), ESS_VERSION );
		wp_register_script( 'jquery-blockui', ESS()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'jquery-tiptip', ESS()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), ESS_VERSION, true );
		wp_register_script( 'ess-admin-meta-boxes', ESS()->plugin_url() . '/assets/js/admin/metaboxes' . $suffix . '.js', array( 'jquery' ), ESS_VERSION );
		wp_register_script( 'ess-social-networks', ESS()->plugin_url() . '/assets/js/admin/ess-social-networks' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'ess-enhanced-select' ), ESS_VERSION );
		wp_register_script( 'select2', ESS()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '3.5.4' );
		wp_register_script( 'ess-enhanced-select', ESS()->plugin_url() . '/assets/js/admin/enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), ESS_VERSION );
		wp_localize_script( 'ess-enhanced-select', 'ess_enhanced_select_params', array(
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'easy-social-sharing' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'easy-social-sharing' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'easy-social-sharing' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'easy-social-sharing' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'easy-social-sharing' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'easy-social-sharing' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'easy-social-sharing' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'easy-social-sharing' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'easy-social-sharing' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'easy-social-sharing' )
		) );

		// EasySocialSharing admin pages
		if ( in_array( $screen_id, ess_get_screen_ids() ) ) {
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'easy-social-sharing-admin' );
			wp_enqueue_script( 'ess-enhanced-select' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			$params = array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			);

			wp_localize_script( 'easy-social-sharing-admin', 'easy_social_sharing_admin', $params );
		}

		// Meta boxes
		if ( in_array( $screen_id, ess_get_allowed_screen_types() ) ) {
			wp_enqueue_script( 'ess-enhanced-select' );
			wp_enqueue_script( 'ess-admin-meta-boxes' );
		}
	}
}

new ESS_Admin_Assets();
