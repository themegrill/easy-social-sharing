<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated easy-social-sharing-message ess-connect">
	<p><strong><?php _e( 'Easy Social Sharing Data Update', 'easy-social-sharing' ); ?></strong> &#8211; <?php _e( 'We need to update your site\'s database to the latest version.', 'easy-social-sharing' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_easy_social_sharing', 'true', admin_url( 'options-general.php?page=easy-social-sharing' ) ) ); ?>" class="ess-update-now button-primary"><?php _e( 'Run the updater', 'easy-social-sharing' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery( '.ess-update-now' ).click( 'click', function() {
		return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'easy-social-sharing' ) ); ?>' ); // jshint ignore:line
	});
</script>
