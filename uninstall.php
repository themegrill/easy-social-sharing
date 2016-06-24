<?php
/**
 * EasySocialSharing Uninstall
 *
 * Uninstalls the plugin deletes tables and options.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  EasySocialSharing/Uninstaller
 * @version  1.0.0
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Drop Tables.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ess_social_networks" );

// Delete options.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'easy_social_sharing\_%';" );
