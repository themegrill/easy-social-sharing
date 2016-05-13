<?php
/**
 * EasySocialSharing Admin Functions
 *
 * @author   ThemeGrill
 * @category Core
 * @package  EasySocialSharing/Admin/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all Social Sharing screen ids.
 * @return array
 */
function ess_get_screen_ids() {
	return apply_filters( 'easy_social_sharing_screen_ids', array( 'settings_page_easy-social-sharing' ) );
}

/**
 * Output admin fields.
 * @param array $options
 */
function easy_social_sharing_admin_fields( $options ) {

	if ( ! class_exists( 'ESS_Admin_Settings' ) ) {
		include 'class-ess-admin-settings.php';
	}

	ESS_Admin_Settings::output_fields( $options );
}

/**
 * Update all settings which are passed.
 * @param array $options
 */
function easy_social_sharing_update_options( $options ) {

	if ( ! class_exists( 'ESS_Admin_Settings' ) ) {
		include 'class-ess-admin-settings.php';
	}

	ESS_Admin_Settings::save_fields( $options );
}

/**
 * Get a setting from the settings API.
 * @param  mixed $option_name
 * @param  mixed $default
 * @return string
 */
function easy_social_sharing_settings_get_option( $option_name, $default = '' ) {

	if ( ! class_exists( 'ESS_Admin_Settings' ) ) {
		include 'class-ess-admin-settings.php';
	}

	return ESS_Admin_Settings::get_option( $option_name, $default );
}
