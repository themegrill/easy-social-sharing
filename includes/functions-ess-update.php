<?php
/**
 * EasySocialSharing Updates
 *
 * Function for updating data, used by the background updater.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  EasySocialSharing/Functions
 * @version  1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ess_update_100_db_version() {
	ESS_Install::update_db_version( '1.0.0' );
}

function ess_update_101_db_version() {
	ESS_Install::update_db_version( '1.0.1' );
}

function ess_update_120_social_networks() {
}

function ess_update_120_db_version() {
	ESS_Install::update_db_version( '1.2.0' );
}

