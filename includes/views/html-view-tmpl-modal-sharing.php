<?php
/**
 * Frontend View: Template - Sharing Pop-Up Modal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$social_networks = ess_get_core_supported_social_networks();

?>
<div id="ess-main-wrapper">
	<div class="ess-popup-overlay"></div>
	<div class="ess-pinterest-popup-overlay"></div>
	<div class="ess-popup-wrapper">
		<div class="ess-popup-close"><i class="fa fa-close"></i></div>
		<div id="ess-wrap-inline-networks">
			<ul class="ess-social-network-lists">
				<?php foreach ( $social_networks as $network_id => $network ) :
					$pinterest = ( 'pinterest' == $network_id ) ? 'ess-social-share-pinterest' : 'ess-social-share'; ?>
					<li class="ess-social-networks ess-<?php echo esc_attr( $network_id ); ?> ess-spacing ess-social-sharing">
						<a href="<?php echo esc_url( ess_share_link( $network_id ) ); ?>" class="<?php echo esc_attr( $pinterest ); ?>" rel="nofollow" data-location="modal">
							<span class="inline-networks socicon ess-icon socicon-<?php echo esc_attr( $network_id ); ?>"></span>
							<span class="ess-text"><?php echo esc_html( $network ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
