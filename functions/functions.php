<?php

function dfrapi_get_api_usage_percentage() {
	$account = get_option( 'dfrapi_account' );
	if ( $account ) {
		if ( $account['max_requests'] > 0 ) {
			$percentage = floor ( ( intval( $account['request_count'] ) / intval( $account['max_requests'] ) * 100 ) );
		} else {
			$percentage = 0;
		}
		return $percentage;
	}
	return false;
}

add_action( 'init', 'dfrapi_email_user_about_usage' );
function dfrapi_email_user_about_usage() {
	
	$percentage = dfrapi_get_api_usage_percentage();
	
	$default = array(
		'80_percent'  => '', 
		'90_percent'  => '', 
		'100_percent' => '' 
	);
	
	// Don't do anything if less than 80%.
	if ( $percentage < 80 ) {
		update_option( 'dfrapi_usage_notification_tracker', $default );
		return; 
	}
	
	$tracker = get_option( 'dfrapi_usage_notification_tracker', $default );
	
	$params 			= array();
	$params['to'] 		= get_bloginfo( 'admin_email' );
	$params['message']  = __( "This is an automated message sent from: ", DFRAPI_DOMAIN ) . get_bloginfo( 'wpurl' );
	$params['message'] .=  "\n\n";
		
	if ( $percentage >= 100 && empty( $tracker['100_percent'] ) ) {
		
		$params['subject']  = get_bloginfo( 'name' ) . __( ': Datafeedr API Message (Critical)', DFRAPI_DOMAIN );	
		$params['message'] .= __( "You have used all of your Datafeedr API requests for this period.", DFRAPI_DOMAIN );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "You are no longer able to query the Datafeedr API to get product information.", DFRAPI_DOMAIN );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "It is strongly recommended that you upgrade your Datafeedr API account.", DFRAPI_DOMAIN );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "Upgrade: ", DFRAPI_DOMAIN ) . dfrapi_user_pages( 'change' );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "View account: ", DFRAPI_DOMAIN ) . admin_url( 'admin.php?page=dfrapi_account' );
		
		$tracker['100_percent'] = 1;
		update_option( 'dfrapi_usage_notification_tracker', $tracker );
		
		wp_mail( $params['to'], $params['subject'], $params['message'] );
		
	} elseif ( $percentage >= 90 && $percentage < 100 && empty( $tracker['90_percent'] ) ) {
		
		$params['subject']  = get_bloginfo( 'name' ) . __( ': Datafeedr API Message (Warning)', DFRAPI_DOMAIN );	
		$params['message'] .= __( "You have used 90% of your Datafeedr API requests for this period.", DFRAPI_DOMAIN );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "It is highly recommended that you upgrade your Datafeedr API account.", DFRAPI_DOMAIN );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "Upgrade: ", DFRAPI_DOMAIN ) . dfrapi_user_pages( 'change' );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "View account: ", DFRAPI_DOMAIN ) . admin_url( 'admin.php?page=dfrapi_account' );
		
		$tracker['90_percent'] = 1;
		update_option( 'dfrapi_usage_notification_tracker', $tracker );
		
		wp_mail( $params['to'], $params['subject'], $params['message'] );
		
	} elseif ( $percentage >= 80 && $percentage < 90 && empty( $tracker['80_percent'] ) ) {
		
		$params['subject']  = get_bloginfo( 'name' ) . __( ': Datafeedr API Message (Notice)', DFRAPI_DOMAIN );	
		$params['message'] .= __( "You have used 80% of your Datafeedr API requests for this period.", DFRAPI_DOMAIN );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "It is recommended that you upgrade your Datafeedr API account.", DFRAPI_DOMAIN );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "Upgrade: ", DFRAPI_DOMAIN ) . dfrapi_user_pages( 'change' );
		$params['message'] .=  "\n\n";
		$params['message'] .= __( "View account: ", DFRAPI_DOMAIN ) . admin_url( 'admin.php?page=dfrapi_account' );
		
		$tracker['80_percent'] = 1;
		update_option( 'dfrapi_usage_notification_tracker', $tracker );
		
		wp_mail( $params['to'], $params['subject'], $params['message'] );
	}
	
	return;
}

/**
 * Modify affiliate ID if product is a Zanox product. 
 * Replaces $affiliate_id with "zmid".
 */
add_filter( 'dfrapi_filter_affiliate_id', 'dfrapi_get_zanox_zmid', 10, 3 );
function dfrapi_get_zanox_zmid( $affiliate_id, $url, $product ) {
	if ( isset( $product['source'] ) && preg_match( "/\bZanox\b/", $product['source'] ) ) {
		$zanox = dfrapi_api_get_zanox_zmid( $product['merchant_id'], $affiliate_id );
    	$affiliate_id = $zanox[0]['zmid'];
	}
	return $affiliate_id;
}

function dfrapi_get_zanox_keys() {
	
	$configuration = (array) get_option( 'dfrapi_configuration' );	
	
	$zanox_connection_key = false;
	$zanox_secret_key = false;
	
	if ( isset( $configuration['zanox_connection_key'] ) && ( $configuration['zanox_connection_key'] != '' ) ) {
		$zanox_connection_key = $configuration['zanox_connection_key'];
	}
	
	if ( isset( $configuration['zanox_secret_key'] ) && ( $configuration['zanox_secret_key'] != '' ) ) {
		$zanox_secret_key = $configuration['zanox_secret_key'];
	}
	
	if ( $zanox_connection_key && $zanox_secret_key ) {
		return array( 
			'connection_key'=> $zanox_connection_key,
			'secret_key' 	=> $zanox_secret_key,
		);
	}
	
	return false;
}

/**
 * Returns a link to a user page on v4.datafeedr.com.
 */
function dfrapi_user_pages( $page ) {

	$pages = array(
		'edit' 		=> 'edit',
		'summary' 	=> 'subscription',
		'invoices' 	=> 'subscription/invoices',
		'billing' 	=> 'subscription/billing',
		'change' 	=> 'subscription/change',
		'cancel' 	=> 'subscription/cancel',
		'signup' 	=> 'subscription/signup',
	);

	$account = get_option( 'dfrapi_account', array() );
	
	if ( empty( $account ) ) { return false; }

	return DFRAPI_HOME_URL . '/user/' . $account['user_id'] . '/' . $pages[$page];
}

/**
 * Adds option name to transient whitelist. This is so we know 
 * all transient options that can be deleted when deleting the 
 * API cache on Tools page.
 */
function dfrapi_update_transient_whitelist( $option_name ) {
	$whitelist = get_option( 'dfrapi_transient_whitelist', array() );
	$whitelist[] = $option_name;
	update_option( 'dfrapi_transient_whitelist', array_unique( $whitelist ) );
}

/**
 * Add affiliate ID to an affiliate link.
 * 
 * @param $product - An array of a single's product's information.
 */
function dfrapi_url( $product ) {
	
	// Get all the user's selected networks.
	$networks = (array) get_option( 'dfrapi_networks' );
	
	// Extract the affiliate ID from the $networks array.
	$affiliate_id = $networks['ids'][$product['source_id']]['aid'];
	$affiliate_id = apply_filters( 'dfrapi_affiliate_id', $affiliate_id, $product, $networks );
	
	// Affiliate ID is missing.  Do action and return empty string.
	if ( $affiliate_id == '' ) {
		do_action( 'dfrapi_affiliate_id_is_missing', $product );
		return '';
	}
	
	// Set URL and apply filters.
	$url = $product['url'];
	$url = apply_filters( 'dfrapi_before_affiliate_id_insertion', $url, $product, $affiliate_id );
	$affiliate_id = apply_filters( 'dfrapi_filter_affiliate_id', $affiliate_id, $url, $product );	
	$url = str_replace( "@@@", $affiliate_id, $url );
	$url = apply_filters( 'dfrapi_after_affiliate_id_insertion', $url, $product, $affiliate_id );
	
	// Return URL
	return $url;
}

/**
 * Output an error message generated by the API.
 */
function dfrapi_output_api_error( $data ) { 
	$error = $data['dfrapi_api_error'];
	$params = @$data['dfrapi_api_error']['params'];
	?>
	<div class="dfrapi_api_error">
		<div class="dfrapi_head"><?php _e( 'Datafeedr API Error', DFRAPI_DOMAIN ); ?></div>
		<div class="dfrapi_msg"><strong><?php _e( 'Message:', DFRAPI_DOMAIN ); ?></strong> <?php echo $error['msg']; ?></div>
		<div class="dfrapi_code"><strong><?php _e( 'Code:', DFRAPI_DOMAIN ); ?></strong> <?php echo $error['code']; ?></div>
		<div class="dfrapi_class"><strong><?php _e( 'Class:', DFRAPI_DOMAIN ); ?></strong> <?php echo $error['class']; ?></div>
		<?php if ( is_array( $params ) ) : ?>
			<div class="dfrps_query"><strong><?php _e( 'Query:', DFRAPI_DOMAIN ); ?></strong> <span><?php echo dfrapi_display_api_request( $params ); ?></span></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Convert a currency code to sign. USD => $
 */
function dfrapi_currency_code_to_sign( $code ) {
	$map = array(
		'USD' => '&#36;',
		'GBP' => '&pound;',
		'EUR' => '&euro;',
		'CAD' => '&#36;',
		'AUD' => '&#36;',
		'DKK' => 'kr',
		'SEK' => 'kr',
		'NOK' => 'kr',
		'CHF' => 'CHF',
		'NZD' => '&#36;',
		'TRY' => '&#8356;',
	);
	
	$map = apply_filters( 'dfrapi_currency_sign_mapper', $map );
	
	if ( isset ( $map[$code] ) ) {
		return $map[$code];
	} else {
		return $map['USD'];
	}
}

/**
 * This displays the API request in PHP format.
 */
function dfrapi_display_api_request( $params=array() ) {

	$html = '';

	if ( empty( $params ) ) { return $html; }
	
	$html .= '$search = $api->searchRequest();<br />';
	foreach ( $params as $k => $v ) {
		
		// Handle query.
		if ( $k == 'query' ) {
			foreach ( $v as $query ) {
				if ( substr( $query, 0, 9 ) !== 'source_id' || substr( $query, 0, 11 ) !== 'merchant_id' ) {
					$query = str_replace( ",", ", ", $query );
				}
				$html .= '$search->addFilter( \''.( $query ).'\' );<br />';
			}
		}
		
		// Handle sort.
		if ( $k == 'sort' ) {
			foreach ( $v as $sort ) {
				$html .= '$search->addSort( \''.stripslashes( $sort ).'\' );<br />';
			}
		}
		
		// Handle limit.
		if ( $k == 'limit' ) {
			$html .= '$search->setLimit( \''.stripslashes( $v ).'\' );<br />';
		}
		
		// Handle Offset.
		if ( $k == 'offset' ) {
			$html .= '$search->setOffset( \''.stripslashes( $v ).'\' );<br />';
		}
		
		// Handle Exclude duplicates.
		if ( $k == 'exclude_duplicates' ) {
			$html .= '$search->excludeDuplicates( \''. $v  . '\' );<br />';
		}
	}
	
	$html .= '$products = $search->execute();';
	return $html;
	
}

function dfrapi_get_query_param( $query, $param ) {
	if ( is_array( $query ) && !empty( $query ) ) {
		foreach( $query as $k => $v ) {
			if ( $v['field'] == $param ) {
				return array(
					'field' 	=> @$v['field'],
					'operator' 	=> @$v['operator'],
					'value' 	=> @$v['value'],
				);
			}
		}
	}
	return false;
}

/**
 * Converts a value in cents into a value with proper
 * decimal placement.
 * 
 * Example: 14999 => 149.99
 */
function dfrapi_int_to_price( $price ) {
	return number_format( ( $price/100 ), 2 );
}

/**
 * Converts decimal or none decimal prices into values in cents.
 * 
 * assert(dfrapi_price_to_int('123')     		==12300);
 * assert(dfrapi_price_to_int('123.4')   		==12340);
 * assert(dfrapi_price_to_int('1234.56') 		==123456);
 * assert(dfrapi_price_to_int('123,4')   		==12340);
 * assert(dfrapi_price_to_int('1234,56') 		==123456);
 * assert(dfrapi_price_to_int('1,234,567')    	==123456700);
 * assert(dfrapi_price_to_int('1,234,567.8')  	==123456780);
 * assert(dfrapi_price_to_int('1,234,567.89') 	==123456789);
 * assert(dfrapi_price_to_int('1.234.567')    	==123456700);
 * assert(dfrapi_price_to_int('1.234.567,8')  	==123456780);
 * assert(dfrapi_price_to_int('1.234.567,89') 	==123456789);
 * assert(dfrapi_price_to_int('FOO 123 BAR')    ==12300);
 */
function dfrapi_price_to_int( $price ) {
    $d = $price;
    $d = preg_replace('~^[^\d.,]+~', '', $d);
    $d = preg_replace('~[^\d.,]+$~', '', $d);

    // 123 => 12300
    if(preg_match('~^(\d+)$~', $d, $m))
        return intval($m[1] . '00');

    // 123.4 => 12340, 123,45 => 12345
    if(preg_match('~^(\d+)[.,](\d{1,2})$~', $d, $m))
        return intval($m[1] . substr($m[2] . '0000', 0, 2));

    // 1,234,567.89 => 123456789
    if(preg_match('~^((?:\d{1,3})(?:,\d{3})*)(\.\d{1,2})?$~', $d, $m)) {
        $f = isset($m[2]) ? $m[2] : '.';
        return intval(str_replace(',', '', $m[1]) . substr($f . '0000', 1, 2));
    }

    // 1.234.567,89 => 123456789
    if(preg_match('~^((?:\d{1,3})(?:\.\d{3})*)(,\d{1,2})?$~', $d, $m)) {
        $f = isset($m[2]) ? $m[2] : '.';
        return intval(str_replace('.', '', $m[1]) . substr($f . '0000', 1, 2));
    }

    return NULL;
}

function dfrapi_html_output_api_error( $data ) { 
	$error = $data['dfrapi_api_error'];
	$params = @$data['dfrapi_api_error']['params'];
	?>
	<div class="dfrapi_api_error">
		<div class="dfrapi_head"><?php _e( 'Datafeedr API Error', DFRAPI_DOMAIN ); ?></div>
		<div class="dfrapi_msg"><strong><?php _e( 'Message:', DFRAPI_DOMAIN ); ?></strong> <?php echo $error['msg']; ?></div>
		<div class="dfrapi_code"><strong><?php _e( 'Code:', DFRAPI_DOMAIN ); ?></strong> <?php echo $error['code']; ?></div>
		<div class="dfrapi_class"><strong><?php _e( 'Class:', DFRAPI_DOMAIN ); ?></strong> <?php echo $error['class']; ?></div>
		<?php if ( is_array( $params ) ) : ?>
			<div class="dfrapi_query"><strong><?php _e( 'Query:', DFRAPI_DOMAIN ); ?></strong> <span><?php echo dfrapi_helper_display_api_request( $params ); ?></span></div>
		<?php endif; ?>
	</div>
	<?php
}

