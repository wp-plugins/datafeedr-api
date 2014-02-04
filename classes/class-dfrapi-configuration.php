<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Configuration' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Configuration {

		private $page = 'dfrapi-configuration';
		private $key;
		private $account;

		public function __construct() {
			$this->key = 'dfrapi_configuration';
			$this->account = (array) get_option( 'dfrapi_account', array( 'max_length' => 50 ) );
			add_action( 'init', array( &$this, 'load_settings' ) );
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}
	
		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Datafeedr API Configuration', DFRAPI_DOMAIN ), 
				__( 'Configuration', DFRAPI_DOMAIN ), 
				'manage_options', 
				'dfrapi',
				array( $this, 'output' ) 
			);
		}
	
		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && 'dfrapi' == $_GET['page'] ) {
				echo '<div class="updated"><p>';
				_e( 'Configuration successfully updated!', DFRAPI_DOMAIN );
				echo '</p></div>';
			}
		}

		function output() {
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>' . dfrapi_setting_pages( $this->page ) . '</h2>';
			echo '<form method="post" action="options.php">';
			wp_nonce_field( 'update-options' );
			settings_fields( $this->page );
			do_settings_sections( $this->page);
			submit_button();
			echo '</form>';		
			echo '</div>';
		}
		
		function load_settings() {
			$this->options = (array) get_option( $this->key );
			$this->options = array_merge( 
				array(
					'access_id' => '',
					'secret_key' => '',
					'transport_method' => 'curl',
				), 
				$this->options 
			);
		}
	
		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'api_settings', __( 'API Settings', DFRAPI_DOMAIN ), array( &$this, 'section_api_settings_desc' ), $this->page );
			add_settings_field( 'access_id', __( 'API Access ID', DFRAPI_DOMAIN ), array( &$this, 'field_access_id' ), $this->page, 'api_settings' );
			add_settings_field( 'secret_key',  __( 'API Secret Key', DFRAPI_DOMAIN ), array( &$this, 'field_secret_key' ), $this->page, 'api_settings' );
			add_settings_field( 'transport_method',  __( 'Transport Method', DFRAPI_DOMAIN ), array( &$this, 'field_transport_method' ), $this->page, 'api_settings' );
		}
	
		function section_api_settings_desc() { 
			echo __( 'Add your ', DFRAPI_DOMAIN );
			echo ' <a href="'.DFRAPI_KEYS_URL.'" target="_blank" title="' . __( 'Get your Datafeedr API Keys', DFRAPI_DOMAIN ) . '">';
			echo __( 'Datafeedr API Keys', DFRAPI_DOMAIN );
			echo '</a>.'; 
		}
	
		function section_update_desc() { 
			echo __( 'Configure Product Set updates.', DFRAPI_DOMAIN );
		}

		function section_default_search_filters_desc() {
			echo __( 'Set up filters that are used in all of your searches.  This can be changed on a Product Set basis.', DFRAPI_DOMAIN );		
		}

		function field_access_id() {
			?>
			<input type="text" class="regular-text" name="<?php echo $this->key; ?>[access_id]" value="<?php echo esc_attr( $this->options['access_id'] ); ?>" />
			<?php
		}

		function field_secret_key() {
			?>
			<input type="text" class="regular-text" name="<?php echo $this->key; ?>[secret_key]" value="<?php echo esc_attr( $this->options['secret_key'] ); ?>" />
			<?php
		}
		
		function field_transport_method() {
			?>
			<select id="transport_method" name="<?php echo $this->key; ?>[transport_method]">
				<option value="curl" <?php selected( $this->options['transport_method'], 'curl', true ); ?>><?php _e( 'CURL', DFRAPI_DOMAIN ); ?></option>
				<option value="file" <?php selected( $this->options['transport_method'], 'file', true ); ?>><?php _e( 'File', DFRAPI_DOMAIN ); ?></option>
				<option value="socket" <?php selected( $this->options['transport_method'], 'socket', true ); ?>><?php _e( 'Socket', DFRAPI_DOMAIN ); ?></option>
			</select>
			<p class="description"><?php _e( 'If you\'re not sure, use CURL.', DFRAPI_DOMAIN ); ?></p>
			<?php
		}

		function validate( $input ) {
			
			if ( !isset( $input ) || !is_array( $input ) || empty( $input ) ) { return $input; }
			
			$new_input = array();
			
			foreach( $input as $key => $value ) {
			
				// Validate "access_id"
				if ( $key == 'access_id' ) {
					$new_input['access_id'] = trim( $value );
				}
			
				// Validate "secret_key"
				if ( $key == 'secret_key' ) {
					$new_input['secret_key'] = trim( $value );
				}
			
				// Validate "transport_method"
				if ( $key == 'transport_method' ) {
					$new_input['transport_method'] = trim( $value );
				}
						
			} // foreach
			
			return $new_input;
		}
		
	} // class Dfrapi_Configuration

} // class_exists check
