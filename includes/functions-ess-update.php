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
	global $wpdb;

	$network_list = ess_get_core_supported_social_networks();

	$network_keys_array = array_keys( $network_list );

	$network_keys = array_map( function ( $val ) {
		return ( '_ess_social_shares_' . $val );
	}, $network_keys_array );


	$query_string = "'" . join( "','", $network_keys ) . "'";

	$meta_data_object = $wpdb->get_results( sprintf( "

		SELECT * FROM " . $wpdb->postmeta . "

		WHERE meta_key IN (%s)", $query_string ) );


	$statistics_data = array();


	foreach ( $meta_data_object as $meta_data ) {

		$meta_data->meta_value = unserialize( $meta_data->meta_value );

		$data = new stdClass();

		$data->network_name = str_replace( '_ess_social_shares_', '', $meta_data->meta_key );

		$data->sharing_date = $meta_data->meta_value['last_upd'];

		$data->post_id = $meta_data->post_id == 1 ? - 1 : $meta_data->post_id;

		$data->ip_info = 'migrated_data';

		$data->ip_address = rand( 1, 1000 );

		$data->share_location = 'inline';

		$data->share_url = $meta_data->post_id == 1 ? home_url() . '/' : get_the_permalink( $meta_data->post_id );

		$data->latest_count = $meta_data->meta_value['counts'];

		array_push( $statistics_data, $data );

		delete_post_meta_by_key( $meta_data->meta_key );

	}


	$loop_index = 1;

	foreach ( $statistics_data as $single_statistics ) {

		$count_number = intval( $single_statistics->latest_count );

		for ( $i = 1; $i <= $count_number; $i ++ ) {

			$statistics_data_single = (array) $single_statistics;

			$statistics_data_single['ip_address'] = $loop_index;

			$statistics_data_single['latest_count'] = $i;

			$wpdb->insert( $wpdb->prefix . 'ess_social_statistics', $statistics_data_single );

			$loop_index ++;

		}

	}

	add_option( 'ess-data-migrated', '1' );
}


function ess_update_130_db_version() {
	ESS_Install::update_db_version( '1.3.0' );
}

