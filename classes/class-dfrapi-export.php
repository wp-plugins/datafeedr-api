<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Export' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Export {

		private $page = 'dfrapi-export';
		private $key;

		public function __construct() {
			$this->key = 'dfrapi_export';
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
	
		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Export &#8212; Datafeedr Product Sets', DFRAPI_DOMAIN ), 
				__( 'Export', DFRAPI_DOMAIN ), 
				'manage_options', 
				$this->key,
				array( $this, 'output' ) 
			);
		}

		function output() {
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>' . dfrapi_setting_pages( $this->page ) . '</h2>';
			settings_fields( $this->page );
			do_settings_sections( $this->page);
			echo '</div>';
		}
	
		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'export_network_data', __( 'Network Data', DFRAPI_DOMAIN ), array( &$this, 'section_export_network_data' ), $this->page );
			add_settings_section( 'export_merchant_data', __( 'Merchant Data', DFRAPI_DOMAIN ), array( &$this, 'section_export_merchant_data' ), $this->page );
		}
		
		function section_export_network_data() {
			$network_settings = (array) get_option( 'dfrapi_networks' );
			echo '<p>' . __( 'To use the same selection of networks on a different website, export this store\'s network data by copying the code below.' ) . '</p>';
			echo '<textarea rows="4" cols="100%" onclick="this.focus();this.select()" readonly="readonly">';
			echo '[NETWORKS]' . serialize( $network_settings ) . '[/NETWORKS]';
			echo '</textarea>';
			echo '<p class="description">' . __( 'Click within the box to select all of your networks and affiliate IDs.' ) . '</p>';
		}
		
		function section_export_merchant_data() {
			$merchant_settings = (array) get_option( 'dfrapi_merchants' );
			echo '<p>' . __( 'To use the same selection of merchants on a different website, export this store\'s merchant data by copying the code below.' ) . '</p>';
			echo '<textarea rows="4" cols="100%" onclick="this.focus();this.select()" readonly="readonly">';
			echo '[MERCHANTS]' . serialize( $merchant_settings ) . '[/MERCHANTS]';
			echo '</textarea>';
			echo '<p class="description">' . __( 'Click within the box to select all of your merchant IDs.' ) . '</p>';
		}
		
		function validate( $input ) {
			return $input;
		}
		
	} // class Dfrapi_Export

} // class_exists check
