<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Networks' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Networks {

		private $page = 'dfrapi-networks';
		private $key;
		private $all_networks;

		public function __construct() {
			
			$this->key = 'dfrapi_networks';
			$this->all_networks = dfrapi_api_get_all_networks();
			add_action( 'init', array( &$this, 'load_settings' ) );
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );			
		}
		
		function api_errors() {
			if ( array_key_exists( 'dfrapi_api_error', $this->all_networks ) ) {
				return $this->all_networks;
			}
			return false;
		}
	
		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Networks &#8212; Datafeedr API', DFRAPI_DOMAIN ), 
				__( 'Networks', DFRAPI_DOMAIN ), 
				'manage_options', 
				$this->key,
				array( $this, 'output' ) 
			);
		}
		
		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && isset( $_GET['page'] ) && $this->key == $_GET['page'] ) {
				echo '<div class="updated"><p>';
				_e( 'Networks successfully updated!', DFRAPI_DOMAIN );
				echo '</p></div>';
			}
		}

		function output() {			
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>' . dfrapi_setting_pages( $this->page ) . ' &#8212; Datafeedr API</h2>';
			if ( $errors = $this->api_errors() ) {
				echo dfrapi_html_output_api_error( $errors );
			} else {
				echo '<form method="post" action="options.php">';
				wp_nonce_field( 'update-options' );
				settings_fields( $this->page );
				do_settings_sections( $this->page);
				submit_button();
				echo '</form>';
			}
            echo '<div id="dfr_unload_message" style="display:none">' . __( 'You have unsaved changes', DFRAPI_DOMAIN ) . '</div>';
			echo '</div>';
		}
		
		function load_settings() {			
			$this->options = (array) get_option( $this->key );
			$this->options = array_merge( array(
				'ids' => array(),
			), $this->options );
		}
	
		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'networks', __( 'Select Networks', DFRAPI_DOMAIN ), array( &$this, 'section_networks_desc' ), $this->page );
			add_settings_field( 'ids', __( 'Networks', DFRAPI_DOMAIN ), array( &$this, 'field_network_ids' ), $this->page, 'networks', array( 'label_for' => 'DFRAPI_HIDE_LABEL' ) );
		}
	
		function section_networks_desc() { 
			echo __( 'Select the affiliate networks you belong to, enter your affiliate ID for each then click <strong>[Save Changes]</strong>.', DFRAPI_DOMAIN );
		}
		
		function field_network_ids() {
			$groups = $this->get_network_group_names();
			foreach ( $groups as $group ) {
				$num_networks_checked_in_group = $this->num_networks_checked_in_group( $group );
				$active = ( $num_networks_checked_in_group != '' ) ? 'active' : '';
				echo '
				<div class="group network_logo_30x30_' . dfrapi_group_name_to_css( $group ) . ' ' . $active . '" id="group_' . dfrapi_group_name_to_css( $group ) . '">
					<div class="meta">
						<span class="name">' . $group . '</span>
						<span class="status">
							' . $this->num_missing_affiliate_ids_in_group( $group ) . '
							' . $num_networks_checked_in_group . '
							' . $this->num_networks_in_group( $group ) . '
							' . $this->num_merchants_in_group( $group ) . '
							' . $this->num_products_in_group( $group ) . ' 
						</span>
					</div>
					' . $this->get_groups_networks( $group ) . '
				</div>';
			}
		}

		function get_groups_networks( $group_name ) {
			$html = '
			<div style="display:none;" class="networks">
				<table class="wp-list-table widefat fixed networks_table" cellspacing="0">
					<thead>
						<tr>
							<th class="checkbox_head"> &nbsp; </th>
							<th class="networks_head">' . __( 'Network', DFRAPI_DOMAIN ) . '</th>
							<th class="type_head">' . __( 'Type', DFRAPI_DOMAIN ) . '</th>
							<th class="aid_head">' . __( 'Affiliate ID', DFRAPI_DOMAIN ) . ' <a href="' .$this->map_link( $group_name ) .'" target="_blank" title="' . __( 'Learn how to find your affiliate ID from ', DFRAPI_DOMAIN ) . $group_name . __( ' (opens in new window).', DFRAPI_DOMAIN ) . '"><img src="' . DFRAPI_URL . 'images/icons/help.png" alt="' . __( 'more info', DFRAPI_DOMAIN ) . '" style="vertical-align: middle" /></a> <small style="font-weight:normal;color:#a00;">(' . __( 'required', DFRAPI_DOMAIN ) . ')</small></th>
							<th class="tid_head">' . __( 'Tracking ID', DFRAPI_DOMAIN ) . ' <a href="' . DFRAPI_HOME_URL . '/node/1113" target="_blank" title="' . __( 'Learn more about this field (opens in new window).', DFRAPI_DOMAIN ) . '"><img src="' . DFRAPI_URL . 'images/icons/help.png" alt="' . __( 'more info', DFRAPI_DOMAIN ) . '" style="vertical-align: middle" /></a> <small style="font-weight:normal;color:#999;">(' . __( 'optional', DFRAPI_DOMAIN ) . ')</small></th>
						</tr>
					</thead>
					<tbody>
			';
		
			$i=0;
			foreach ( $this->all_networks as $network ) {
				$i++;
				$checked = ( array_key_exists( $network['_id'], (array) $this->options['ids'] ) ) ? ' checked="checked"' : '';
				$type = ( $network['type'] == 'products' ) ?  __( 'products', DFRAPI_DOMAIN ) :  __( 'coupons', DFRAPI_DOMAIN );
				$type_class = ( $network['type'] == 'products' ) ?  ' dfrapi_label-info"' :  ' dfrapi_label-success';
				$no_products = ( $network['product_count'] < 1 ) ? 'no_products' : '';
				$alternate = ( $i % 2 ) ? '' : ' alternate';
			
				if ( $network['group'] == $group_name ) {
					$html .= '
					<tr 
						class="network ' . $no_products . $alternate . '" 
						id="network_id_' . $network['_id'] . '" 
						nid="' . $network['_id'] . '" 
						key="' . $this->key . '" 
						aid="' . @$this->options['ids'][$network['_id']]['aid'] . '" 
						tid="' . @$this->options['ids'][$network['_id']]['tid'] . '"
					>
						<td class="network_checkbox">
							<input type="checkbox" id="nid_' . $network['_id'] . '" class="check_network" name="' . $this->key . '[ids][' . $network['_id'] . '][nid]" value="' . $network['_id'] . '"' . $checked . ' />
						</td>
						<td class="network_name">
							<label for="nid_' . $network['_id'] . '">
								' . $network['name'] . '
								<div class="network_info">
									<span class="num_merchants">' . number_format( $network['merchant_count'] ) . ' ' . __( 'merchants', DFRAPI_DOMAIN ) . '  <span class="sep">/</span>
									<span class="num_products">' . number_format( $network['product_count'] ) . ' ' . $type . '</span>
								</div>
							</label>
						</td>
						<td class="network_type">
							<span class="dfrapi_label' . $type_class . '">' . ucfirst( $type ) . '</span>
						</td>
						';
						
						if ( $group_name == 'Zanox' ) {
							$html .= '<td class="aid_input">' . $this->zanox_adspace( $network['_id'], @$this->options['ids'][$network['_id']]['aid'] ) . '</td>';
						} else {
							$html .= '<td class="aid_input"><input type="text" name="dfrapi_networks[ids][' . $network['_id'] . '][aid]" value="' . @$this->options['ids'][$network['_id']]['aid'] . '" class="aid_input_field" /></td>';
						}					
						
						$html .= '
						<td class="tid_input"><input type="text" name="dfrapi_networks[ids][' . $network['_id'] . '][tid]" value="' . @$this->options['ids'][$network['_id']]['tid'] . '" class="tid_input_field" /></td>
					</tr>
					';
				}
			}
		
			$html .= '
					</tbody>
				</table>
			</div>
			';
			return $html;
		}
	
		function get_network_group_names() {
			$networks = $this->all_networks;
			$groups = array();
			foreach ( $networks as $network ) {
				$groups[] = $network['group'];
			}
			return array_unique( $groups );	
		}
		
		/**
		 * This returns <select> menu for adspaces.
		 */		
		function zanox_adspace( $nid, $selected_adspace ) {
			$html = '';
			$adspaces = $this->get_zanox_adspaces();
			if ( isset( $adspaces['zanox_error'] ) ) {
				if ( $adspaces['zanox_error'] == 'missing_keys' ) {
					$html .= '<span><a href="' . admin_url( 'admin.php?page=dfrapi' ) . '" class="dfrapi_warning">' . __( 'Please add your Zanox Connection &amp; Secret Key', DFRAPI_DOMAIN ) . '</a>.</span>';
				} else {
					$html .= '<pre>' . print_r( $adspaces['zanox_error'], TRUE ) . '</pre>';
				}
			} else {
				$html .= '<select name="dfrapi_networks[ids][' . $nid . '][aid]">';
				$html .= '<option value="">' . __( 'Select an adspace', DFRAPI_DOMAIN ) . '</option>';
				foreach( $adspaces as $adspace ) {
					$selected = selected( $selected_adspace, $adspace['id'], false );
					$html .= '<option value="' . $adspace['id'] . '" ' . $selected . '>' . $adspace['name'] . '</option>';
				}
				$html .= '</select>';
			}			
			return $html;
		}
		
		/**
		 * This returns adspaces for a Zanox user.
		 */
		function get_zanox_adspaces() {
			$option_name = 'dfrapi_zanox_adspaces';
			$adspaces = get_transient( $option_name );
			if ( false === $adspaces || empty ( $adspaces ) ) {
				$zanox_keys = dfrapi_get_zanox_keys();
				if ( !$zanox_keys ) {
					return array( 'zanox_error' => 'missing_keys' );
				} else {
					$client = new Dfr_ZanoxAPIClient( $zanox_keys['connection_key'], $zanox_keys['secret_key'] );
					$adspaces = $client->adspaces();
					if ( $client->error() ) {
						return array( 'zanox_error' => $client->error() );
					} else {
						set_transient( $option_name, $adspaces, HOUR_IN_SECONDS );
					}
				}				
			}
			dfrapi_update_transient_whitelist( $option_name );
			return $adspaces;
		}
		
		function num_missing_affiliate_ids_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					if ( array_key_exists( $network['_id'], (array) $this->options['ids'] ) ) {
						if ( trim( $this->options['ids'][$network['_id']]['aid'] ) == '' ) {
							$count++;
						}
					}
				}
			}
		
			if ( $count > 0 ) {
				$messages = $this->messages();
				return '<span class="num_missing">' . sprintf( translate_nooped_plural( $messages['num_missing'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}
	
		function num_networks_checked_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					if ( array_key_exists( $network['_id'], (array) $this->options['ids'] ) ) {
						$count++;
					}
				}
			}
		
			if ( $count > 0 ) {
				$messages = $this->messages();
				return '<span class="num_checked">' . sprintf( translate_nooped_plural( $messages['num_checked'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}
	
		function num_networks_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					$count++;
				}			
			}
		
			if ( $count > 0 ) {
				$messages = $this->messages();
				return '<span class="num_networks">' . sprintf( translate_nooped_plural( $messages['num_networks'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}
	
		function num_merchants_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					$count += $network['merchant_count'];
				}			
			}
		
			if ( $count > 0 ) {
				$messages = $this->messages();
				return '<span class="num_merchants">' . sprintf( translate_nooped_plural( $messages['num_merchants'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}	
		}
	
		function num_products_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					$count += $network['product_count'];
				}			
			}
		
			if ( $count > 0 ) {
				$messages = $this->messages();
				return '<span class="num_products">' . sprintf( translate_nooped_plural( $messages['num_products'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span>';
			}
	
		}
	
		function messages() {
			return array(
				'num_missing' 	=> _n_noop( '%s missing affiliate ID', '%s missing affiliate IDs' ),
				'num_checked' 	=> _n_noop( '%s network selected', '%s networks selected' ),
				'num_networks'  => _n_noop( '%s network', '%s networks' ),
				'num_merchants' => _n_noop( '%s merchant', '%s merchants' ),
				'num_products'  => _n_noop( '%s product', '%s products' ),
			);
		}
		
		function map_link( $name ) {
			$links = array(
				'AdRecord' 				=> 'http://www.datafeedr.com/docs/item/252',
				'Adtraction' 			=> 'http://www.datafeedr.com/docs/item/261',
				'Affiliate4You' 		=> 'http://www.datafeedr.com/docs/item/265',
				'AffiliateWindow' 		=> 'http://www.datafeedr.com/docs/item/101',
				'Affiliator' 			=> 'http://www.datafeedr.com/docs/item/270',
				'Affilinet' 			=> 'http://www.datafeedr.com/docs/item/104',
				'Amazon Local' 			=> 'http://www.datafeedr.com/docs/item/275',
				'Avangate' 				=> 'http://www.datafeedr.com/docs/item/188',
				'AvantLink' 			=> 'http://www.datafeedr.com/docs/item/190',
				'Belboon' 				=> 'http://www.datafeedr.com/docs/item/201',
				'BettyMills' 			=> 'http://www.datafeedr.com/docs/item/264',
				'bol.com' 				=> 'http://www.datafeedr.com/docs/item/253',
				'ClickBank' 			=> 'http://www.datafeedr.com/docs/item/106',
				'ClixGalore' 			=> 'http://www.datafeedr.com/docs/item/200',
				'Commission Factory' 	=> 'http://www.datafeedr.com/docs/item/193',
				'Commission Junction' 	=> 'http://www.datafeedr.com/docs/item/65',
				'Commission Monster' 	=> 'http://www.datafeedr.com/docs/item/109',
				'Daisycon' 				=> 'http://www.datafeedr.com/docs/item/196',
				'DGM'					=> 'http://www.datafeedr.com/docs/item/263',
				'Double.net'			=> 'http://www.datafeedr.com/docs/item/271',
				'FlipKart'				=> 'http://www.datafeedr.com/docs/item/273',
				'Impact Radius' 		=> 'http://www.datafeedr.com/docs/item/268',
				'LinkConnector' 		=> 'http://www.datafeedr.com/docs/item/105',
				'LinkShare' 			=> 'http://www.datafeedr.com/docs/item/60',
				'M4N' 					=> 'http://www.datafeedr.com/docs/item/198',
				'MyCommerce' 			=> 'http://www.datafeedr.com/docs/item/111',
				'OneNetworkDirect' 		=> 'http://www.datafeedr.com/docs/item/112',
				'Paid on Results' 		=> 'http://www.datafeedr.com/docs/item/189',
				'Partner-ads' 			=> 'http://www.datafeedr.com/docs/item/262',
				'PepperJam' 			=> 'http://www.datafeedr.com/docs/item/103',
				'RegNow' 				=> 'http://www.datafeedr.com/docs/item/111',
				'RevResponse' 			=> 'http://www.datafeedr.com/docs/item/197',
				'ShareASale' 			=> 'http://www.datafeedr.com/docs/item/108',
				'SuperClix' 			=> 'http://www.datafeedr.com/docs/item/256',
				'TradeDoubler' 			=> 'http://www.datafeedr.com/docs/item/113',
				'TradeTracker' 			=> 'http://www.datafeedr.com/docs/item/195',
				'Webgains' 				=> 'http://www.datafeedr.com/docs/item/114',
				'Zanox' 				=> 'http://www.datafeedr.com/docs/item/269',
			);
			return $links[$name];
		}
		
		function validate( $input ) {
			$new_input['ids'] = array();
			if ( isset( $input['ids'] ) ) {
				foreach ( $input['ids'] as $k => $v ) {
					if ( isset( $v['nid'] ) ) {
						$new_input['ids'][$k] = $v;		
					}
				}
			}
			return $new_input;
		}
		
	} // class Dfrapi_Networks

} // class_exists check
