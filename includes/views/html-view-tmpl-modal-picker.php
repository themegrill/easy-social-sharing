<?php
/**
 * Frontend View: Template - Pinterest Image Picker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="ess-main-wrapper">
	<div class="ess-pinterest-img-picker-popup">
		<h3 class="ess-pinterest-popup-title"><?php esc_html_e( 'Pin It on Pinterest', 'easy-social-sharing' ); ?></h3>
		<div class="ess-pinterest-popup-close"><i class="fa fa-close"></i></div>
		<div class="ess-social-pin-images" data-permalink="<?php echo esc_attr( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-post_id="<?php echo esc_attr( get_the_ID() ); ?>"></div>
		<div class="ess-no-pinterest-img-found"></div>
	</div>
</div>
