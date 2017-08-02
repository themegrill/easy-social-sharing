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
				'title'    => __( 'Specific Locations', 'easy-social-sharing' ),
				'desc'     => __( 'This option lets you limit which location placements you are willing to display to.', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_allowed_locations',
				'type'     => 'multiselect',
				'class'    => 'ess-enhanced-select',
				'css'      => 'width: 450px;',
				'default'  => array( 'inline', 'sidebar' ),
				'desc_tip' => true,
				'options'  => ess_get_allowed_screen_locations(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Choose Locations&hellip;', 'easy-social-sharing' )
				)
			),

			array(
				'title'    => __( 'Custom Screen(s)', 'easy-social-sharing' ),
				'desc'     => __( 'This option lets you limit which screens you are willing to display to.', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_allowed_screens',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'ess-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' => true,
				'options'  => array(
					'all'        => __( 'Display on all screens', 'easy-social-sharing' ),
					'all_except' => __( 'Display on all screens, except for&hellip;', 'easy-social-sharing' ),
					'specific'   => __( 'Display on specific screens', 'easy-social-sharing' )
				)
			),

			array(
				'title'   => __( 'Display On All Screens, Except For&hellip;', 'easy-social-sharing' ),
				'desc'    => '',
				'id'      => 'easy_social_sharing_all_except_screens',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_screens'
			),

			array(
				'title'   => __( 'Display On Specific Screens', 'easy-social-sharing' ),
				'desc'    => '',
				'id'      => 'easy_social_sharing_specific_allowed_screens',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_screens'
			),

			array(
				'title'    => __( 'Front Page Display', 'easy-social-sharing' ),
				'desc'     => __( 'Enable share icons on front page display', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_front_page_enable',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'autoload' => false
			),

			array(
				'title'    => __( 'Mobile Behaviours', 'easy-social-sharing' ),
				'desc'     => __( 'Enable share on mobile or handheld devices', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_handheld_enable',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'autoload' => false
			),

			array(
				'title'    => __( 'Reset WordPress Loop', 'easy-social-sharing' ),
				'desc'     => __( 'Enable to reset the post data after a custom query', 'easy-social-sharing' ),
				'id'       => 'easy_social_sharing_reset_postdata',
				'default'  => 'no',
				'type'     => 'checkbox',
				'desc_tip' =>  __( 'Enable the option if the plugin does not detect permalinks properly.', 'easy-social-sharing' ),
				'autoload' => false
			),

			array(
				'type' => 'sectionend',
				'id'   => 'general_options'
			)

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
