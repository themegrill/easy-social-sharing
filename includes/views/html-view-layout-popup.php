<?php
/**
 * Frontend View: Layout - Pop-Up
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$post_id = is_front_page() && ! is_page() ? '-1' : get_the_ID();

if ( 'yes' == get_post_meta($post->ID, 'disable_ess', true) ) {
	return;
}

$counter = 'yes' == get_option( 'easy_social_sharing_popup_enable_share_counts' ) ? 'ess-display-counts' : 'ess-no-display-counts';

$visbility_class = array();
$attributes_data = array();

if ( 'rounded_corners' == get_option( 'easy_social_sharing_popup_layout' ) ) {
	$visbility_class[] = 'ess-popup-layout-rectangular-rounded';
}

if ( 'no' == get_option( 'easy_social_sharing_popup_enable_all_networks' ) ) {
	$visbility_class[] = 'ess-no-all-networks';
}

if ( 'no' == get_option( 'easy_social_sharing_popup_enable_networks_label' ) ) {
	$visbility_class[] = 'ess-no-network-label';
}

// Trigger visibility classes and custom attributes.
if ( 'yes' == get_option( 'easy_social_sharing_popup_trigger_purchase' ) ) {
	$visbility_class[] = 'ess-social-after-purchase';
}

if ( 'yes' == get_option( 'easy_social_sharing_popup_trigger_comment' ) ) {
	$visbility_class[] = 'ess-social-after-comment';
}

if ( 'yes' == get_option( 'easy_social_sharing_popup_trigger_bottom' ) ) {
	$visbility_class[] = 'ess-social-bottom-trigger';
}

if ( 'yes' ==  get_option( 'easy_social_sharing_popup_enable_delay' ) ) {
	$visbility_class[] = 'ess-social-auto-popup';
	$attributes_data['data-delay'] = get_option( 'easy_social_sharing_popup_delay_duration' );
}

if ( 'yes' ==  get_option( 'easy_social_sharing_popup_enable_idle' ) ) {
	$visbility_class[] = 'ess-social-trigger-idle';
	$attributes_data['data-idle_timeout'] = get_option( 'easy_social_sharing_popup_idle_timeout' );
}

if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_scroll' ) ) {
	$visbility_class[] = 'ess-social-trigger-scroll';
	$attributes_data['data-scroll_position'] = get_option( 'easy_social_sharing_popup_scroll_position' );
}

if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_cookie' ) ) {
	$attributes_data['data-cookie_duration'] = get_option( 'easy_social_sharing_popup_cookie_duration' );
}

// Custom attribute handling.
$custom_attributes = array();

if ( ! empty( $attributes_data ) && is_array( $attributes_data ) ) {
	foreach ( $attributes_data as $attribute => $attribute_value ) {
		$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
	}
}

// Pop-Up title and message.
$popup_title   = get_option( 'easy_social_sharing_popup_title' );
$popup_message = get_option( 'easy_social_sharing_popup_message' );

$social_networks = ess_get_core_supported_social_networks();

?>
<div id="ess-main-wrapper">
	<div id="ess-wrap-popup-networks" class="ess-popup-layout-wrapper ess-clear <?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>" <?php echo implode( ' ', $custom_attributes ); ?>>
		<div class="ess-popup-layout-overlay"></div>
		<div class="ess-popup-block-wrapper">
			<div class="ess-popup-layout-close"><i class="fa fa-close"></i></div>
			<?php if ( $popup_title || $popup_message ) : ?>
				<div class="ess-popup-title-wrapper">
					<?php if ( $popup_title ) : ?>
						<h3 class="ess-popup-title"><?php echo esc_html( $popup_title ); ?></h3>
					<?php endif; ?>
					<?php if ( $popup_message ) : ?>
						<div class="ess-popup-subtitle"><?php echo esc_html( $popup_message ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_total_shares' ) ) : ?>
				<div class="ess-total-share" data-post-id="<?php echo esc_attr( $post_id ); ?>"><div class="ess-total-share-block"><i class="fa fa-share-alt"></i><span class="ess-total-count"></span><span><?php esc_html_e( 'Shares', 'easy-social-sharing' ); ?></span></div></div>
			<?php endif; ?>
			<div class="ess-popup-share-wrapper ess-clear">
				<ul class="ess-clear ess-choosen-networks ess-social-network-lists">
					<?php foreach ( $allowed_networks as $network ) :
						$pinterest = ( 'pinterest' == $network ) ? 'ess-social-share-pinterest' : 'ess-social-share'; ?>
						<li class="ess-social-networks ess-<?php echo esc_attr( $network ); ?> ess-spacing ess-social-sharing">
							<a href="<?php echo esc_url( ess_share_link( $network ) ); ?>" class="<?php echo esc_attr( $pinterest . ' ' . $counter ); ?>" rel="nofollow" data-social-name="<?php echo esc_attr( $network ); ?>" data-min-count="<?php echo esc_attr( $network_count[ $network ] ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-location="popup">

								<span class="ess-social-networks-info-wrapper">
									<span class="popup-networks socicon ess-icon socicon-<?php echo esc_attr( $network ); ?>" data-tip="<?php echo ess_sanitize_tooltip( $network_desc[ $network ] ); ?>"></span>
									<?php if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_share_counts' ) ) : ?>
										<span class="ess-social-count"></span>
									<?php endif; ?>
									<?php if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_networks_label' ) ) : ?>
										<span class="ess-text"><?php echo esc_html( $social_networks[ $network ] ); ?></span>
									<?php endif; ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
					<?php if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_all_networks' ) ) : ?>
						<li class="ess-social-networks ess-all-networks--popup">
							<span class="ess-social-network-link">
								<i class="fa fa-ellipsis-h ess-icon" aria-hidden="true"></i>
							</span>
						</li>
					<?php endif; ?>
				</ul>
				<?php if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_all_networks' ) ) : ?>
					<ul class="ess-clear ess-available-networks ess-social-network-lists">
						<?php foreach ( $social_networks as $network => $network_name ) :
							$pinterest = ( 'pinterest' == $network ) ? 'ess-social-share-pinterest' : 'ess-social-share'; ?>
							<li class="ess-social-networks ess-<?php echo esc_attr( $network ); ?> ess-spacing ess-social-sharing">
								<a href="<?php echo esc_url( ess_share_link( $network ) ); ?>" class="<?php echo esc_attr( $pinterest . ' ' . $counter ); ?>" rel="nofollow" data-social-name="<?php echo esc_attr( $network ); ?>" data-min-count="<?php echo isset( $network_count[ $network ] ) ? esc_attr( $network_count[ $network ] ) : 0; ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-location="popup">
									<span class="ess-social-networks-info-wrapper">
										<span class="popup-networks socicon ess-icon socicon-<?php echo esc_attr( $network ); ?>" data-tip="<?php echo isset( $network_desc[ $network ] ) ? ess_sanitize_tooltip( $network_desc[ $network ] ) : ''; ?>"></span>
										<?php if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_share_counts' ) ) : ?>
											<span class="ess-social-count"></span>
										<?php endif; ?>
										<?php if ( 'yes' == get_option( 'easy_social_sharing_popup_enable_networks_label' ) ) : ?>
											<span class="ess-text"><?php echo esc_html( $network_name ); ?></span>
										<?php endif; ?>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>

	</div>
</div>
