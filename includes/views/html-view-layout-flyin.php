<?php
/**
 * Frontend View: Layout - Fly-In
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$post_id = is_front_page() && ! is_page() ? '-1' : get_the_ID();

if ( 'yes' == get_post_meta($post->ID, 'disable_ess', true) ) {
	return;
}

$counter = 'yes' == get_option( 'easy_social_sharing_flyin_enable_share_counts' ) ? 'ess-display-counts' : 'ess-no-display-counts';

$visbility_class = array();
$attributes_data = array();

if ( 'rounded' == get_option( 'easy_social_sharing_flyin_icon_shape' ) ) {
	$visbility_class[] = 'ess-rounded-icon';
}

if ( 'bottom_left' == get_option( 'easy_social_sharing_flyin_icons_location' ) ) {
	$visbility_class[] = 'ess-bottom-left';
}

if ( 'no' == get_option( 'easy_social_sharing_flyin_enable_all_networks' ) ) {
	$visbility_class[] = 'ess-no-all-networks';
}

if ( 'no' == get_option( 'easy_social_sharing_flyin_enable_networks_label' ) ) {
	$visbility_class[] = 'ess-no-network-label';
}

// Trigger visibility classes and custom attributes.
if ( 'yes' == get_option( 'easy_social_sharing_flyin_trigger_purchase' ) ) {
	$visbility_class[] = 'ess-social-after-purchase';
}

if ( 'yes' == get_option( 'easy_social_sharing_flyin_trigger_comment' ) ) {
	$visbility_class[] = 'ess-social-after-comment';
}

if ( 'yes' == get_option( 'easy_social_sharing_flyin_trigger_bottom' ) ) {
	$visbility_class[] = 'ess-social-bottom-trigger';
}

if ( 'yes' ==  get_option( 'easy_social_sharing_flyin_enable_delay' ) ) {
	$visbility_class[] = 'ess-social-auto-flyin';
	$attributes_data['data-delay'] = get_option( 'easy_social_sharing_flyin_delay_duration' );
}

if ( 'yes' ==  get_option( 'easy_social_sharing_flyin_enable_idle' ) ) {
	$visbility_class[] = 'ess-social-trigger-idle';
	$attributes_data['data-idle_timeout'] = get_option( 'easy_social_sharing_flyin_idle_timeout' );
}

if ( 'yes' == get_option( 'easy_social_sharing_flyin_enable_scroll' ) ) {
	$visbility_class[] = 'ess-social-trigger-scroll';
	$attributes_data['data-scroll_position'] = get_option( 'easy_social_sharing_flyin_scroll_position' );
}

if ( 'yes' == get_option( 'easy_social_sharing_flyin_enable_cookie' ) ) {
	$attributes_data['data-cookie_duration'] = get_option( 'easy_social_sharing_flyin_cookie_duration' );
}

// Custom attribute handling.
$custom_attributes = array();

if ( ! empty( $attributes_data ) && is_array( $attributes_data ) ) {
	foreach ( $attributes_data as $attribute => $attribute_value ) {
		$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
	}
}

// Fly-In title and message.
$flyin_title   = get_option( 'easy_social_sharing_flyin_title' );
$flyin_message = get_option( 'easy_social_sharing_flyin_message' );

$social_networks = ess_get_core_supported_social_networks();

?>
<div id="ess-main-wrapper">
	<div id="ess-wrap-fly-networks" class="ess-fly-layout-wrapper ess-clear <?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>" <?php echo implode( ' ', $custom_attributes ); ?>>
		<div class="ess-fly-block-wrapper">
			<div class="ess-fly-layout-close"> <i class="fa fa-close"> </i> </div>
			<?php if ( $flyin_title || $flyin_message ) : ?>
				<div class="ess-fly-title-wrapper">
					<?php if ( $flyin_title ) : ?>
						<h3 class="ess-fly-title"><?php echo esc_html( $flyin_title ); ?></h3>
					<?php endif; ?>
					<?php if ( $flyin_message ) : ?>
						<div class="ess-fly-subtitle"><?php echo esc_html( $flyin_message ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( 'yes' == get_option( 'easy_social_sharing_flyin_enable_total_shares' ) ) : ?>
				<div class="ess-total-share" data-post-id="<?php echo esc_attr( $post_id ); ?>"><div class="ess-total-share-block"><i class="fa fa-share-alt"></i><span class="ess-total-count"></span><span><?php esc_html_e( 'Shares', 'easy-social-sharing' ); ?></span></div></div>
			<?php endif; ?>
			<div class="ess-fly-share-wrapper ess-clear">
				<ul class="ess-clear ess-social-network-lists">
					<?php foreach ( $allowed_networks as $network ) :
						$pinterest = ( 'pinterest' == $network ) ? 'ess-social-share-pinterest' : 'ess-social-share'; ?>
						<li class="ess-social-networks ess-<?php echo esc_attr( $network ); ?> ess-spacing ess-social-sharing">
							<a href="<?php echo esc_url( ess_share_link( $network ) ); ?>" class="<?php echo esc_attr( $pinterest . ' ' . $counter ); ?>" rel="nofollow" data-social-name="<?php echo esc_attr( $network ); ?>" data-min-count="<?php echo esc_attr( $network_count[ $network ] ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-location="flyin">
								<span class="ess-social-networks-info-wrapper">
									<span class="fly-networks socicon ess-icon socicon-<?php echo esc_attr( $network ); ?>" data-tip="<?php echo ess_sanitize_tooltip( $network_desc[ $network ] ); ?>"></span>
									<?php if ( 'yes' == get_option( 'easy_social_sharing_flyin_enable_share_counts' ) ) : ?>
										<span class="ess-social-count"></span>
									<?php endif; ?>
									<?php if ( 'yes' == get_option( 'easy_social_sharing_flyin_enable_networks_label' ) ) : ?>
										<span class="ess-text"><?php echo esc_html( $social_networks[ $network ] ); ?></span>
									<?php endif; ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
					<?php if ( 'yes' == get_option( 'easy_social_sharing_flyin_enable_all_networks' ) ) : ?>
						<li class="ess-social-networks ess-all-networks">
							<span class="ess-social-network-link">
								<i class="fa fa-ellipsis-h ess-icon" aria-hidden="true"></i>
							</span>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
