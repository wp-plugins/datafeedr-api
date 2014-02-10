<?php
/*
Plugin Name: Datafeedr API
Version: 0.9.5
Plugin URI: https://v4.datafeedr.com
Description: Connect to the Datafeedr API and configure your API settings.
Author: Datafeedr
Author URI: https://v4.datafeedr.com
License: GPL v3
Requires at least: 3.8
Tested up to: 3.8.1

Datafeedr API Plugin
Copyright (C) 2013, Datafeedr - api@datafeedr.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define constants.
 */
define( 'DFRAPI_VERSION', 			'0.9.5' );
define( 'DFRAPI_URL', 				plugin_dir_url( __FILE__ ) );
define( 'DFRAPI_PATH', 				plugin_dir_path( __FILE__ ) );
define( 'DFRAPI_BASENAME', 			plugin_basename( __FILE__ ) );
define( 'DFRAPI_DOMAIN', 			'datafeedr-api' );
define( 'DFRAPI_HOME_URL', 			'https://v4.datafeedr.com' );
define( 'DFRAPI_KEYS_URL', 			'https://v4.datafeedr.com/keys' );
define( 'DFRAPI_USER_URL', 			'https://v4.datafeedr.com/user' );
define( 'DFRAPI_HELP_URL', 			'https://v4.datafeedr.com/support' );
define( 'DFRAPI_BUG_REPORTS_URL',	'https://datafeedr.desk.com/customer/portal/topics/610933-bug-reports/questions?t=417957' );
define( 'DFRAPI_QNA_URL',			'https://datafeedr.desk.com/customer/portal/topics/608564-customer-support/questions' );
define( 'DFRAPI_DOCS_URL',			'https://datafeedr.desk.com/customer/portal/topics/608564-customer-support/articles' );
define( 'DFRAPI_REPORT_BUG_URL',	'https://datafeedr.desk.com/customer/portal/questions/new?t=417957&qna[kb_topic_id]=610933' );
define( 'DFRAPI_ASK_QUESTION_URL',	'https://datafeedr.desk.com/customer/portal/questions/new?t=417957&qna[kb_topic_id]=608564' );
define( 'DFRAPI_EMAIL_US_URL',		'https://datafeedr.desk.com/customer/portal/emails/new' );
/**
 * Require WP 3.8+
 */
add_action( 'admin_init', 'dfrapi_wp_version_check' );
function dfrapi_wp_version_check() {
	$version = get_bloginfo( 'version' );
	if ( version_compare( $version, '3.8', '<' ) ) {
		deactivate_plugins( DFRAPI_BASENAME );
	}
}

/**
 * Notify user that this plugin is deactivated.
 */
add_action( 'admin_notices', 'dfrapi_wp_version_notice' );
function dfrapi_wp_version_notice() {
	$version = get_bloginfo( 'version' );
	if ( version_compare( $version, '3.8', '<' ) ) {
		echo '<div class="error"><p>' . __( 'The ', DFRAPI_DOMAIN ) . '<strong><em>';
		_e( 'Datafeedr API', DFRAPI_DOMAIN );
		echo '</em></strong>';
		_e( ' plugin could not be activated because it requires WordPress version 3.8 or greater. Please upgrade your installation of WordPress.', DFRAPI_DOMAIN );
		echo '</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

/**
 * Load files for all pages.
 */
require_once( DFRAPI_PATH . 'functions/functions.php' );				// Core functions.
require_once( DFRAPI_PATH . 'libraries/datafeedr.php' ); 				// Load the Datafeedr API Library.
require_once( DFRAPI_PATH . 'classes/class-dfrapi-searchform.php' );	// Product search form.
require_once( DFRAPI_PATH . 'functions/api.php' );						// API specific helper functions.

/**
 * Load files only if we're in the admin section of the site.
 */
if ( is_admin() ) {
	require_once ( DFRAPI_PATH . 'classes/class-dfrapi-initialize.php' );
}