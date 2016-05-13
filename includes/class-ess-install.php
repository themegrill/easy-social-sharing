<?php
/**
 * Installation related functions and actions.
 *
 * @class    ESS_Install
 * @version  1.0.0
 * @package  EasySocialSharing/Classes
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ESS_Install Class.
 */
class ESS_Install {

	/** @var array DB updates that need to be run */
	private static $db_updates = array();

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_action( 'in_plugin_update_message-easy-social-sharing/easy-social-sharing.php', array( __CLASS__, 'in_plugin_update_message' ) );
		add_filter( 'plugin_action_links_' . ESS_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Check EasySocialSharing version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'easy_social_sharing_version' ) !== ESS()->version ) {
			self::install();
			do_action( 'easy_social_sharing_updated' );
		}
	}

	/**
	 * Install actions when a update button is clicked within the admin area.
	 *
	 * This function is hooked into admin_init to affect admin only.
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_easy_social_sharing'] ) ) {
			self::update();
			ESS_Admin_Notices::remove_notice( 'update' );
			add_action( 'admin_notices', array( __CLASS__, 'updated_notice' ) );
		}
	}

	/**
	 * Show notice stating update was successful.
	 */
	public static function updated_notice() {
		?>
		<div id="message" class="updated easy-social-sharing-message ess-connect">
			<p><?php _e( 'Easy Social Sharing data update complete. Thank you for updating to the latest version!', 'easy-social-sharing' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Install ESS.
	 */
	public static function install() {
		global $wpdb;

		if ( ! defined( 'ESS_INSTALLING' ) ) {
			define( 'ESS_INSTALLING', true );
		}

		// Ensure needed classes are loaded
		include_once( 'admin/class-ess-admin-notices.php' );

		self::create_options();

		// Queue upgrades/setup wizard
		$current_ess_version = get_option( 'easy_social_sharing_version', null );
		$current_db_version  = get_option( 'easy_social_sharing_db_version', null );
		$major_ess_version   = substr( ESS()->version, 0, strrpos( ESS()->version, '.' ) );

		ESS_Admin_Notices::remove_all_notices();

		if ( ! empty( self::$db_updates ) && ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {
			ESS_Admin_Notices::add_notice( 'update' );
		} else {
			self::update_db_version();
		}

		self::update_ess_version();

		// Flush rules after install
		flush_rewrite_rules();

		/*
		 * Deletes all expired transients. The multi-table delete syntax is used
		 * to delete the transient record from table a, and the corresponding
		 * transient_timeout record from table b.
		 *
		 * Based on code inside core's upgrade_network() function.
		 */
		$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d";
		$wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', time() ) );

		// Trigger action
		do_action( 'easy_social_sharing_installed' );
	}

	/**
	 * Update ESS version to current.
	 */
	private static function update_ess_version() {
		delete_option( 'easy_social_sharing_version' );
		add_option( 'easy_social_sharing_version', ESS()->version );
	}

	/**
	 * Update DB version to current.
	 */
	private static function update_db_version( $version = null ) {
		delete_option( 'easy_social_sharing_db_version' );
		add_option( 'easy_social_sharing_db_version', is_null( $version ) ? ESS()->version : $version );
	}

	/**
	 * Handle updates.
	 */
	private static function update() {
		if ( ! defined( 'ESS_UPDATING' ) ) {
			define( 'ESS_UPDATING', true );
		}

		$current_db_version = get_option( 'easy_social_sharing_db_version' );

		foreach ( self::$db_updates as $version => $updater ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				include( $updater );
				self::update_db_version( $version );
			}
		}

		self::update_db_version();
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 */
	private static function create_options() {
		// Include settings so that we can run through defaults
		include_once( 'admin/class-ess-admin-settings.php' );

		$settings = ESS_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			if ( ! method_exists( $section, 'get_settings' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 */
	public static function in_plugin_update_message( $args ) {
		$transient_name = 'ess_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
			$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/social-sharing/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = self::parse_update_notice( $response['body'], $args['new_version'] );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Parse update notice from readme file
	 * @param  string $content
	 * @param  string $new_version
	 * @return string
	 */
	private static function parse_update_notice( $content, $new_version ) {
		// Output Upgrade Notice.
		$matches        = null;
		$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( ESS_VERSION ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

			// Check the latest stable version and ignore trunk.
			if ( $version === $new_version && version_compare( ESS_VERSION, $version, '<' ) ) {

				$upgrade_notice .= '<div class="ess_plugin_upgrade_notice">';

				foreach ( $notices as $index => $line ) {
					$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) );
				}

				$upgrade_notice .= '</div> ';
			}
		}

		return wp_kses_post( $upgrade_notice );
	}

	/**
	 * Display action links in the Plugins list table.
	 * @param  array $actions
	 * @return array
	 */
	public static function plugin_action_links( $actions ) {
		$new_actions = array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=easy-social-sharing' ) . '" title="' . esc_attr( __( 'View Easy Social Sharing Settings', 'easy-social-sharing' ) ) . '">' . __( 'Settings', 'easy-social-sharing' ) . '</a>',
		);

		return array_merge( $new_actions, $actions );
	}

	/**
	 * Display row meta in the Plugins list table.
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @return array
	 */
	public static function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( $plugin_file == ESS_PLUGIN_BASENAME ) {
			$new_plugin_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'easy_social_sharing_docs_url', 'http://docs.themegrill.com/easy-social-sharing/' ) ) . '" title="' . esc_attr( __( 'View Social Sharing Documentation', 'easy-social-sharing' ) ) . '">' . __( 'Docs', 'easy-social-sharing' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'easy_social_sharing_support_url', 'http://themegrill.com/support-forum/' ) ) . '" title="' . esc_attr( __( 'Visit Free Customer Support Forum', 'easy-social-sharing' ) ) . '">' . __( 'Free Support', 'easy-social-sharing' ) . '</a>',
			);

			return array_merge( $plugin_meta, $new_plugin_meta );
		}

		return (array) $plugin_meta;
	}
}

ESS_Install::init();
