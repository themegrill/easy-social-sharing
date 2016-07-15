<?php
/**
 * EasySocialSharing Layouts Settings
 *
 * @class    ESS_Settings_Layouts
 * @version  1.0.0
 * @package  EasySocialSharing/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ESS_Settings_Layouts' ) ) :

/**
 * ESS_Settings_Layouts Class
 */
class ESS_Settings_Layouts extends ESS_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'layouts';
		$this->label = __( 'Layouts', 'easy-social-sharing' );

		add_filter( 'easy_social_sharing_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'easy_social_sharing_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'easy_social_sharing_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'easy_social_sharing_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get sections.
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''        => __( 'Layout Options', 'easy-social-sharing' ),
			'inline'  => __( 'Inline Layout', 'easy-social-sharing' ),
			'sidebar' => __( 'Sidebar Layout', 'easy-social-sharing' )
		);

		return apply_filters( 'easy_social_sharing_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );

		ESS_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		ESS_Admin_Settings::save_fields( $settings );
	}

	/**
	 * Get settings array.
	 * @param  string $current_section
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		if ( 'inline' == $current_section ) {

			$settings = apply_filters( 'easy_social_sharing_layouts_inline_settings', array(

				array( 'title' => __( 'Style Options', 'easy-social-sharing' ), 'type' => 'title', 'id' => 'layouts_inline_style_options' ),

				array(
					'title'    => __( 'Icon Shape', 'easy-social-sharing' ),
					'desc'     => __( 'This controls which icon shape is shown for this layout.', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_inline_icon_shape',
					'class'    => 'ess-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'square',
					'type'     => 'select',
					'desc_tip' => true,
					'options'  => array(
						'square'  => __( 'Square', 'easy-social-sharing' ),
						'rounded' => __( 'Rounded', 'easy-social-sharing' )
					)
				),

				array(
					'title'    => __( 'Choose Layout', 'easy-social-sharing' ),
					'desc'     => __( 'This option lets you choose the desire layouts.', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_inline_layouts',
					'class'    => 'ess-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'one',
					'type'     => 'select',
					'desc_tip' => true,
					'options'  => array(
						'one' => __( 'Layout One', 'easy-social-sharing' ),
						'two' => __( 'Layout Two', 'easy-social-sharing' ),
					)
				),

				array(
					'title'    => __( 'Choose Location', 'easy-social-sharing' ),
					'desc'     => __( 'This option lets you choose the icons location.', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_inline_icons_location',
					'class'    => 'ess-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'above',
					'type'     => 'select',
					'desc_tip' => true,
					'options'  => array(
						'above' => __( 'Above content', 'easy-social-sharing' ),
						'below' => __( 'Below content', 'easy-social-sharing' ),
						'both'  => __( 'Above &amp; below content', 'easy-social-sharing' )
					)
				),

				array( 'type' => 'sectionend', 'id' => 'layouts_inline_style_options' ),

				array( 'title' => __( 'Share Options', 'easy-social-sharing' ), 'type' => 'title', 'id' => 'layout_share_options' ),

				array(
					'title'         => __( 'Share Visibility', 'easy-social-sharing' ),
					'desc'          => __( 'Enable networks label', 'easy-social-sharing' ),
					'desc_tip'      => __( 'Allows users to view specific social networks label.', 'easy-social-sharing' ),
					'id'            => 'easy_social_sharing_inline_enable_networks_label',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'autoload'      => false
				),

				array(
					'desc'          => __( 'Enable social share counts', 'easy-social-sharing' ),
					'desc_tip'      => __( 'Allows users to view specific social network share counts.', 'easy-social-sharing' ),
					'id'            => 'easy_social_sharing_inline_enable_share_counts',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
					'autoload'      => false,
				),

				array(
					'title'    => __( 'Show All Networks', 'easy-social-sharing' ),
					'desc'     => __( 'Show "All Networks" Button', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_inline_enable_all_networks',
					'default'  => 'yes',
					'type'     => 'checkbox',
					'desc_tip' =>  __( 'Allows users to select and share from all available social networks.', 'easy-social-sharing' ),
					'autoload' => false
				),

				array( 'type' => 'sectionend', 'id' => 'layout_share_options' ),

			) );

		} elseif ( 'sidebar' == $current_section ) {

			$settings = apply_filters( 'easy_social_sharing_layouts_sidebar_settings', array(

				array( 'title' => __( 'Style Options', 'easy-social-sharing' ), 'type' => 'title', 'id' => 'layouts_sidebar_style_options' ),

				array(
					'title'    => __( 'Icon Shape', 'easy-social-sharing' ),
					'desc'     => __( 'This controls which icon shape is shown for this layout.', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_sidebar_icon_shape',
					'class'    => 'ess-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'square',
					'type'     => 'select',
					'desc_tip' => true,
					'options'  => array(
						'square'  => __( 'Square', 'easy-social-sharing' ),
						'rounded' => __( 'Rounded', 'easy-social-sharing' )
					)
				),

				array(
					'title'    => __( 'Layout Orientation', 'easy-social-sharing' ),
					'desc'     => __( 'This option lets you choose the layout position.', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_sidebar_layout_orientation',
					'class'    => 'ess-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'left',
					'type'     => 'select',
					'desc_tip' => true,
					'options'  => array(
						'left'  => __( 'Left', 'easy-social-sharing' ),
						'right' => __( 'Right', 'easy-social-sharing' )
					)
				),

				array( 'type' => 'sectionend', 'id' => 'layouts_sidebar_style_options' ),

				array( 'title' => __( 'Share Options', 'easy-social-sharing' ), 'type' => 'title', 'id' => 'layout_share_options' ),

				array(
					'title'         => __( 'Share Visibility', 'easy-social-sharing' ),
					'desc'          => __( 'Enable social share counts', 'easy-social-sharing' ),
					'desc_tip'      => __( 'Allows users to view specific social network share counts.', 'easy-social-sharing' ),
					'id'            => 'easy_social_sharing_sidebar_enable_share_counts',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'autoload'      => false
				),

				array(
					'desc'          => __( 'Display total shares counter', 'easy-social-sharing' ),
					'id'            => 'easy_social_sharing_sidebar_enable_total_shares',
					'desc_tip'      => __( 'Allows users to view total shares performed in all social networks.', 'easy-social-sharing' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
					'autoload'      => false,
				),

				array(
					'title'    => __( 'Show All Networks', 'easy-social-sharing' ),
					'desc'     => __( 'Show "All Networks" Button', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_sidebar_enable_all_networks',
					'default'  => 'yes',
					'type'     => 'checkbox',
					'desc_tip' =>  __( 'Allows users to select and share from all available social networks.', 'easy-social-sharing' ),
					'autoload' => false
				),

				array( 'type' => 'sectionend', 'id' => 'layout_share_options' ),

			) );

		} else {

			$settings = apply_filters( 'easy_social_sharing_layouts_general_settings', array(

				array( 'title' => __( 'Layout Color Options', 'easy-social-sharing' ), 'desc'  => __( 'This section lets you customize the background color for share icons, by default network colors will be used.', 'easy-social-sharing' ), 'type' => 'title', 'id' => 'layout_color_options' ),

				array(
					'title'    => __( 'Custom Colors', 'easy-social-sharing' ),
					'desc'     => __( 'Enable custom colors', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_custom_colors_enabled',
					'default'  => 'no',
					'type'     => 'checkbox',
					'autoload' => false
				),

				array(
					'title'    => __( 'Background Color', 'easy-social-sharing' ),
					'desc'     => __( 'The background color for share buttons. Default <code>#557da1</code>.', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_background_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#557da1',
					'autoload' => false,
					'desc_tip' => true
				),

				array(
					'title'    => __( 'Hover Background Color', 'easy-social-sharing' ),
					'desc'     => __( 'The hover background color for share buttons. Default <code>#99a7b7</code>.', 'easy-social-sharing' ),
					'id'       => 'easy_social_sharing_hover_background_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#99a7b7',
					'autoload' => false,
					'desc_tip' => true
				),

				array( 'type' => 'sectionend', 'id' => 'layouts_color_options' ),

			) );
		}

		return apply_filters( 'easy_social_sharing_sidebar_get_settings_' . $this->id, $settings, $current_section );
	}
}

endif;

return new ESS_Settings_Layouts();
