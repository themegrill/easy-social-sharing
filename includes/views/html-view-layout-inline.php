<?php
/**
 * Frontend View: Layout - Inline
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = is_front_page() && ! is_page() ? '-1' : get_the_ID();

$counter = 'yes' == get_option( 'easy_social_sharing_inline_enable_share_counts' ) ? 'ess-display-counts' : 'ess-no-display-counts';

$visbility_class = array();

$visbility_class[] = $class;

if ( 'rounded' == get_option( 'easy_social_sharing_inline_icon_shape' ) ) {
	$visbility_class[] = 'ess-rounded-icon';
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
<div id="ess-wrap-inline-networks" class="ess-clear <?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
	<ul class="ess-social-network-lists">
		<?php foreach ( $allowed_networks as $network ) :
			$pinterest = ( 'pinterest' == $network ) ? 'ess-social-share-pinterest' : 'ess-social-share'; ?>
			<li class="ess-social-networks ess-<?php echo esc_attr( $network ); ?> ess-spacing ess-social-sharing">
				<a href="<?php echo esc_url( ess_share_link( $network ) ); ?>" class="<?php echo esc_attr( $pinterest . ' ' . $counter ); ?>" rel="nofollow" data-social-name="<?php echo esc_attr( $network ); ?>" data-min-count="<?php echo esc_attr( $network_count[ $network ] ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-location="inline">
					<span class="inline-networks socicon ess-icon socicon-<?php echo esc_attr( $network ); ?>" data-tip="<?php echo ess_sanitize_tooltip( $network_desc[ $network ] ); ?>"></span>
					<?php if ( 'yes' == get_option( 'easy_social_sharing_inline_enable_networks_label' ) ) : ?>
						<span class="ess-text"><?php echo esc_html( $social_networks[ $network ] ); ?></span>
					<?php endif; ?>
					<?php if ( 'yes' == get_option( 'easy_social_sharing_inline_enable_share_counts' ) ) : ?>
						<span class="ess-social-count">0</span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
		<?php if ( 'yes' == get_option( 'easy_social_sharing_inline_enable_all_networks' ) ) : ?>
			<li class="ess-all-networks"><span>&raquo;</span></li>
		<?php endif; ?>
	</ul>
</div>
