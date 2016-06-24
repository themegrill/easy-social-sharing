<?php
/**
 * EasySocialSharing Network Settings
 *
 * @class    ESS_Settings_Network
 * @version  1.0.0
 * @package  EasySocialSharing/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ESS_Settings_Network' ) ) :

/**
 * ESS_Settings_Network Class
 */
class ESS_Settings_Network extends ESS_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'network';
		$this->label = __( 'Network', 'easy-social-sharing' );

		add_filter( 'easy_social_sharing_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'easy_social_sharing_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'easy_social_sharing_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'easy_social_sharing_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''        => __( 'Social Networks', 'easy-social-sharing' ),
			'options' => __( 'Social Options', 'easy-social-sharing' ),
		);

		return apply_filters( 'easy_social_sharing_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'easy_social_sharing_network_settings', array(

			array( 'title' => __( 'Social Options', 'easy-social-sharing' ), 'type' => 'title', 'desc' => __( 'These credentials need to be set to access social network related functionality.', 'easy-social-sharing' ), 'id' => 'social_options' ),

			array(
				'title'       => __( 'Twitter Username', 'easy-social-sharing' ),
				'desc'        => __( 'Enter twitter username used while sharing.', 'easy-social-sharing' ),
				'id'          => 'easy_social_sharing_twitter_username',
				'type'        => 'text',
				'css'         => 'min-width:350px;',
				'placeholder' => __( 'Username', 'easy-social-sharing' ),
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true,
			),

			array(
				'title'    => __( 'Facebook App ID', 'easy-social-sharing' ),
				'desc'     => __( 'Enter your App ID provided by facebook.', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_facebook_client_id',
				'type'     => 'text',
				'css'      => 'width: 350px;',
				'default'  => '',
				'autoload' => false,
				'desc_tip' => true
			),

			array(
				'title'    => __( 'Facebook App Secret', 'easy-social-sharing' ),
				'desc'     => __( 'Enter your App Secret provided by facebook.', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_facebook_client_secret',
				'type'     => 'text',
				'css'      => 'width: 350px;',
				'default'  => '',
				'autoload' => false,
				'desc_tip' => true
			),

			array(
				'title'    => __( 'API Supported Networks', 'easy-social-sharing' ),
				'desc'     => __( 'Enable to display share only for API supported networks', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_api_support_networks_only',
				'default'  => 'no',
				'type'     => 'checkbox',
				'desc_tip' =>  __( 'Enable the option to provide support for API supported networks only.', 'easy-social-sharing' ),
				'autoload' => false
			),

			array( 'type' => 'sectionend', 'id' => 'social_options' ),

		) );

		return apply_filters( 'easy_social_sharing_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section, $hide_save_button;

		if ( '' === $current_section ) {
			$hide_save_button = true;
			$this->output_networks_screen();
		} elseif ( 'options' === $current_section ) {
			$settings = $this->get_settings();
			ESS_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		switch ( $current_section ) {
			case 'options' :
				ESS_Admin_Settings::save_fields( $this->get_settings() );
			break;
			case '' :
			break;
		}
	}

	/**
	 * Handles output of the social networks page in admin.
	 */
	protected function output_networks_screen() {
		wp_localize_script( 'ess-social-networks', 'socialNetworksLocalizeScript', array(
			'networks'      => ESS_Social_Networks::get_networks(),
			'default_social_network'  => array(
				'network_id'    => 0,
				'network_name'  => 'facebook',
				'network_desc'  => '',
				'network_count' => 0,
				'network_order' => null,
			),
			'ess_social_networks_nonce'  => wp_create_nonce( 'ess_social_networks_nonce' ),
			'strings'       => array(
				'unload_confirmation_msg' => __( 'Your changed data will be lost if you leave this page without saving.', 'easy-social-sharing' ),
				'save_failed'             => __( 'Your changes were not saved. Please retry.', 'easy-social-sharing' ),
				'yes'                     => __( 'Yes', 'easy-social-sharing' ),
				'no'                      => __( 'No', 'easy-social-sharing' ),
			),
		) );
		wp_enqueue_script( 'ess-social-networks' );

		include_once( 'views/html-admin-page-social-networks.php' );
	}
}

endif;

return new ESS_Settings_Network();
