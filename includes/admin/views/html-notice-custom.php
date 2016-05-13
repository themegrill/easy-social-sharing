<?php
/**
 * Admin View: Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated easy-social-sharing-message">
	<a class="easy-social-sharing-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'ess-hide-notice', $notice ), 'easy_social_sharing_hide_notices_nonce', '_ess_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'easy-social-sharing' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>
