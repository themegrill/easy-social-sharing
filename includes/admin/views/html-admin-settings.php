<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class = '';
if ( $current_tab === "layouts" && $current_tab != null && $current_section != '' ) {
	$class = 'ess-form-inner';
}
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

		<div class="ess-form-wrapper">
			<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
			<?php
				do_action( 'easy_social_sharing_sections_' . $current_tab );
				self::show_messages();
			?>

			<div class="<?php echo esc_attr( $class ); ?>">
				<?php
					do_action( 'easy_social_sharing_settings_' . $current_tab );
					do_action( 'easy_social_sharing_settings_tabs_' . $current_tab ); // @deprecated hook
				?>
				<p class="submit">
					<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
						<input name="save" class="button-primary" type="submit"
						       value="<?php esc_attr_e( 'Save Changes', 'easy-social-sharing' ); ?>"/>
					<?php endif; ?>
					<input type="hidden" name="subtab" id="last_tab"/>
					<?php wp_nonce_field( 'easy-social-sharing-settings' ); ?>
				</p>
			</div>
			<?php if ( $current_tab === "layouts" && $current_tab != null && $current_section != '' ) { ?>
				<div id="ess-main-wrapper" class="ess-setting-preview">
					<?php
					echo $default_preview_class;
					$preview_class_name = isset( $ess_preview_class[ $current_section ] ) ? $ess_preview_class[ $current_section ] : '';
					?>
					<h3 class="ess-setting-preview__title">Live Preview</h3>
					<p class="ess-setting-preview__subtitle">See how the social icon will look like</p>
					<div id="ess-wrap-<?php echo $current_section !== '' ? $current_section : 'unknown'; ?>-networks"
					     class="ess-preview-icon-container ess-<?php echo $current_section !== '' ? $current_section : 'unknown'; ?>-networks-container ess-clear <?php echo $preview_class_name; ?>">
						<ul class="ess-social-network-lists">
							<li class="ess-social-networks ess-facebook ess-spacing ess-social-sharing">
							<span href="" class="ess-social-network-link ess-social-share ess-display-counts">
								<span class="inline-networks socicon ess-icon socicon-facebook" data-tip=""></span>
								<span class="ess-social-count">20</span>
							</span>
							</li>

							<li class="ess-social-networks ess-twitter ess-spacing ess-social-sharing">
							<span href="" class="ess-social-network-link ess-social-share ess-display-counts">
								<span class="inline-networks socicon ess-icon socicon-twitter" data-tip=""></span>
								<span class="ess-social-count">20</span>
							</span>
							</li>

							<li class="ess-social-networks ess-googleplus ess-spacing ess-social-sharing">
							<span href="" class="ess-social-network-link ess-social-share ess-display-counts">
								<span class="inline-networks socicon ess-icon socicon-googleplus" data-tip=""></span>
								<span class="ess-social-count">20</span>
							</span>
							</li>
						</ul>
					</div>
				</div> <!-- .ess-setting-preview -->
			<?php } ?>
		</div> <!-- ess-form-inner -->
	</form>
</div>
