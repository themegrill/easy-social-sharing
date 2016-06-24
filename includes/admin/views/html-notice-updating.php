<?php
/**
 * Admin View: Notice - Updating
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated easy-social-sharing-message ess-connect">
	<p><strong><?php _e( 'Easy Social Sharing Data Update', 'easy-social-sharing' ); ?></strong> &#8211; <?php _e( 'Your database is being updated in the background.', 'easy-social-sharing' ); ?> <a href="<?php echo esc_url( add_query_arg( 'force_update_easy_social_sharing', 'true', admin_url( 'options-general.php?page=easy-social-sharing' ) ) ); ?>"><?php _e( 'Taking a while? Click here to run it now.', 'easy-social-sharing' ); ?></a></p>
</div>
