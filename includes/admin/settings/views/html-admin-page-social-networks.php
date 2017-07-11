<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<h2>
	<?php _e( 'Social Networks', 'easy-social-sharing' ); ?>
	<?php echo ess_help_tip( __( 'The following social networks enable your users to spread their experience with an effective word-of-mouth advertising.', 'easy-social-sharing' ) ); ?>
</h2>

<?php do_action( 'easy_social_sharing_before_networks_table' ); ?>

<table class="ess-social-networks widefat">
	<thead>
		<tr>
			<th class="ess-social-network-sort"><?php echo ess_help_tip( __( 'Drag and drop to re-order your custom networks. This is the order in which they will be displayed on the frontend.', 'easy-social-sharing' ) ); ?></th>
			<th class="ess-social-network-icon"><?php _e( 'Icon', 'easy-social-sharing' ); ?></th>
			<th class="ess-social-network-name"><?php _e( 'Name', 'easy-social-sharing' ); ?></th>
			<th class="ess-social-network-description"><?php _e( 'Description', 'easy-social-sharing' ); ?></th>
			<th class="ess-social-network-min-count"><?php _e( 'Minimum Count', 'easy-social-sharing' ); ?></th>
			<th class="ess-social-network-api-support"><?php _e( 'API Support', 'easy-social-sharing' ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="6">
				<input type="submit" name="save" class="button button-primary ess-social-network-save" value="<?php esc_attr_e( 'Save changes', 'easy-social-sharing' ); ?>" disabled />
				<a class="button button-secondary ess-social-network-add" href="#"><?php esc_html_e( 'Add social network', 'easy-social-sharing' ); ?></a>
			</td>
		</tr>
	</tfoot>
	<tbody class="ess-social-network-rows"></tbody>
</table>

<script type="text/html" id="tmpl-ess-social-network-row-blank">
	<tr>
		<td class="ess-social-network-blank-state" colspan="6">
			<p><?php _e( 'No Social Networks have been created.', 'easy-social-sharing' ); ?></p>
		</td>
	</tr>
</script>

<script type="text/html" id="tmpl-ess-social-network-row">
	<tr data-id="{{ data.network_id }}">
		<td width="1%" class="ess-social-network-sort"></td>
		<td class="ess-social-network-icon">
			<# if ( data.network_name ) { #>
				<span class="socicon socicon-{{ data.network_name }}"></span>
			<# } else { #>
				<span class="socicon socicon-facebook"></span>
			<# } #>
		</td>
		<td class="ess-social-network-name">
			<div class="view">
				<strong>{{ data.formatted_network_name }}</strong>
				<div class="row-actions">
					<a class="ess-social-network-edit" href="#"><?php _e( 'Edit', 'easy-social-sharing' ); ?></a> | <a href="#" class="ess-social-network-delete"><?php _e( 'Remove', 'easy-social-sharing' ); ?></a>
				</div>
			</div>
			<div class="edit">
				<select class="ess-enhanced-select" name="network_name[{{ data.network_id }}]" data-attribute="network_name" data-placeholder="<?php esc_attr_e( 'Choose social networks&hellip;', 'easy-social-sharing' ); ?>">
					<?php
						foreach ( ess_get_core_supported_social_networks() as $network_id => $network_label ) {
							echo '<option value="' . esc_attr( $network_id ) . '">' . esc_attr( $network_label ) . '</li>';
						}
					?>
				</select>
				<div class="row-actions">
					<a class="ess-social-network-cancel-edit" href="#"><?php _e( 'Cancel changes', 'easy-social-sharing' ); ?></a>
				</div>
			</div>
		</td>
		<td class="ess-social-network-description">
			<div class="view">
				<# if ( data.network_desc ) { #>
					{{ data.network_desc }}
				<# } else { #>
					<mark class="dash">&ndash;</mark>
				<# } #>
			</div>
			<div class="edit"><input type="text" name="network_desc[{{ data.network_id }}]" data-attribute="network_desc" value="{{ data.network_desc }}" placeholder="<?php esc_attr_e( 'Description for this network', 'easy-social-sharing' ); ?>" /></div>
		</td>
		<td class="ess-social-network-min-count">
			<div class="view">
				{{ data.network_count }}
			</div>
			<div class="edit"><input type="number" min="0" step="1" name="network_count[{{ data.network_id }}]" data-attribute="network_count" value="{{ data.network_count }}" /></div>
		</td>
		<td class="ess-social-network-api-support">
			<div class="view">
				{{{ data.api_support_icon }}}
			</div>
			<div class="edit">
				<mark class="dash tips" data-disabled-tip="<?php esc_attr_e( 'Save changes to preview API Support to this network', 'easy-social-sharing' ); ?>">&ndash;</mark>
			</div>
		</td>
	</tr>
</script>
