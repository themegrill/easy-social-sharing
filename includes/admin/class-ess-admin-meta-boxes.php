<?php
/**
 * EasySocialSharing Meta Boxes
 *
 * Sets up the write panels used by custom post types.
 *
 * @class    ESS_Admin_Meta_Boxes
 * @version  1.0.0
 * @package  EasySocialSharing/Admin/Meta Boxes
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_Admin_Meta_Boxes Class
 */
class ESS_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Location Meta Boxes
		add_action( 'easy_social_sharing_process_location_meta', 'ESS_Meta_Box_Location_Data::save', 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'easy_social_sharing_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'easy_social_sharing_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="easy_social_sharing_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'easy_social_sharing_meta_box_errors' );
		}
	}

	/**
	 * Add AC Meta boxes.
	 */
	public function add_meta_boxes() {
		// Location Settings
		foreach ( ess_get_allowed_screen_types() as $type ) {
			add_meta_box( 'easy-social-sharing-location-data', __( 'Share Location', 'easy-social-sharing' ), 'ESS_Meta_Box_Location_Data::output', $type, 'normal', 'default' );
		}
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 * @param int $post_id
	 * @param object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Don't save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['easy_social_sharing_meta_nonce'] ) || ! wp_verify_nonce( $_POST['easy_social_sharing_meta_nonce'], 'easy_social_sharing_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		self::$saved_meta_boxes = true;

		// Trigger action
		$process_actions = array( 'location' );
		foreach ( $process_actions as $process_action ) {
			do_action( 'easy_social_sharing_process_' . $process_action . '_meta', $post_id, $post );
		}
	}
}

new ESS_Admin_Meta_Boxes();
