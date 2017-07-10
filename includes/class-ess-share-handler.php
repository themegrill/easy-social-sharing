<?php
/**
 * Handle social sharing.
 *
 * @class    ESS_Share_Handler
 * @version  1.0.0
 * @package  EasySocialSharing/Classes
 * @category Class
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_Share_Handler Class
 */
class ESS_Share_Handler {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_templates' ) );
		add_action( 'wp_footer', array( $this, 'load_modal_tmpl' ) );
	}

	/**
	 * Load Frontend templates.
	 */
	public function load_templates() {
		$locations = get_option( 'easy_social_sharing_allowed_locations', array() );

		// Reset huh?
		$this->reset_postdata();

		if ( ! empty( $locations ) ) {
			foreach ( $locations as $location ) {
				if ( is_callable( array( $this, 'display_' . $location ) ) ) {
					if ( 'inline' == $location ) {
						add_filter( 'the_content', array( $this, 'display_inline' ) );
						add_action( 'woocommerce_after_single_product_summary', array( $this, 'display_on_wc_page' ) );
					} else {
						add_action( 'wp_footer', array( $this, "display_{$location}" ) );
					}
				}
			}
		}
	}

	/**
	 * Reset Post Data.
	 */
	private function reset_postdata() {
		if ( 'yes' == get_option( 'easy_social_sharing_reset_postdata' ) ) {
			wp_reset_postdata();
		}
	}

	/**
	 * Check for capability.
	 * @return bool
	 */
	private function check_capability( $location ) {
		$post_types         = ess_get_allowed_screen_types();
		$front_page         = get_option( 'easy_social_sharing_front_page_enable' );
		$disabled_locations = get_post_meta( get_the_ID(), '_ess_location_disabled', true );

		if ( $disabled_locations ) {
			if ( ! in_array( $location, $disabled_locations ) && ! ( 'inline' == $location && is_singular( 'product' ) ) ) {
				return true;
			}
		} else {
			if ( is_front_page() ) {
				if ( ( 'yes' == $front_page && 'inline' !== $location ) || ( is_page() && 'yes' == $front_page && 'inline' == $location ) ) {
					return true;
				}
			} else {
				if ( ! empty( $post_types ) && is_singular( $post_types ) && ! ( 'inline' == $location && is_singular( 'product' ) ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Output Inline Layout.
	 * @param $mixed $content
	 */
	public function display_inline( $content ) {
		$location = get_option( 'easy_social_sharing_inline_icons_location', 'above' );

		if ( $this->check_capability( 'inline' ) ) {
			$content = sprintf( '%1$s%2$s%3$s', ( 'above' == $location || 'both' == $location ) ? $this->generate_inline_icons( 'ess-inline-top' ) : '', $content, ( 'below' == $location || 'both' == $location ) ? $this->generate_inline_icons( 'ess-inline-bottom' ) : '' );
		}

		return $content;
	}

	/**
	 * Output on WC Page.
	 */
	public function display_on_wc_page() {
		if ( in_array( 'product', ess_get_allowed_screen_types() ) ) {
			echo $this->generate_inline_icons();
		}
	}

	/**
	 * Output Sidebar Layout.
	 */
	public function display_sidebar() {
		$network_desc     = ESS_Social_Networks::get_network_desc();
		$network_count    = ESS_Social_Networks::get_network_count();
		$allowed_networks = ESS_Social_Networks::get_allowed_networks();

		if ( $allowed_networks && $this->check_capability( 'sidebar' ) ) {
			include( 'views/html-view-layout-sidebar.php' );
		}
	}

	/**
	 * Generate Inline Icons.
	 */
	public function generate_inline_icons( $class = 'ess-inline-top' ) {
		ob_start();

		$network_desc     = ESS_Social_Networks::get_network_desc();
		$network_count    = ESS_Social_Networks::get_network_count();
		$allowed_networks = ESS_Social_Networks::get_allowed_networks();

		if ( $allowed_networks ) {
			include( 'views/html-view-layout-inline.php' );
		}

		return ob_get_clean();
	}

	/**
	 * Load Modal Template.
	 */
	public function load_modal_tmpl( $location = 'inline' ) {
		$all_inline_networks  = 'yes' == get_option( 'easy_social_sharing_inline_enable_all_networks' );
		$all_sidebar_networks = 'yes' == get_option( 'easy_social_sharing_sidebar_enable_all_networks' );

		if (
			$all_inline_networks && $this->check_capability( 'inline' ) ||
			$all_sidebar_networks && $this->check_capability( 'sidebar' )
		) {
			include( 'views/html-view-tmpl-modal-sharing.php' );
		}

		// Pinterest images picker.
		include( 'views/html-view-tmpl-modal-picker.php' );
	}
}

new ESS_Share_Handler();
