<?php
/**
 * Installation related functions and actions.
 *
 * @class    ESS_Install
 * @version  1.3.0
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

	/** @var array DB updates and callbacks that need to be run per version */
	private static $db_updates = array(
		'1.0.0' => array(
			'ess_update_100_db_version',
		),
		'1.0.1' => array(
			'ess_update_101_db_version',
		),
		'1.2.0' => array(
			'ess_update_120_social_networks',
			'ess_update_120_delete_options',
			'ess_update_120_db_version',
		),
		'1.3.0' => array(
			'ess_update_130_db_version',
			'ess_update_130_social_statics',
		),
	);

	/** @var object Background update class */
	private static $background_updater;

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_action( 'in_plugin_update_message-easy-social-sharing/easy-social-sharing.php', array( __CLASS__, 'in_plugin_update_message' ) );
		add_filter( 'plugin_action_links_' . ESS_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Init background updates.
	 */
	public static function init_background_updater() {
		include_once( 'class-ess-background-updater.php' );
		self::$background_updater = new ESS_Background_Updater();
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
			ESS_Admin_Notices::add_notice( 'update' );
		}
		if ( ! empty( $_GET['force_update_easy_social_sharing'] ) ) {
			do_action( 'wp_ess_updater_cron' );
			wp_safe_redirect( admin_url( 'options-general.php?page=easy-social-sharing' ) );
		}
	}

	/**
	 * Install ESS.
	 */
	public static function install() {
		global $wpdb;

		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'ess_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'ess_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		if ( ! defined( 'ESS_INSTALLING' ) ) {
			define( 'ESS_INSTALLING', true );
		}

		// Ensure needed classes are loaded
		include_once( 'admin/class-ess-admin-notices.php' );

		self::create_options();
		self::create_tables();
		self::create_networks();

		// Queue upgrades wizard
		$current_ess_version = get_option( 'easy_social_sharing_version', null );
		$current_db_version  = get_option( 'easy_social_sharing_db_version', null );

		ESS_Admin_Notices::remove_all_notices();

		// No versions? This is a new install :)
		if ( is_null( $current_ess_version ) && is_null( $current_db_version ) && apply_filters( 'easy_social_sharing_enable_setup_wizard', true ) ) {
			set_transient( '_ess_activation_redirect', 1, 30 );
		}

		if ( ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {
			ESS_Admin_Notices::add_notice( 'update' );
		} else {
			self::update_db_version();
		}

		self::update_ess_version();

		delete_transient( 'ess_installing' );

		// Flush rules after install
		do_action( 'easy_social_sharing_flush_rewrite_rules' );

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
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {
		$current_db_version = get_option( 'easy_social_sharing_db_version' );
		$update_queued      = false;

		foreach ( self::$db_updates as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					self::$background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			self::$background_updater->save()->dispatch();
		}
	}

	/**
	 * Update DB version to current.
	 *
	 * @param string $version
	 */
	public static function update_db_version( $version = null ) {
		delete_option( 'easy_social_sharing_db_version' );
		add_option( 'easy_social_sharing_db_version', is_null( $version ) ? ESS()->version : $version );
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
	 * Set up the database table which the plugin need to function.
	 *
	 * Tables:
	 *    ess_social_networks - Table for storing social networks data.
	 *    ess_social_statistics - Table for storing social statistics data.
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::get_schema() );
	}

	/**
	 * Get Table schema.
	 *
	 * A note on indexes; Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
	 * As of WordPress 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
	 * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
	 *
	 * Changing indexes may cause duplicate index notices in logs due to https://core.trac.wordpress.org/ticket/34870 but dropping
	 * indexes first causes too much load on some servers/larger DB.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$charset_collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$charset_collate = $wpdb->get_charset_collate();
		}

		$tables = "
CREATE TABLE {$wpdb->prefix}ess_social_statistics (
  id bigint(20) NOT NULL auto_increment,
  network_name varchar(50) NOT NULL,
  sharing_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  post_id bigint(20) NOT NULL,
  ip_info text NOT NULL,
  ip_address VARCHAR(200) NOT NULL,
  share_location VARCHAR(50) NOT NULL,
  share_url text NULL,
  latest_count  bigint(20) NOT NULL,
  PRIMARY KEY (id)
) $charset_collate;
CREATE TABLE {$wpdb->prefix}ess_social_networks (
  network_id bigint(20) NOT NULL auto_increment,
  network_name varchar(200) NOT NULL,
  network_desc longtext NULL,
  network_order bigint(20) NOT NULL,
  network_count bigint(20) NOT NULL DEFAULT 0,
  is_api_support tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY  (network_id),
  UNIQUE KEY network_name (network_name(64))
) $charset_collate;
		";

		return $tables;
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
	 *
	 * @param  string $content
	 * @param  string $new_version
	 *
	 * @return string
	 */
	private static function parse_update_notice( $content, $new_version ) {
		// Output Upgrade Notice.
		$matches        = null;
		$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( ESS_VERSION ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

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
	 *
	 * @param  array $actions
	 *
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
	 *
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 *
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

	/**
	 * Create default networks
	 */
	public static function create_networks() {
		global $wpdb;

		$all_network_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM  {$wpdb->prefix}ess_social_networks WHERE network_id > %d", 0 ) );

		if ( count( $all_network_data ) > 0 ) {
			return;
		}

		$default_networks       = ess_get_default_networks();
		$api_supported_networks = ess_get_share_networks_with_api_support();

		foreach ( $default_networks as $network_index => $network ) {
			$is_api_support = in_array( $network, $api_supported_networks ) ? 1 : 0;
			$network_data   = array(
				'network_name'   => $network,
				'network_desc'   => ucwords( $network ),
				'network_order'  => ( $network_index + 1 ),
				'network_count'  => 0,
				'is_api_support' => $is_api_support,
			);

			$wpdb->insert( $wpdb->prefix . 'ess_social_networks', $network_data );
		}
	}
}

ESS_Install::init();
