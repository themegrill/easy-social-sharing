<?php
/**
 * Frontend View: Layout - Inline
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$post_id = is_front_page() && ! is_page() ? '-1' : get_the_ID();

if ( 'yes' == get_post_meta($post->ID, 'disable_ess', true) ) {
	return;
}

$counter = 'yes' == get_option( 'easy_social_sharing_inline_enable_share_counts' ) ? 'ess-display-counts' : 'ess-no-display-counts';

$visbility_class = array();

$visbility_class[] = $class;

if ( 'rounded' == get_option( 'easy_social_sharing_inline_icon_shape' ) ) {
	$visbility_class[] = 'ess-rounded-icon';
} elseif ( 'diagonal' == get_option( 'easy_social_sharing_inline_icon_shape' ) ) {
	$visbility_class[] = 'ess-diagonal-icon';
} elseif ( 'rectangular_rounded' == get_option( 'easy_social_sharing_inline_icon_shape' ) ) {
	$visbility_class[] = 'ess-rectangular-rounded-icon';
}

if ( $inline_layout = get_option( 'easy_social_sharing_inline_layouts' ) ) {
	$visbility_class[] = 'ess-inline-layout-' . $inline_layout;
}

if ( 'no' == get_option( 'easy_social_sharing_inline_enable_share_counts' ) ) {
	$visbility_class[] = 'ess-no-share-counts';
}

if ( 'no' == get_option( 'easy_social_sharing_inline_enable_networks_label' ) ) {
	$visbility_class[] = 'ess-no-network-label';
}

$social_networks = ess_get_core_supported_social_networks();

?>
<div id="ess-main-wrapper">
	<div id="ess-wrap-inline-networks" class="ess-inline-networks-container ess-clear <?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
		<ul class="ess-social-network-lists">
			<?php foreach ( $allowed_networks as $network ) :
				$pinterest = ( 'pinterest' == $network ) ? 'ess-social-share-pinterest' : 'ess-social-share'; ?>
				<li class="ess-social-networks ess-<?php echo esc_attr( $network ); ?> ess-spacing ess-social-sharing">
					<a href="<?php echo esc_url( ess_share_link( $network ) ); ?>" class="ess-social-network-link <?php echo esc_attr( $pinterest . ' ' . $counter ); ?>" rel="nofollow" data-social-name="<?php echo esc_attr( $network ); ?>" data-min-count="<?php echo esc_attr( $network_count[ $network ] ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-location="inline">
						<span class="inline-networks socicon ess-icon socicon-<?php echo esc_attr( $network ); ?>" data-tip="<?php echo ess_sanitize_tooltip( $network_desc[ $network ] ); ?>"></span>
						<?php if ( 'yes' == get_option( 'easy_social_sharing_inline_enable_networks_label' ) ) : ?>
							<span class="ess-text"><?php echo esc_html( $social_networks[ $network ] ); ?></span>
						<?php endif; ?>
						<?php if ( 'yes' == get_option( 'easy_social_sharing_inline_enable_share_counts' ) ) : ?>
							<span class="ess-social-count"></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
			<?php if ( 'yes' == get_option( 'easy_social_sharing_inline_enable_all_networks' ) ) : ?>
				<li class="ess-all-networks ess-social-networks">
					<div class="ess-social-network-link">
						<span class="ess-all-networks-button"><i aria-hidden="true" class="fa fa-ellipsis-h"></i></span>
					</div>
				</li>
			<?php endif; ?>
			<?php if ( 'yes' == get_option( 'easy_social_sharing_inline_enable_total_shares' ) ) : ?>
				<li class="ess-total-share ess-social-networks" data-post-id="<?php echo esc_attr( $post_id ); ?>"><div class="ess-total-share-block"><i class="fa fa-share-alt"></i> <span class="ess-total-count"></span><span class="ess-share-text"><?php esc_html_e( 'Shares', 'easy-social-sharing' ); ?></span></div></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
