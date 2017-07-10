<?php
/**
 * EasySocialSharing Location Data.
 *
 * @class    ESS_Meta_Box_Location_Data
 * @version  1.0.0
 * @package  EasySocialSharing/Admin/Meta Boxes
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_Meta_Box_Location_Data Class
 */
class ESS_Meta_Box_Location_Data {

	/**
	 * Output the meta box.
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		wp_nonce_field( 'easy_social_sharing_save_data', 'easy_social_sharing_meta_nonce' );

		?>
		<ul class="location_data">
			<p class="form-field">
				<label for="location_disabled"><?php _e( 'Disable Locations', 'easy-social-sharing' ) ?></label>
				<span class="description side"><?php _e( 'Select locations to disable on this screen.', 'easy-social-sharing' ); ?></span>
				<select id="location_disabled" name="location_disabled[]" class="ess-enhanced-select" multiple="multiple" style="width:50%" data-placeholder="<?php esc_attr_e( 'No locations', 'easy-social-sharing' ); ?>">
					<?php
						$location_ids = (array) get_post_meta( $post->ID, '_ess_location_disabled', true );
						$locations    = ess_get_allowed_screen_locations();

						if ( $locations ) foreach ( $locations as $location_id => $location_name ) {
							echo '<option value="' . esc_attr( $location_id ) . '"' . selected( in_array( $location_id, $location_ids ), true, false ) . '>' . esc_html( $location_name ) . '</option>';
						}
					?>
				</select>
			</p>
			<?php do_action( 'easy_social_sharing_settings_data_end', $post->ID ); ?>
		</ul>
		<?php
	}

	/**
	 * Save meta box data.
	 * @param int $post_id
	 */
	public static function save( $post_id ) {
		// Update meta
		update_post_meta( $post_id, '_ess_location_disabled', isset( $_POST['location_disabled'] ) ? array_map( 'ess_clean', $_POST['location_disabled'] ) : array() );
	}
}
