<?php
/**
 * Admin View: Notice - Updating
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated easy-social-sharing-message ess-connect">
	<p><strong><?php esc_html__( 'Easy Social Sharing Data Update', 'easy-social-sharing' ); ?></strong> &#8211; <?php esc_html__( 'Your database is being updated in the background.', 'easy-social-sharing' ); ?> <a href="<?php echo esc_url( add_query_arg( 'force_update_easy_social_sharing', 'true', admin_url( 'options-general.php?page=easy-social-sharing' ) ) ); ?>"><?php esc_html__( 'Taking a while? Click here to run it now.', 'easy-social-sharing' ); ?></a></p>
</div>
