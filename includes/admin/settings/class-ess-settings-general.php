<?php
/**
 * EasySocialSharing General Settings
 *
 * @class    ESS_Settings_General
 * @version  1.0.0
 * @package  EasySocialSharing/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ESS_Settings_General' ) ) :

/**
 * ESS_Settings_General Class
 */
class ESS_Settings_General extends ESS_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'general';
		$this->label = __( 'General', 'easy-social-sharing' );

		add_filter( 'easy_social_sharing_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'easy_social_sharing_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'easy_social_sharing_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters( 'easy_social_sharing_general_settings', array(

			array(
				'title' => __( 'General Options', 'easy-social-sharing' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'general_options'
			),

			array(
				'title'    => __( 'Specific Location(s)', 'easy-social-sharing' ),
				'desc'     => __( 'This option lets you limit which location placements you are willing to display to.', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_allowed_locations',
				'type'     => 'multiselect',
				'class'    => 'ess-enhanced-select',
				'css'      => 'width: 450px;',
				'default'  => '',
				'desc_tip' => true,
				'options'  => array(
					'inline'  => __( 'Inline', 'easy-social-sharing' ),
					'sidebar' => __( 'Sidebar', 'easy-social-sharing' )
				),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Choose Locations&hellip;', 'easy-social-sharing' )
				)
			),

			array(
				'title'    => __( 'Reset WordPress Loop', 'easy-social-sharing' ),
				'desc'     => __( 'Enable to reset post data', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_reset_postdata',
				'default'  => 'no',
				'type'     => 'checkbox',
				'desc_tip' =>  __( 'Enable the option if the plugin does not detect permalinks properly.', 'easy-social-sharing' ),
				'autoload' => false
			),

			array(
				'type' => 'sectionend',
				'id'   => 'general_options'
			),

			array( 'title' => __( 'Social Network Options', 'easy-social-sharing' ), 'type' => 'title', 'id' => 'social_network_options' ),

			array(
				'title'    => __( 'Social Network(s)', 'easy-social-sharing' ),
				'desc'     => __( 'This option lets you limit which social networks you are willing to display on frontend.', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_allowed_networks',
				'type'     => 'multiselect',
				'class'    => 'ess-enhanced-select',
				'css'      => 'width: 450px;',
				'default'  => '',
				'desc_tip' => true,
				'options'  => ess_get_core_supported_social_networks(),
				'custom_attributes' => array(
					'data-max_selection' => 10,
					'data-placeholder'   => __( 'Choose social networks&hellip;', 'easy-social-sharing' )
				)
			),

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
				'desc'     => __( 'Enter your App ID provided by facebook.', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_facebook_client_secret',
				'type'     => 'password',
				'css'      => 'width: 350px;',
				'default'  => '',
				'autoload' => false,
				'desc_tip' => true
			),

			array( 'type' => 'sectionend', 'id' => 'social_network_options' )

		) );

		return apply_filters( 'easy_social_sharing_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		ESS_Admin_Settings::save_fields( $settings );
	}
}

endif;

return new ESS_Settings_General();
