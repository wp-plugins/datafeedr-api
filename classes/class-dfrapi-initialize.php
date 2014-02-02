<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'Dfrapi_Initialize' ) ) {


class Dfrapi_Initialize {

	public function __construct() {

        // Core admin functions.
		require_once( DFRAPI_PATH . 'functions/admin-functions.php' );					
	
		// Load required classes.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-env.php' );			// Checks environment for any problems.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-configuration.php' );	// Configuration page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-networks.php' );		// Networks page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-merchants.php' );		// Merchants page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-tools.php' );			// Tools page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-export.php' );		// Export page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-import.php' );		// Import page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-account.php' );		// Account page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-help.php' );			// Help tabs.
		
		// Hooks
		add_action( 'admin_enqueue_scripts', 	array( $this, 'load_css' ) );
		add_action( 'admin_enqueue_scripts', 	array( $this, 'load_js' ) );
		add_action( 'plugins_loaded', 			array( $this, 'initialize_classes' ) );
		add_action( 'admin_notices', 			array( $this, 'admin_notices' ) );
		add_action( 'admin_menu', 				array( $this, 'admin_menu' ) );
		add_action( 'wp_ajax_search_form', 		array( $this, 'ajax_search_form' ) );

		add_filter( 'plugin_action_links_' . DFRAPI_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		do_action( 'dfrapi_loaded' );
		
	}

    function ajax_search_form() {
        $sform = new Dfrapi_SearchForm();
        echo $sform->ajaxHandler();
        die;
    }

	function admin_menu() {	
		add_menu_page( 
			__( 'Datafeedr API', DFRAPI_DOMAIN ), 
			__( 'Datafeedr API', DFRAPI_DOMAIN ), 
			'manage_options', 
			'dfrapi',
			'', 
			null, 
			42 
		);
	}
	
	function initialize_classes() {
		
		new Dfrapi_Configuration();
		
		// Show "Networks" page if API keys are present.
		if ( Dfrapi_Env::api_keys_exist() ) {
			new Dfrapi_Networks();
		}
		
		// Show "Merchants" page if API keys are present AND a network is selected.
		if ( Dfrapi_Env::api_keys_exist() &&  Dfrapi_Env::network_is_selected() ) {
			new Dfrapi_Merchants();
		}
		
		// Show Tools, Export and Import pages if API keys are present.
		if ( Dfrapi_Env::api_keys_exist() ) {
			new Dfrapi_Tools();
			new Dfrapi_Export();
			new Dfrapi_Import();
			new Dfrapi_Account();
		}
	}
	
	function load_css() {
	
		// Basic styling for API pages.
		wp_register_style( 'dfrapi_css', DFRAPI_URL . 'css/style.css', false, DFRAPI_VERSION );
		wp_enqueue_style( 'dfrapi_css' );
	
		// Basic styling for API pages.
		wp_register_style( 'dfrapi_searchform', DFRAPI_URL . 'css/searchform.css', false, DFRAPI_VERSION );
		wp_enqueue_style( 'dfrapi_searchform' );
	}
	
    function load_js() {
    
    	wp_register_script( 'dfrapi_general_js', DFRAPI_URL.'js/general.js', array( 'jquery' ), DFRAPI_VERSION, true );
        wp_enqueue_script( 'dfrapi_general_js' );
        
    	wp_register_script( 'dfrapi_searchfilter_js', DFRAPI_URL.'js/searchfilter.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_searchfilter_js' );
        
    	wp_register_script( 'dfrapi_merchants_js', DFRAPI_URL.'js/merchants.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_merchants_js' );
        
    	wp_register_script( 'dfrapi_searchform_js', DFRAPI_URL.'js/searchform.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_searchform_js' );

        wp_register_script( 'dfrapi_jquery_reveal_js', DFRAPI_URL.'js/jquery.reveal.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_jquery_reveal_js' );
    }

	function admin_notices() {
		$dfrapi_env = new Dfrapi_Env();
		if ( $notices = get_option( 'dfrapi_admin_notices' ) ) {
			foreach ( $notices as $key => $message ) {
				$button = ( $message['url'] != '' ) ? dfrapi_fix_button( $message['url'], $message['button_text'] ) : '';
				echo '<div class="'.$message['class'].'"><p>'.$message['message'].$button.'</p></div>';
			}
			delete_option( 'dfrapi_admin_notices' );
		}
	}
    
	function plugin_row_meta( $links, $plugin_file ) {
		if ( $plugin_file == DFRAPI_BASENAME ) {
			/* $links[] = sprintf( '<a href="' . admin_url( 'plugin-install.php?tab=search&type=tag&s=dfrapi' ) . '">%s</a>', __( 'Integration Plugins', DFRAPI_DOMAIN ) ); */
			$links[] = sprintf( '<a href="' . DFRAPI_HELP_URL . '">%s</a>', __( 'Support', DFRAPI_DOMAIN ) );
			return $links;
		}
		return $links;
	}

	function action_links( $links ) {
		return array_merge(
			$links,
			array(
				'config' => '<a href="' . admin_url( 'admin.php?page=dfrapi' ) . '">' . __( 'API Settings', DFRAPI_DOMAIN ) . '</a>',
			)
		);
	}

}

$dfrapi_initialize = new Dfrapi_Initialize();

} // class_exists check
