<?php
/**
 * EasySocialSharing Admin.
 *
 * @class    ESS_Admin
 * @version  1.0.0
 * @package  EasySocialSharing/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_Admin Class
 */
class ESS_Admin {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'fb_access_token' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ) );
		add_action( 'admin_footer', 'ess_print_js', 25 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Check if is Network Options settings page.
	 * @return bool
	 */
	private function is_network_options_settings_page() {
		return isset( $_GET['page'] )
			&& 'easy-social-sharing' == $_GET['page']
			&& isset( $_GET['tab'] )
			&& 'network' == $_GET['tab']
			&& isset( $_GET['section'] )
			&& 'options' == $_GET['section'];
	}

	/**
	 * Includes any classes we need within admin.
	 */
	public function includes() {
		include_once( 'functions-ess-admin.php' );
		include_once( 'functions-ess-meta-box.php' );
		include_once( 'class-ess-admin-notices.php' );
		include_once( 'class-ess-admin-assets.php' );
		include_once( 'class-ess-admin-meta-boxes.php' );
	}

	/**
	 * Add plugin settings menu item.
	 */
	public function settings_menu() {
		add_options_page( __( 'Easy Social Sharing Settings', 'easy-social-sharing' ),  __( 'Easy Social Sharing', 'easy-social-sharing' ) , 'manage_options', 'easy-social-sharing', array( $this, 'settings_page' ) );
	}

	/**
	 * Init the settings page.
	 */
	public function settings_page() {
		ESS_Admin_Settings::output();
	}

	/**
	 * Gets a facebook access token.
	 */
	public function fb_access_token() {
		if ( $this->is_network_options_settings_page() && current_user_can( 'manage_options' ) ) {
			$client_id     = get_option( 'easy_social_sharing_facebook_client_id' );
			$client_secret = get_option( 'easy_social_sharing_facebook_client_secret' );

			// Check for autorization code.
			if ( ! get_option( 'easy_social_sharing_facebook_access_token' ) && $client_id && $client_secret ) {
				$request = wp_remote_post( 'https://graph.facebook.com/v2.4/oauth/access_token', array(
					'method'  => 'POST',
					'timeout' => 30,
					'body'    => array (
						'client_id'     => $client_id,
						'client_secret' => $client_secret,
						'grant_type'    => 'client_credentials'
					)
				) );

				if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) == 200 ) {
					$response = json_decode( wp_remote_retrieve_body( $request ) );

					// Update facebook access token.
					if ( isset( $response->access_token ) ) {
						update_option( 'easy_social_sharing_facebook_access_token', ess_clean( $response->access_token ) );
					}
				} else {
					add_action( 'admin_notices', array( $this, 'access_token_error' ) );
				}
			}
		}
	}

	/**
	 * Error shown if the facebook token is missing.
	 */
	public function access_token_error() {
		echo '<div class="error"><p>' . __( 'Facebook Access Token Error: Please ensure your facebook credentials are correct.', 'easy-social-sharing' ) . '</p></div>';
	}

	/**
	 * Change the admin footer text on EasySocialSharing admin pages.
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$current_screen = get_current_screen();
		$ess_pages      = ess_get_screen_ids();

		// Check to make sure we're on a Social Sharing admin page
		if ( isset( $current_screen->id ) && apply_filters( 'easy_social_sharing_display_admin_footer_text', in_array( $current_screen->id, $ess_pages ) ) ) {
			// Change the footer text
			if ( ! get_option( 'easy_social_sharing_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>Easy Social Sharing</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thanks in advance!', 'easy-social-sharing' ), '<a href="https://wordpress.org/support/view/plugin-reviews/easy-social-sharing?filter=5#postform" target="_blank" class="ess-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'easy-social-sharing' ) . '">', '</a>' );
				ess_enqueue_js( "
					jQuery( 'a.ess-rating-link' ).click( function() {
						jQuery.post( '" . ESS()->ajax_url() . "', { action: 'easy_social_sharing_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
				" );
			} else {
				$footer_text = __( 'Thank you for sharing with Easy Social Sharing.', 'easy-social-sharing' );
			}
		}

		return $footer_text;
	}
}

new ESS_Admin();
