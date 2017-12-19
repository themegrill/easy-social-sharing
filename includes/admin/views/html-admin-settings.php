<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ess_live_preview_class = array(
	'inline'  => 'ess-no-network-label ess-inline-top',
	'sidebar' => 'ess-sidebar-enable ess-no-total-shares',
	'popup'   => 'ess-popup-layout-wrapper ess-social-visible',
	'flyin'   => 'ess-fly-layout-wrapper ess-social-visible',
);

$ess_live_preview_network = array( 'facebook', 'twitter', 'googleplus' );
$ess_live_preview_classes = isset( $ess_live_preview_class[ $current_section ] ) ? $ess_live_preview_class[ $current_section ] : '';
?>
<div class="wrap easy-social-sharing">
	<?php if ( 'analytics' != $current_tab ) : ?>
	<form method="<?php echo esc_attr( apply_filters( 'easy_social_sharing_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
	<?php endif; ?>
		<nav class="nav-tab-wrapper ess-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label ) {
					echo '<a href="' . admin_url( 'options-general.php?page=easy-social-sharing&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
				}

				do_action( 'easy_social_sharing_settings_tabs' );
			?>
		</nav>
		<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
		<?php
			do_action( 'easy_social_sharing_sections_' . $current_tab );
			self::show_messages();
		?>
		<?php if ( 'layouts' === $current_tab && '' !== $current_section ) : ?>
			<div class="ess-form-wrapper">
				<div class="ess-form-inner">
					<?php do_action( 'easy_social_sharing_settings_' . $current_tab ); ?>
				</div>
				<div id="ess-main-wrapper" class="ess-setting-preview">
					<h3 class="ess-setting-preview__title"><?php esc_html_e( 'Live Preview', 'easy-social-sharing' ); ?></h3>
					<p class="ess-setting-preview__subtitle"><?php esc_html_e( 'See how the social icon will look like.', 'easy-social-sharing' ); ?></p>
					<div id="ess-wrap-<?php echo $current_section !== '' ? $current_section : 'unknown'; ?>-networks"
					     class="ess-preview-icon-container ess-<?php echo $current_section !== '' ? $current_section : 'unknown'; ?>-networks-container ess-clear <?php echo $ess_live_preview_classes; ?>">
						<ul class="ess-social-network-lists">
							<?php foreach ( $ess_live_preview_network as $live_preview_network ) : ?>
								<li class="ess-social-networks ess-<?php echo $live_preview_network; ?> ess-spacing ess-social-sharing">
									<span href="" class="ess-social-network-link ess-social-share ess-display-counts">
										<span class="inline-networks socicon ess-icon socicon-<?php echo $live_preview_network; ?>"></span>
										<span class="ess-social-count"><?php echo number_format_i18n( 20 ); ?></span>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		<?php else: ?>
			<?php do_action( 'easy_social_sharing_settings_' . $current_tab ); ?>
		<?php endif; ?>
		<p class="submit">
			<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
				<input name="save" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save changes', 'easy-social-sharing' ); ?>" />
			<?php endif; ?>
			<?php wp_nonce_field( 'easy-social-sharing-settings' ); ?>
		</p>
	<?php if ( 'analytics' != $current_tab ) : ?>
	</form>
	<?php endif; ?>
</div>
