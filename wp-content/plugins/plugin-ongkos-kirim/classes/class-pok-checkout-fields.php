<?php

/**
 * Customized checkout fields
 */
class POK_Checkout_Fields {

	/**
	 * POK Core
	 *
	 * @var object
	 */
	protected $core;

	/**
	 * POK Setting
	 *
	 * @var object
	 */
	protected $setting;

	/**
	 * POK Helper
	 *
	 * @var object
	 */
	protected $helper;

	/**
	 * Field order
	 *
	 * @var array
	 */
	protected $field_order;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		$this->field_order = apply_filters(
			'pok_fields_priority', array(
				'first_name'    => 10,
				'last_name'     => 20,
				'company'       => 30,
				'country'       => 40,
				'state'         => 50,
				'city'          => 60,
				'district'      => 70,
				'address_1'     => 80,
				'address_2'     => 90,
				'postcode'      => 100,
				'phone'         => 110,
				'email'         => 120,
			)
		);

		if ( $this->helper->is_plugin_active() ) {
			add_filter( 'woocommerce_states', array( $this, 'set_provinces' ) );
			add_filter( 'woocommerce_checkout_fields', array( $this, 'custom_checkout_fields' ), 30 );
			add_filter( 'woocommerce_billing_fields', array( $this, 'custom_billing_fields' ), 40 );
			add_filter( 'woocommerce_shipping_fields', array( $this, 'custom_shipping_fields' ), 40 );
			add_filter( 'woocommerce_default_address_fields', array( $this, 'custom_special_checkout_fields' ), 40 );
			add_filter( 'woocommerce_get_country_locale', array( $this, 'country_locale' ), 30 );
			add_filter( 'woocommerce_localisation_address_formats', array( $this, 'custom_address_format' ) );
			add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'custom_address_replacement' ), 30, 2 );
			add_filter( 'woocommerce_my_account_my_address_formatted_address', array( $this, 'format_myaccount_address' ), 10, 3 );
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_district' ) );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );
			add_action( 'woocommerce_get_order_address', array( $this, 'set_address_district' ), 10, 3 );
			add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'show_shipping_weight' ) );
			add_action( 'woocommerce_checkout_update_order_review', array( $this, 'delete_wc_cache' ) );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_custom_fee' ) );
			add_filter( 'woocommerce_shipping_settings', array( $this, 'modify_shipping_settings' ) );
			add_filter( 'woocommerce_cart_ready_to_calc_shipping', array( $this, 'remove_shipping_on_cart' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Set custom provinces
	 *
	 * @param array $states WC States.
	 */
	public function set_provinces( $states ) {
		$provinces = $this->core->get_province();
		if ( ! empty( $provinces ) ) {
			$states['ID'] = $provinces;
		} else {
			wc_add_notice( __( 'Failed to load data. Please refresh the page.', 'pok' ), 'error' );
		}
		return $states;
	}

	/**
	 * Custom checkout fields
	 *
	 * @param  array $fields Checkout fields.
	 * @return array         Checkout fields
	 */
	public function custom_checkout_fields( $fields ) {
		$fields['billing']  = $this->alter_fields( $fields['billing'], 'billing' );
		$fields['shipping'] = $this->alter_fields( $fields['shipping'], 'shipping' );
		return $fields;
	}

	/**
	 * Custom billing fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Billing fields
	 */
	public function custom_billing_fields( $fields ) {
		return $this->alter_fields( $fields, 'billing' );
	}

	/**
	 * Custom shipping fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Billing fields
	 */
	public function custom_shipping_fields( $fields ) {
		return $this->alter_fields( $fields, 'shipping' );
	}

	/**
	 * Alter checkout fields
	 *
	 * @param  array  $fields Checkout fields.
	 * @param  string $type   Billing/Shipping.
	 * @return array          Customized fields.
	 */
	private function alter_fields( $fields = array(), $type = 'billing' ) {
		if ( 'ID' !== $this->helper->get_country_session( $type ) ) {
			return $fields;
		}

		$fields[ $type . '_first_name' ]['label']      = __( 'First Name', 'pok' );
		$fields[ $type . '_last_name' ]['label']       = __( 'Last Name', 'pok' );

		$fields[ $type . '_address_1' ]['label']       = __( 'Address', 'pok' );
		$fields[ $type . '_address_1' ]['priority']    = $this->field_order['address_1'];
		$fields[ $type . '_address_2' ]['priority']    = $this->field_order['address_2'];

		$fields[ $type . '_postcode' ]['label']        = __( 'Postcode / ZIP', 'pok' );
		$fields[ $type . '_postcode' ]['required']     = false;
		$fields[ $type . '_postcode' ]['class']        = array();
		$fields[ $type . '_postcode' ]['priority']     = $this->field_order['postcode'];

		$fields[ $type . '_email' ]['label']           = __( 'Email Address', 'pok' );
		$fields[ $type . '_email' ]['priority']        = $this->field_order['email'];

		$fields[ $type . '_phone' ]['label']           = __( 'Phone', 'pok' );
		$fields[ $type . '_phone' ]['priority']        = $this->field_order['phone'];

		$fields[ $type . '_country' ]['label']         = __( 'Country', 'pok' );
		$fields[ $type . '_country' ]['priority']      = $this->field_order['country'];

		$fields[ $type . '_state' ]['label']           = __( 'Province', 'pok' );
		$fields[ $type . '_state' ]['placeholder']     = __( 'Select Province', 'pok' );
		$fields[ $type . '_state' ]['priority']        = $this->field_order['state'];

		$fields[ $type . '_city' ]['label']            = __( 'City', 'pok' );
		$fields[ $type . '_city' ]['placeholder']      = __( 'Select City', 'pok' );
		$fields[ $type . '_city' ]['type']             = 'select';
		$fields[ $type . '_city' ]['required']         = true;
		$fields[ $type . '_city' ]['options']          = array( '' => __( 'Select City', 'pok' ) );
		$fields[ $type . '_city' ]['class']            = is_array( $fields[ $type . '_city' ]['class'] ) ? array_merge( $fields[ $type . '_city' ]['class'],array( 'validate-required' ) ) : array( 'validate-required' );
		$fields[ $type . '_city' ]['priority']         = $this->field_order['city'];

		if ( 'nusantara' === $this->setting->get( 'base_api' ) || 'pro' === $this->setting->get( 'rajaongkir_type' ) ) {
			$fields[ $type . '_district' ]['label']        = __( 'District', 'pok' );
			$fields[ $type . '_district' ]['placeholder']  = __( 'Select District', 'pok' );
			$fields[ $type . '_district' ]['type']         = 'select';
			$fields[ $type . '_district' ]['required']     = true;
			$fields[ $type . '_district' ]['options']      = array( '' => __( 'Select District', 'pok' ) );
			$fields[ $type . '_district' ]['class']        = isset( $fields[ $type . '_district' ]['class'] ) ? array_merge( $fields[ $type . '_district' ]['class'], array( 'update_totals_on_change', 'address-field' ) ) : array( 'update_totals_on_change', 'address-field' );
			$fields[ $type . '_district' ]['priority']     = $this->field_order['district'];
		}

		// sort fields.
		uasort( $fields, array( $this, 'sort_field_by_priority' ) );

		return $fields;
	}

	/**
	 * Sort checkout fields based on priority.
	 *
	 * @param  array $x Field.
	 * @param  array $y Field.
	 * @return int      Diff.
	 */
	private function sort_field_by_priority( $x, $y ) {
		return ( isset( $x['priority'] ) ? $x['priority'] : 50 ) - ( isset( $y['priority'] ) ? $y['priority'] : 50 );
	}

	/**
	 * Custom checkout fields that can't modified by custom_checkout_fields hooks
	 *
	 * @param  array $fields Default checkout fields.
	 * @return array         Default checkout fields.
	 */
	public function custom_special_checkout_fields( $fields ) {
		$fields['postcode']['required'] = false;
		return $fields;
	}

	/**
	 * Get country locale
	 *
	 * @param  array $fields Fields.
	 * @return array         Fields.
	 */
	public function country_locale( $fields ) {
		$fields['ID']['state']['label'] = __( 'Province', 'pok' );
		$fields['ID']['postcode']['label'] = __( 'Postcode / ZIP', 'pok' );
		$fields['ID']['city']['label'] = __( 'Town / City', 'pok' );
		return $fields;
	}

	/**
	 * Load scripts
	 */
	public function enqueue_scripts() {
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			if ( is_checkout() || is_account_page() ) {
				wp_enqueue_style( 'pok-checkout', POK_PLUGIN_URL . '/assets/css/checkout.css', array(), POK_VERSION );
				wp_enqueue_script( 'pok-checkout', POK_PLUGIN_URL . '/assets/js/checkout.js', array( 'jquery' ), POK_VERSION, true );
				$localize = array(
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'labelFailedCity'       => __( 'Failed to load city list. Try again?', 'pok' ),
					'labelFailedDistrict'   => __( 'Failed to load district list. Try again?', 'pok' ),
					'labelSelectCity'       => __( 'Select City', 'pok' ),
					'labelLoadingCity'      => __( 'Loading city options...', 'pok' ),
					'labelSelectDistrict'   => __( 'Select District', 'pok' ),
					'labelLoadingDistrict'  => __( 'Loading district options...', 'pok' ),
					'enableDistrict'        => false,
					'billing_country'       => $this->helper->get_country_session( 'billing' ),
					'shipping_country'      => $this->helper->get_country_session( 'shipping' ),
					'loadReturningUserData' => is_account_page() ? 'yes' : $this->setting->get( 'auto_fill_address' ),
					'billing_state'         => 0,
					'shipping_state'        => 0,
					'billing_city'          => 0,
					'shipping_city'         => 0,
					'billing_district'      => 0,
					'shipping_district'     => 0,
					'nonce_change_country'  => wp_create_nonce( 'change_country' ),
					'nonce_get_list_city'   => wp_create_nonce( 'get_list_city' ),
					'nonce_get_list_district' => wp_create_nonce( 'get_list_district' ),
				);
				// check if district is displayed.
				if ( 'nusantara' === $this->setting->get( 'base_api' ) || ( 'rajaongkir' === $this->setting->get( 'base_api' ) && 'pro' === $this->setting->get( 'rajaongkir_type' ) ) ) {
					$localize['enableDistrict'] = true;
				}
				// get returning user data.
				if ( is_user_logged_in() && ( is_account_page() || $this->setting->get( 'auto_fill_address' ) ) ) {
					$user_id = get_current_user_id();
					$billing_state = get_user_meta( $user_id, 'billing_state', true );
					if ( '' !== $billing_state ) {
						$localize['billing_state'] = $billing_state;
					}
					$shipping_state = get_user_meta( $user_id, 'shipping_state', true );
					if ( '' !== $shipping_state ) {
						$localize['shipping_state'] = $shipping_state;
					}
					$billing_city = get_user_meta( $user_id, 'billing_city', true );
					if ( '' !== $billing_city ) {
						$localize['billing_city'] = $billing_city;
					}
					$shipping_city = get_user_meta( $user_id, 'shipping_city', true );
					if ( '' !== $shipping_city ) {
						$localize['shipping_city'] = $shipping_city;
					}
					$billing_district = get_user_meta( $user_id, 'billing_district', true );
					if ( '' !== $billing_district ) {
						$localize['billing_district'] = $billing_district;
					}
					$shipping_district = get_user_meta( $user_id, 'shipping_district', true );
					if ( '' !== $shipping_district ) {
						$localize['shipping_district'] = $shipping_district;
					}
				}
				wp_localize_script( 'pok-checkout', 'pok_checkout_data', $localize );
			}
		}
	}

	/**
	 * Custom address format
	 *
	 * @param  array $formats Address formats.
	 * @return array          Address formats.
	 */
	public function custom_address_format( $formats ) {
		$formats['ID'] = "{name}\n{company}\n{address_1}\n{address_2}\n{pok_district}{pok_city}\n{pok_state}\n{country}\n{postcode}";
		return $formats;
	}

	/**
	 * Custom address format replacements
	 *
	 * @param  array $replacements Replacement fields.
	 * @param  array $args         Address args.
	 * @return array               Replacement fields.
	 */
	public function custom_address_replacement( $replacements, $args ) {
		// set district & city name.
		$district = '';
		$city = isset( $args['city'] ) ? $args['city'] : '';
		if ( isset( $args['city'] ) && 0 !== intval( $args['city'] ) ) {
			$city_detail = $this->core->get_single_city( intval( $args['city'] ) );
			if ( $city_detail ) {
				$city = $city_detail->type . ' ' . $city_detail->nama;
			}
			if ( isset( $args['district'] ) && 0 !== intval( $args['district'] ) ) {
				$district_list = $this->core->get_district( intval( $args['city'] ) );
				foreach ( $district_list as $d ) {
					if ( intval( $d->id ) === intval( $args['district'] ) ) {
						$district = 'Kec. ' . $d->nama . "\n";
					}
				}
			}
		}

		// set state name.
		$province = isset( $args['state'] ) ? $args['state'] : '';
		if ( isset( $args['state'] ) && 0 !== intval( $args['state'] ) ) {
			$provinces = $this->core->get_province();
			if ( isset( $provinces[ intval( $args['state'] ) ] ) ) {
				$province = $provinces[ intval( $args['state'] ) ];
			}
		}

		$replacements['{pok_district}'] = $district;
		$replacements['{pok_city}']     = $city;
		$replacements['{pok_state}']    = $province;

		return $replacements;
	}

	/**
	 * Fix name formatting on myaccount page
	 *
	 * @param  array  $address     Address data.
	 * @param  int    $customer_id Customer ID.
	 * @param  string $name        Billing/Shipping.
	 * @return array               Address data.
	 */
	public function format_myaccount_address( $address, $customer_id, $name ) {
		$address['district'] = get_user_meta( $customer_id, $name . '_district', true );
		return $address;
	}

	/**
	 * Validate district on checkout
	 */
	public function validate_district() {
		$vendor = $this->setting->get( 'base_api' );
		$type   = $this->setting->get( 'rajaongkir_type' );
		if ( 'pro' === $type || 'rajaongkir' !== $vendor ) {
			if ( isset( $_POST['billing_country'] ) && 'ID' === $_POST['billing_country'] && ( ! isset( $_POST['billing_district'] ) || empty( $_POST['billing_district'] ) ) ) {
				wc_add_notice( __( '<b>Billing district</b> is required', 'pok' ), 'error' );
			}

			if ( isset( $_POST['ship_to_different_address'] ) && ! empty( $_POST['ship_to_different_address'] ) && isset( $_POST['shipping_country'] ) && 'ID' === $_POST['shipping_country'] ) {
				if ( ! isset( $_POST['shipping_district'] ) || empty( $_POST['shipping_district'] ) ) {
					wc_add_notice( __( '<b>Shipping district</b> is required', 'pok' ), 'error' );
				}
			}
		}

	}

	/**
	 * Update user meta on checkout
	 *
	 * @param  integer $order_id Order ID.
	 */
	public function update_order_meta( $order_id ) {
		global $woocommerce;
		$order = wc_get_order( $order_id );
		$user_id = version_compare( $woocommerce->version, '3.0', '>=' ) ? $order->get_user_id() : $order->user_id;
		if ( ! empty( $_POST['billing_district'] ) ) {
			update_user_meta( $user_id, 'billing_district', sanitize_text_field( wp_unslash( $_POST['billing_district'] ) ) );
		}
		if ( ! empty( $_POST['shipping_district'] ) ) {
			update_user_meta( $user_id, 'shipping_district', sanitize_text_field( wp_unslash( $_POST['shipping_district'] ) ) );
		}
	}

	/**
	 * Add district to order address
	 *
	 * @param array  $address Address data.
	 * @param string $type    Billing/shipping.
	 * @param object $order   Order object.
	 */
	public function set_address_district( $address, $type = 'billing', $order ) {
		$district = get_post_meta( $order->get_id(), '_' . $type . '_district', true );
		if ( ! empty( $district ) ) {
			$address['district'] = $district;
		}
		return $address;
	}

	/**
	 * Delete shipping cache
	 */
	public function delete_wc_cache() {
		$packages = WC()->cart->get_shipping_packages();
		foreach ( $packages as $key => $value ) {
			$shipping_session = "shipping_for_package_$key";
			WC()->session->__unset( $shipping_session );
		}
	}

	/**
	 * Add custom fee to checkout
	 */
	public function add_custom_fee() {
		// unique number.
		if ( 'yes' === $this->setting->get( 'unique_number' ) ) {
			if ( WC()->session->__isset( 'pok_random_number' ) ) {
				$number = WC()->session->get( 'pok_random_number' );
			} else {
				$number = $this->helper->random_number( $this->setting->get( 'unique_number_length' ) );
				WC()->session->set( 'pok_random_number', $number );
			}
			WC()->cart->add_fee( __( 'Unique Number', 'pok' ), $number );
		}
	}

	/**
	 * Show shipping weight on checkout
	 */
	public function show_shipping_weight() {
		if ( 'yes' === $this->setting->get( 'show_weight_on_checkout' ) && ! $this->helper->is_multi_vendor_addon_active() ) {
			if ( count( WC()->cart->get_cart() ) > 0 ) {
				$weight = 0;
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$weight += ( $this->helper->get_product_weight( $values['data'] ) * $values['quantity']);
				}
				?>
				<tr>
					<td class="product-name">
						<?php esc_html_e( 'Total Shipping Weight', 'pok' ); ?>
					</td>
					<td class="product-total">
						<?php
						$weight = $this->helper->weight_convert( $weight );
						if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
							echo esc_html( number_format( $weight, get_option( 'woocommerce_price_num_decimals' ), get_option( 'woocommerce_price_decimal_sep' ), get_option( 'woocommerce_price_thousand_sep' ) ) );
						} else {
							echo esc_html( $this->helper->round_weight( $weight ) < 1 ? 1 : $this->helper->round_weight( $weight ) );
						}
						?>
						Kg
					</td>
				</tr>
				<?php
			}
		}
	}

	/**
	 * Modify woocommerce setting page
	 *
	 * @param  array $fields Setting fields.
	 * @return array         Setting fields.
	 */
	public function modify_shipping_settings( $fields ) {
		if ( function_exists( 'array_column' ) ) {
			$key = array_search( 'woocommerce_enable_shipping_calc', array_column( $fields, 'id' ), true );
			if ( false !== $key ) {
				update_option( 'woocommerce_enable_shipping_calc', 'no' );
				$fields[ $key ]['custom_attributes']['disabled'] = 'disabled';
				$fields[ $key ]['desc'] .= ' (' . esc_html__( 'disabled by Plugin Ongkos Kirim', 'pok' ) . ')';
			}
		}
		return $fields;
	}

	/**
	 * Remove shipping from cart page
	 *
	 * @param  boolean $show_shipping Show shipping or not.
	 * @return boolean                Show shipping or not.
	 */
	public function remove_shipping_on_cart( $show_shipping ) {
		if ( is_cart() ) {
			return false;
		}
		return $show_shipping;
	}

}
