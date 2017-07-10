<?php
/**
 * EasySocialSharing Updates
 *
 * Function for updating data, used by the background updater.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  EasySocialSharing/Functions
 * @version  1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ess_update_100_db_version() {
	ESS_Install::update_db_version( '1.0.0' );
}

function ess_update_101_db_version() {
	ESS_Install::update_db_version( '1.0.1' );
}

function ess_update_120_social_networks() {
	$existing_social_networks = get_option( 'easy_social_sharing_allowed_networks', array() );

	if ( $existing_social_networks ) {

		foreach ( $existing_social_networks as $key => $social_network ) {
			$network_data = array();

			if ( ! in_array( $social_network, ESS_Social_Networks::get_network_names() ) ) {
				$network_data['network_name'] = $social_network;

				if ( ! in_array( $social_network, ess_get_share_networks_with_api_support() ) ) {
					$network_data['is_api_support'] = 0;
				}

				// Insert an existing network.
				ESS_Social_Networks::_insert_network( $network_data );
			}
		}

		delete_option( 'easy_social_sharing_allowed_networks' );
	}
}

function ess_update_120_delete_options() {
	delete_option( 'easy_social_sharing_handheld_disable' );
	delete_option( 'easy_social_sharing_minimum_share_count' );
	delete_option( 'easy_social_sharing_sidebar_icons_spacing' );
}

function ess_update_120_db_version() {
	ESS_Install::update_db_version( '1.2.0' );
}

function ess_update_130_social_statics() {

}

function ess_update_130_db_version() {
	ESS_Install::update_db_version( '1.3.0' );
}
