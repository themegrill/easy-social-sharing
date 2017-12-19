<?php
/**
 * EasySocialSharing ESS_AJAX
 *
 * AJAX Event Handler
 *
 * @class    ESS_AJAX
 * @version  1.0.0
 * @package  EasySocialSharing/Classes
 * @category Class
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_AJAX Class
 */
class ESS_AJAX {

	/**
	 * Hooks in ajax handlers
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax)
	 */
	public static function add_ajax_events() {
		// easy_social_sharing_EVENT => nopriv
		$ajax_events = array(
			'update_single_share'          => true,
			'get_all_network_shares_count' => true,
			'rated'                        => false,
			'social_networks_save_changes' => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_easy_social_sharing_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_easy_social_sharing_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Update single share on network click.
	 */
	public static function update_single_share() {

		check_ajax_referer( 'update-share', 'security' );
	
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

		$network = isset( $_POST['network'] ) ? ess_clean( $_POST['network'] ) : '';

		$page_url = isset( $_POST['page_url'] ) ? ess_clean( $_POST['page_url'] ) : '';

		$location = isset( $_POST['location'] ) ? ess_clean( $_POST['location'] ) : '';

		$share_counts = self::update_and_get_share_count( $network, $post_id, $page_url, $location, false );

		wp_send_json_success( array( 'counts' => $share_counts ) );

		die();
	}

	/**
	 * Get all shares count for all networks.
	 */
	public static function get_all_network_shares_count( $page_url = '', $is_ajax_request = true, $post_id = - 1 ) {
		if ( $is_ajax_request ) {
			check_ajax_referer( 'all-network-shares-count', 'security' );

			$page_url = ess_clean( $_POST['page_url'] );
			$post_id  = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : $post_id;
		} else {
			$page_url = '' != $page_url ? $page_url : get_permalink();
		}

		$supported_networks      = ess_get_core_supported_social_networks();
		$all_network_share_count = array();
		$client_ip_object        = self::get_client_ip_object();
		$ip_address              = isset( $client_ip_object->ip ) ? trim( $client_ip_object->ip ) : null;

		$cache_option = ess_handle_cache();

		foreach ( $supported_networks as $network_name => $network_label ) {
			$network_statistics = ESS_Social_Networks::_get_network_statistics( $network_name, $page_url );
			$last_share_time    = isset( $network_statistics['sharing_date'] ) ? $network_statistics['sharing_date'] : null;
			if ( ! in_array( $network_name, ess_get_share_networks_with_api_support() ) ) {
				$all_network_share_count[ $network_name ] = ( ESS_Social_Networks::get_network_count_for_non_api_support( $network_name, $page_url, $ip_address ) );
			} elseif ( ess_check_cached_counts( $cache_option ) && in_array( $network_name, ess_get_share_networks_with_api_support() ) ) {
				$all_network_share_count[ $network_name ] = isset( $network_statistics['latest_count'] ) ? $network_statistics['latest_count'] : 0;
			} else {
				$share_counts_received                    = ess_get_shares_number( $network_name, $page_url, $post_id );
				$all_network_share_count[ $network_name ] = $share_counts_received === false ? 0 : $share_counts_received;
			}
		}

		wp_send_json( $all_network_share_count );
	}

	public static function update_and_get_share_count( $network_name, $post_id, $page_url, $share_location, $is_ajax_request = false ) {
		$client_ip_object = self::get_client_ip_object();
		$ip_address       = isset( $client_ip_object->ip ) ? trim( $client_ip_object->ip ) : null;
		$ip_info          = wp_json_encode( $client_ip_object );

		if ( in_array( $network_name, ess_get_share_networks_with_api_support() ) ) {
			$share_counts_received = ess_get_shares_number( $network_name, $page_url, $post_id );
			$network_share_count   = $share_counts_received === false ? 0 : $share_counts_received;
		} else {
			$network_share_count = ( ESS_Social_Networks::get_network_count_for_non_api_support( $network_name, $page_url, $ip_address ) );
		}

		$status = ESS_Social_Networks::update_single_network_count( $network_name, $post_id, $ip_info, $ip_address, $share_location, $page_url, $network_share_count );

		if ( $status ) {
			wp_send_json_success( array( "total_count" => $network_share_count, 'network_name' => $network_name ) );
		} else {
			wp_send_json_error( array( "total_count" => $network_share_count, 'network_name' => $network_name ) );
		}
	}

	public static function get_client_ip_object() {
		$url      = "http://ipinfo.io/json";
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return new stdClass();
		}

		$body = trim( wp_remote_retrieve_body( $response ) );

		return json_decode( $body );
	}


	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function rated() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die( - 1 );
		}

		update_option( 'easy_social_sharing_admin_footer_text_rated', 1 );
		die();
	}

	/**
	 * Handle submissions from assets/js/ess-social-networks.js Backbone model.
	 */
	public static function social_networks_save_changes() {
		if ( ! isset( $_POST['ess_social_networks_nonce'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			exit;
		}

		if ( ! wp_verify_nonce( $_POST['ess_social_networks_nonce'], 'ess_social_networks_nonce' ) ) {
			wp_send_json_error( 'bad_nonce' );
			exit;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			exit;
		}

		global $wpdb;

		$changes = $_POST['changes'];

		foreach ( $changes as $network_id => $data ) {
			if ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				ESS_Social_Networks::_delete_network( $network_id );
				continue;
			}

			// If network name already exists, no need to update database.
			if ( isset( $data['network_name'] ) && in_array( $data['network_name'], ESS_Social_Networks::get_network_names() ) ) {
				continue;
			}

			$network_data = array_intersect_key( $data, array(
				'network_id'     => 1,
				'network_name'   => 1,
				'network_desc'   => 1,
				'network_order'  => 1,
				'network_count'  => 1,
				'is_api_support' => 1
			) );

			if ( isset( $data['newRow'] ) ) {
				// Hurrah, shiny and new!
				$network_id = ESS_Social_Networks::_insert_network( $network_data );
			} else {
				// Updating an existing network..
				if ( ! empty( $network_data ) ) {
					ESS_Social_Networks::_update_network( $network_id, $network_data );
				}
			}

			// Update an API support for a network...
			if ( ! empty( $data['network_name'] ) ) {
				ESS_Social_Networks::_update_network_api_support( $network_id, $data['network_name'] );
			}
		}

		wp_send_json_success( array(
			'social_networks' => ESS_Social_Networks::get_networks()
		) );
	}
}

ESS_AJAX::init();
