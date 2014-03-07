<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Account' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Account {

		private $page = 'dfrapi-account';
		private $key;

		public function __construct() {
			$this->key = 'dfrapi_account';
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'init', array( &$this, 'load_settings' ) );
		}
		
		function load_settings() {	
			$this->options = (array) get_option( $this->key );
			if ( isset( $this->options[0] ) && empty( $this->options[0] ) ) {
				$this->options = $status = dfrapi_api_get_status();
				if ( !array_key_exists( 'dfrapi_api_error', $status ) ) {
					update_option( $this->key, $status );
				}
			}
		}
	
		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Account', DFRAPI_DOMAIN ), 
				__( 'Account', DFRAPI_DOMAIN ), 
				'manage_options', 
				$this->key,
				array( $this, 'output' ) 
			);
		}

		function output() {
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>Datafeedr API ' . dfrapi_setting_pages( $this->page ) . '</h2>';
			settings_fields( $this->page );
			do_settings_sections( $this->page);
			echo '</div>';
		}

		function register_settings() {
			
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			
			// Current Plan
			add_settings_section( 'current_plan', __( 'Current Plan', DFRAPI_DOMAIN ), array( &$this, 'section_current_plan_desc' ), $this->page );
						
			// Only show the following if there is no Error.
			if ( !array_key_exists( 'dfrapi_api_error', $this->options ) ) {
			
				// Current Usage
				add_settings_section( 'current_usage', __( 'Current Usage', DFRAPI_DOMAIN ), array( &$this, 'section_current_usage_desc' ), $this->page );
			}
			
			// Account Links
			add_settings_section( 'account', __( 'Account Links', DFRAPI_DOMAIN ), array( &$this, 'section_account_desc' ), $this->page );
		}
		
		function section_current_plan_desc() {
			
			$plans = dfrapi_get_membership_plans();
			$plan_name = '';
			if ( $this->options['plan_id'] > 0 ) {
				$plan_name .= $plans[$this->options['plan_id']];
				if ( $this->options['plan_id'] != 10250000 ) {
					$plan_name .= ' (';
					$plan_name .= '<a href="' . dfrapi_user_pages( 'change' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank" class="dfrapi_plan_link">' . __( 'Upgrade', DFRAPI_DOMAIN ) . '</a>';
					$plan_name .= ')';
				}
			} else {
				$plan_name .= '<em>';
				$plan_name .= __( 'None', DFRAPI_DOMAIN );
				$plan_name .= '</em> (';
				$plan_name .= '<a href="' . dfrapi_user_pages( 'signup' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank" class="dfrapi_plan_link">' . __( 'Reactivate your subscription', DFRAPI_DOMAIN ) . '</a>';
				$plan_name .= ')';
			}
			
			echo '
			<table class="widefat account_table" cellspacing="0">
				<tbody>
					<tr class="alternate">
						<td class="row-title">' . __( 'Plan name', DFRAPI_DOMAIN ) . '</td>
						<td class="desc">' . $plan_name . '</td>
					</tr>
			';
			
			if ( !array_key_exists( 'dfrapi_api_error', $this->options ) ) {
				echo '	
					<tr>
						<td class="row-title">' . __( 'Requests per month (RPM)', DFRAPI_DOMAIN ) . '</td>
						<td class="desc">' . number_format( $this->options['max_requests'] ) . '</td>
					</tr>
					<tr class="alternate">
						<td class="row-title">' . __( 'Products per request (PPR)', DFRAPI_DOMAIN ) . '</td>
						<td class="desc">' . number_format( $this->options['max_length'] ) . '</td>
					</tr>
				';
			}
			
			echo '
				</tbody>
			</table>
			';
		}
		
		function section_current_usage_desc() {
			
			// Percent of API Requests Used
			$percent_api_requests_users = '';
			if ( $this->options['max_requests'] > 0 ) {
				$percent_api_requests_users .= floor ( ( intval( $this->options['request_count'] ) / intval( $this->options['max_requests'] ) * 100 ) );
			} else {
				$percent_api_requests_users .= 0;
			}
			$percent_api_requests_users .= '%';
			
			// Your API requests will reset on
			$reset_date = '';
			$today = date('j');
			$num_days = date('t');
			if ( $this->options['bill_day'] > $num_days ) {
				$bill_day = $num_days;
			} else {
				$bill_day = $this->options['bill_day'];
			}
			if ( $bill_day == 0 ) {
				$reset_date .= '<em>' . __( 'Never', DFRAPI_DOMAIN ) . '</em>';
			} elseif ( $today >= $bill_day ) {
				$reset_date .= date('F', strtotime('+1 month')) . ' ' . $bill_day . ', ' . date('Y', strtotime('+1 month'));
			} else {
				$reset_date .= date('F') . ' ' . $bill_day . ', ' . date('Y');
			}		

			echo '
			<table class="widefat account_table" cellspacing="0">
				<tbody>
					<tr class="alternate">
						<td class="row-title">' . __( 'API requests used this period', DFRAPI_DOMAIN ) . '</td>
						<td class="desc">' . number_format( $this->options['request_count'] ) . '</td>
					</tr>
					<tr>
						<td class="row-title">' . __( 'API requests remaining this period', DFRAPI_DOMAIN ) . '</td>
						<td class="desc">' . number_format( $this->options['max_requests'] - $this->options['request_count'] ) . '</td>
					</tr>
					<tr class="alternate">
						<td class="row-title">' . __( 'Percent of API requests used this period', DFRAPI_DOMAIN ) . '</td>
						<td class="desc">' . $percent_api_requests_users . '</td>
					</tr>
					<tr>
						<td class="row-title">' . __( 'Your API requests will reset on', DFRAPI_DOMAIN ) . '</td>
						<td class="desc">' . $reset_date . '</td>
					</tr>
				</tbody>
			</table>
			';			
		}
	
		function section_account_desc() {
			echo '<p><a href="' . dfrapi_user_pages( 'summary' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank">' . __( 'View your Datafeedr account', DFRAPI_DOMAIN ) . '</a></p>';
			echo '<p><a href="' . dfrapi_user_pages( 'change' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank">' . __( 'Upgrade your plan', DFRAPI_DOMAIN ) . '</a></p>';
		}
		
		function validate( $input ) {
			return $input;
		}
		
	} // class Dfrapi_Account

} // class_exists check
