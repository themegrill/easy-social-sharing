<?php
/**
 * EasySocialSharing Analytics Settings
 *
 * @class    ESS_Settings_Analytics
 * @version  1.0.0
 * @package  EasySocialSharing/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ESS_Settings_Analytics' ) ) :

	/**
	 * ESS_Settings_Analytics Class
	 */
	class ESS_Settings_Analytics extends ESS_Settings_Page {

		/**
		 * The chart interval.
		 *
		 * @var int
		 */
		public $chart_interval;

		/**
		 * Group by SQL query.
		 *
		 * @var string
		 */
		public $group_by_query;

		/**
		 * Group chart item by day or month.
		 *
		 * @var string
		 */
		public $chart_groupby;

		/**
		 * The start date of the report.
		 *
		 * @var int timestamp
		 */
		public $start_date;

		/**
		 * The end date of the report.
		 *
		 * @var int timestamp
		 */
		public $end_date;

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id    = 'analytics';
			$this->label = __( 'Analytics', 'easy-social-sharing' );

			add_filter( 'easy_social_sharing_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'easy_social_sharing_settings_' . $this->id, array( $this, 'output' ) );
 		}

		/**
		 * Output the charts.
		 */
		public function output_chart_screen() {
			$this->output_report();
		}

		/**
		 * output the report.
		 */
		public function output_report() {
			?>
			<div class="ess-pro-blankSlate">
				<h2 class="ess-pro-BlankState-message"><?php _e( 'This feature is available in pro version of Easy Social Sharing plugin.', 'easy-social-sharing' ) ?></h2>
				<a target="_blank" class="ess-pro-BlankState-cta button button-primary button-hero activate-now" href="https://themegrill.com/plugins/easy-social-sharing-pro/" aria-label="<?php esc_attr_e( 'Buy pro', 'easy-social-sharing' ); ?>"><?php esc_html_e( 'Buy pro', 'easy-social-sharing' ); ?></a>
			</div>
			<?php
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			global $current_section, $hide_save_button;
			if ( '' === $current_section ) {
				$hide_save_button = true;
				$this->output_chart_screen();
			} elseif ( 'options' === $current_section ) {
				$settings = $this->get_settings();
				ESS_Admin_Settings::output_fields( $settings );
			}
		}
	}

endif;

return new ESS_Settings_Analytics();
