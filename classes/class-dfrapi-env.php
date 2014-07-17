<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Env' ) ) {

	/**
	 * Check environment and print errors..
	 */
	class Dfrapi_Env {

		function __construct() { 
		
			// Cascading Errors
			if ( !self::api_keys_exist() ) {
				dfrapi_admin_messages( 'missing_api_keys' );
			} elseif ( !self::network_is_selected() ) {
				dfrapi_admin_messages( 'missing_network_ids' );
			} elseif ( !self::merchant_is_selected() ) {
				dfrapi_admin_messages( 'missing_merchant_ids' );
			}
		
			// Non-cascading Errors
			if ( self::usage_over_90_percent() ) {
				dfrapi_admin_messages( 'usage_over_90_percent' );
			}
			
			if ( self::missing_affiliate_ids() ) {
				dfrapi_admin_messages( 'missing_affiliate_ids' );
			}
			
			if ( self::check_gmt_time() ) {
				dfrapi_admin_messages( 'database_rotation' );
			}
			
			if ( self::unapproved_zanox_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_zanox_merchants' );
			}
		}

		static function api_keys_exist() {
			
			$configuration = (array) get_option( 'dfrapi_configuration' );
			$access_id = false;
			$secret_key = false;
			
			if ( isset( $configuration['access_id'] ) && ( $configuration['access_id'] != '' ) ) {
				$access_id = $configuration['access_id'];
			}
	
			if ( isset( $configuration['secret_key'] ) && ( $configuration['secret_key'] != '' ) ) {
				$secret_key = $configuration['secret_key'];
			}
			
			if ( $access_id && $secret_key ) {
				return true;
			}
			
			return false;
		}
	
		static function network_is_selected() {
			$networks = (array) get_option( 'dfrapi_networks' );
			if ( !empty( $networks['ids'] ) ) {
				return true;
			}
			return false;
		}
	
		static function merchant_is_selected() {
			$merchants = (array) get_option( 'dfrapi_merchants' );
			if ( !empty( $merchants['ids'] ) ) {
				return true;
			}
			return false;
		}
		
		static function usage_over_90_percent() {
			$percentage = dfrapi_get_api_usage_percentage();
			if ( $percentage >= 90 ) {
				return true;
			}
			return false;
		}
	
		static function missing_affiliate_ids() {
			$networks = get_option( 'dfrapi_networks', array() );
			if ( !empty( $networks ) ) {
				foreach ( $networks['ids'] as $network ) {
					if ( empty( $network['aid'] ) ) {
						return true;
					}
				}
			}
			return false;
		}
	
		static function check_gmt_time() {
			$gmt_time = gmdate( 'Gis' );
			if ( $gmt_time > 80000 && $gmt_time < 82000 ) { 
				return true;
			}
			return false;
		}
		
		static function unapproved_zanox_merchants_exist() {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_value = 'dfrapi_unapproved_zanox_merchant' ", OBJECT );
			if ( !empty( $results ) ) {
				return TRUE;
			}
			return FALSE;
		}
		
	} // class Dfrapi_Env

} // class_exists check
