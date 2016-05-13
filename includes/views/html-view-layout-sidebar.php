<?php
/**
 * Frontend View: Layout - Sidebar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$min_cnt = get_option( 'easy_social_sharing_minimum_share_count' );
$post_id = is_front_page() && ! is_page() ? '-1' : get_the_ID();

$spacing = 'yes' == get_option( 'easy_social_sharing_sidebar_icons_spacing' ) ? 'ess-spacing' : 'ess-no-spacing';
$counter = 'yes' == get_option( 'easy_social_sharing_sidebar_enable_share_counts' ) ? 'ess-display-counts' : 'ess-no-display-counts';

$visbility_class = array();

if ( 'rounded' == get_option( 'easy_social_sharing_sidebar_icon_shape' ) ) {
	$visbility_class[] = 'ess-rounded-icon';
}

if ( 'no' == get_option( 'easy_social_sharing_sidebar_enable_total_shares' ) ) {
	$visbility_class[] = 'ess-no-total-shares';
}

if ( 'no' == get_option( 'easy_social_sharing_sidebar_enable_all_networks' ) ) {
	$visbility_class[] = 'ess-no-all-networks';
}

if ( 'right' == get_option( 'easy_social_sharing_sidebar_layout_orientation' ) ) {
	$visbility_class[] = 'ess-right-layout';
}

?>
<div id="ess-wrap-sidebar-networks" class="ess-sidebar-enable <?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
	<?php if ( 'yes' == get_option( 'easy_social_sharing_sidebar_enable_total_shares' ) ) : ?>
		<div class="ess-total-share" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="ess-total-count">&hellip;</span><span><?php esc_html_e( 'Shares', 'easy-social-sharing' ); ?></span></div>
	<?php endif; ?>
	<ul class="ess-social-network-lists">
		<?php foreach ( $allowed_networks as $network ) :
			$pinterest = ( 'pinterest' == $network ) ? 'ess-social-share-pinterest' : 'ess-social-share'; ?>
			<li class="ess-social-networks <?php echo esc_attr( $spacing ); ?>">
				<a href="<?php echo esc_url( ess_share_link( $network ) ); ?>" class="<?php echo esc_attr( $pinterest . ' ' . $counter ); ?>" rel="nofollow" data-social-name="<?php echo esc_attr( $network ); ?>" data-min-count="<?php echo esc_attr( $min_cnt ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-location="sidebar">
					<span class="sidebar-networks socicon socicon-<?php echo esc_attr( $network ); ?>">
						<?php if ( 'yes' == get_option( 'easy_social_sharing_sidebar_enable_share_counts' ) ) : ?>
							<span class="ess-social-count">&hellip;</span>
						<?php endif; ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( 'yes' == get_option( 'easy_social_sharing_sidebar_enable_all_networks' ) ) : ?>
		<div class="ess-all-networks"><span>&raquo;</span></div>
	<?php endif; ?>
	<div class="ess-all-networks-toggle"><span>&rsaquo;</span></div>
</div>
