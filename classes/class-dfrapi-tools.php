<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Tools' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Tools {

		private $page = 'dfrapi-tools';
		private $key;

		public function __construct() {
			$this->key = 'dfrapi_tools';
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );		
		}
	
		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Tools &#8212; Datafeedr API', DFRAPI_DOMAIN ), 
				__( 'Tools', DFRAPI_DOMAIN ), 
				'manage_options', 
				$this->key,
				array( $this, 'output' ) 
			);
		}
		
		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && isset( $_GET['page'] ) && $this->key == $_GET['page'] ) {
				echo '<div class="updated"><p>';
				_e( 'Updated!', DFRAPI_DOMAIN );
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
	
		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
		
			// Delete transient data.
			add_settings_section( 'delete_transient_data', __( 'Delete Cached API Data', DFRAPI_DOMAIN ), array( &$this, 'section_delete_transient_data_desc' ), $this->page );
			add_settings_field( 'delete_transient_data_checkbox', __( 'Delete Cached API Data', DFRAPI_DOMAIN ), array( &$this, 'field_delete_transient_data_checkbox' ), $this->page, 'delete_transient_data' );
		}
	
		function section_delete_transient_data_desc() { 
			echo __( 'Check the box below and click [Save Changes] to delete all cached API data. Deleting cached data will not affect your store, however, it <strong>will require multiple API requests</strong> in order to re-build the data. Typically, you delete cached data only when Datafeedr Support instructs you to do so.', DFRAPI_DOMAIN );
		}

		function field_delete_transient_data_checkbox() {
			?>
			<input type="checkbox" name="<?php echo $this->key; ?>[delete_transient_data_checkbox]" value="on" /> <?php _e( 'Yes', DFRAPI_DOMAIN ); ?>
			<?php
		}
		
		function validate( $input ) {
		
			if ( !isset( $input ) || !is_array( $input ) || empty( $input ) ) { return $input; }
			
			foreach( $input as $key => $value ) {
			
				// Delete transient data.
				if ( $key == 'delete_transient_data_checkbox' && $value == 'on' ) {
					// Only delete if user has API requests remaining. This is because we'll need to make 1 request to rebuild 'dfrapi_account'.
					$status = dfrapi_api_get_status();
					if ( !array_key_exists( 'dfrapi_api_error', $status ) ) {
						delete_option( 'dfrapi_account' );
						$transient_options = get_option( 'dfrapi_transient_whitelist' );
						if ( !empty( $transient_options ) ) {
							foreach ( $transient_options as $name ) {
								delete_transient( $name );
							}
						}
						// Update account status immediately in case there are not enough API
						// requests remaining in order to do so later.
						$status = dfrapi_api_get_status();
						update_option( 'dfrapi_account', $status );
					}
				} // END Delete transient data.
		
			} // END foreach( $input as $key => $value ) {
		}
		
	} // class Dfrapi_Tools

} // class_exists check
