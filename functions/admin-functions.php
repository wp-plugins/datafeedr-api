<?php

/**
 * These are the Admin pages associated with the Datafeedr API plugin.
 * 
 * These are called from their respecitive classes.
 */
function dfrapi_setting_pages( $key=false ) {
	
	$pages = array ( 
		'dfrapi-configuration' => __( 'Configuration', DFRAPI_DOMAIN ),
		'dfrapi-networks' => __( 'Networks', DFRAPI_DOMAIN ),
		'dfrapi-merchants' => __( 'Merchants', DFRAPI_DOMAIN ),
		'dfrapi-tools' => __( 'Tools', DFRAPI_DOMAIN ),
		'dfrapi-export' => __( 'Export', DFRAPI_DOMAIN ),
		'dfrapi-import' => __( 'Import', DFRAPI_DOMAIN ),
		'dfrapi-account' => __( 'Account', DFRAPI_DOMAIN ),
	);
	
	if ( isset( $pages[$key] ) ) {
		return $pages[$key];
	}
	
}

/**
 * This stores messages to be displayed using the 
 * 'admin_notices' action.
 */
function dfrapi_admin_messages( $key=false ) {
	
	$messages = array(
		
		// User is missing 1 or more of their API Keys.
		'missing_api_keys' => array(
			'class' 		=> 'update-nag',
			'message' 		=> __( 'Your Datafeedr API Keys are missing.', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi',
			'button_text'	=> __( 'Add Your API Keys', DFRAPI_DOMAIN )
		),
		
		// User is missing a network
		'missing_network_ids' => array(
			'class' 		=> 'update-nag',
			'message' 		=> __( 'You haven\'t selected any affiliate networks yet.', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_networks',
			'button_text'	=> __( 'Select Networks', DFRAPI_DOMAIN )
		),
		
		// User is missing a merchant
		'missing_merchant_ids' => array(
			'class' 		=> 'update-nag',
			'message' 		=> __( 'You haven\'t selected any merchants yet.', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_merchants',
			'button_text'	=> __( 'Select Merchants', DFRAPI_DOMAIN )
		),
		
		// Display message that user has used 90%+ of API requests.
		'usage_over_90_percent' => array(
			'class' 		=> 'update-nag',
			'message' 		=> __( 'You have used ', DFRAPI_DOMAIN ) . dfrapi_get_api_usage_percentage() . __( '% of your total Datafeedr API requests this month. ', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_account',
			'button_text'	=> __( 'View your account', DFRAPI_DOMAIN )
		),
		
		// Missing affiliate IDs message.
		'missing_affiliate_ids' => array(
			'class' 		=> 'update-nag',
			'message' 		=> __( 'You are missing affiliate IDs. ', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_networks',
			'button_text'	=> __( 'Enter your Affiliate IDs', DFRAPI_DOMAIN )
		),
	);
	
	if ( isset( $messages[$key] ) ) {
		dfrapi_admin_notices( $key, $messages );
	}	

}

/**
 * This gets any notices set by the plugin.
 */
function dfrapi_admin_notices( $key, $messages ) {
	$notices = get_option( 'dfrapi_admin_notices', array() );
	$notices[$key] = $messages[$key];
	update_option( 'dfrapi_admin_notices', $notices );
}

/**
 * Button text for admin notices.
 */
function dfrapi_fix_button( $url, $button_text=false ) {
	if ( !$button_text ) {
		$button_text = __( 'Fix This Now', DFRAPI_DOMAIN );
	}
	if ( substr( $url, 0, 4 ) === "http" ) {
		return ' <a target="blank" href="' . $url . '">' .$button_text . '</a>';
	} else {
		return ' <a href="' . admin_url( $url ) . '">' .$button_text . '</a>';
	}
}

/**
 * Return plan IDs & names.
 */
function dfrapi_get_membership_plans() {
	return array(
		101000 		=> 'Starter',
		1025000 	=> 'Basic',
		1125000 	=> 'Beta Tester',
		10100000 	=> 'Professional',
		10250000 	=> 'Enterprise',
	);
}

/**
 * Convert network group names to a css class name.
 */
function dfrapi_group_name_to_css( $group ) {
	if ( is_string( $group ) ) {
		return strtolower( 
			str_replace( 
				array( " ", "-", "." ), 
				"", 
				$group 
			) 
		);
	} elseif ( is_array( $group ) ) {
		$name = str_replace( array( " ", "-", "." ), "", $group['group'] );
		$type = ( $group['type'] == 'coupons' ) ? '_coupons' : '';
		return strtolower( $name . $type );	
	}
}

/**
 * Adds global "support" content to "Help" tabs for all Datafeedr plugins.
 */
function dfrapi_help_tab( $screen ) {

	$screen->add_help_tab( array(
		'id'	=> 'dfrapi_support_tab',
		'title'	=> __( 'Support', DFRAPI_DOMAIN ),
		'content'	=>
			'<h2>' . __( "Datafeedr Support", DFRAPI_DOMAIN ) . '</h2>' . 
			'<p>' . sprintf(__( 'If you have a question or a problem, please search the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">documentation</a> and the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">support forums</a>', DFRAPI_DOMAIN ), DFRAPI_DOCS_URL, DFRAPI_QNA_URL ) . '. ' . __( 'If you are still unable to find an answer feel free to contact us using the links below.', DFRAPI_DOMAIN ) . '</p>' .
			'<p><a href="' . DFRAPI_ASK_QUESTION_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button button-primary" target="_blank">' . __( 'Post a Question', DFRAPI_DOMAIN ) . '</a> (' . __( 'recommended', DFRAPI_DOMAIN ) . ')</p>' . 
			'<p><a href="' . DFRAPI_EMAIL_US_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button" target="_blank">' . __( 'Email Us', DFRAPI_DOMAIN ) . '</a></p>'

	) );

	$screen->add_help_tab( array(
		'id'	=> 'dfrapi_bug_tab',
		'title'	=> __( 'Found a bug?', DFRAPI_DOMAIN ),
		'content'	=>
			'<h2>' . __( "Found a bug?", DFRAPI_DOMAIN ) . '</h2>' . 
			'<p>' . sprintf( __( 'If you find a bug within Datafeedr please see if it\'s already been reported in <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">Bug Reports</a>. If it\'s a new bug, please be as descriptive as possible when reporting a bug.', DFRAPI_DOMAIN ), DFRAPI_BUG_REPORTS_URL ) . '</p>' .
			'<p><a href="' . DFRAPI_REPORT_BUG_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button button-primary" target="_blank">' . __( 'Report a bug', DFRAPI_DOMAIN ) . '</a></p>'

	) );

	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', DFRAPI_DOMAIN ) . '</strong></p>' .
		'<p><a href="' . DFRAPI_HOME_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">' . __( 'About Datafeedr', DFRAPI_DOMAIN ) . '</a></p>' .
		'<p><a href="' . DFRAPI_HOME_URL . '/keys?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">' . __( 'Datafeedr API Keys', DFRAPI_DOMAIN ) . '</a></p>'
	);

	return $screen;
}