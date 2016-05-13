<?php
/**
 * EasySocialSharing Uninstall
 *
 * Uninstalls the plugin deletes options.
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

// Delete options.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'easy_social_sharing\_%';" );
