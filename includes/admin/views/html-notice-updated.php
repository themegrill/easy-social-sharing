<?php
/**
 * Admin View: Notice - Updated
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated easy-social-sharing-message ess-connect">
	<a class="easy-social-sharing-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'ess-hide-notice', 'update', remove_query_arg( 'do_update_easy_social_sharing' ) ), 'easy_social_sharing_hide_notices_nonce', '_ess_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'easy-social-sharing' ); ?></a>

	<p><?php _e( 'Easy Social Sharing data update complete. Thank you for updating to the latest version!', 'easy-social-sharing' ); ?></p>
</div>
