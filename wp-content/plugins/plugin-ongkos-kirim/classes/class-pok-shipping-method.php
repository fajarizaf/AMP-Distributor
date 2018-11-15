<?php

/**
 * POK Shipping Method
 */
class POK_Shipping_Method extends WC_Shipping_Method {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->id                   = 'plugin_ongkos_kirim';
		$this->method_title         = __( 'Plugin Ongkos Kirim', 'pok' );
		$this->method_description   = __( 'Shipping Method for Indonesia Marketplace', 'pok' );
		$this->enabled              = 'yes';
		$this->title                = __( 'Plugin Ongkos Kirim', 'pok' );
		$this->core                 = $pok_core;
		$this->setting              = new POK_Setting();
		$this->helper               = $pok_helper;
		$this->type                 = $this->helper->get_license_type();
		add_action( 'pok_calculate_shipping', array( $this, 'pok_calculate_shipping' ), 30 );
	}

	/**
	 * Display admin options
	 */
	public function admin_options() {
		include_once POK_PLUGIN_PATH . 'views/setting-wc.php';
	}

	/**
	 * Calculate shipping cost
	 *
	 * @param  array $package Packages.
	 */
	public function calculate_shipping( $package = array() ) {
		global $woocommerce;

		$rates = array();
		$final_rates = array();

		if ( ! $this->helper->is_plugin_active() ) {
			return false;
		}

		if ( empty( $package ) ) {
			return false;
		}

		// clear all cached WC's shipping costs.
		$this->helper->clear_cached_costs();

		do_action( 'pok_calculate_shipping', $package, $this );

	}

	/**
	 * POK's Calculate Shipping cost
	 *
	 * @param  array $package Packages.
	 */
	public function pok_calculate_shipping( $package ) {
		$destination = $package['destination'];

		$enable_insurance       = ( 'yes' === $this->setting->get( 'enable_insurance' ) ? true : false );
		$enable_timber_packing  = ( 'yes' === $this->setting->get( 'enable_timber_packing' ) ? true : false );

		if ( ! isset( $package['weight'] ) ) {
			$weight = $this->helper->get_total_weight( $package['contents'] );
		} else {
			$weight = $package['weight'];
		}

		if ( 'ID' === $destination['country'] ) {
			// get destination.
			if ( 'pro' === $this->type ) { // get district (not provided by WC by default).
				if ( isset( $_POST['post_data'] ) ) { // checkout page.
					if ( '1' === $this->get_checkout_post_data( 'ship_to_different_address' ) ) {
						$district = $this->get_checkout_post_data( 'shipping_district' );
					} else {
						$district = $this->get_checkout_post_data( 'billing_district' );
					}
				} else { // order detail (after checkout).
					if ( isset( $_POST['shipping_district'] ) && ! empty( $_POST['shipping_district'] ) ) {
						$district = sanitize_text_field( wp_unslash( $_POST['shipping_district'] ) );
					} elseif ( isset( $_POST['billing_district'] ) && ! empty( $_POST['billing_district'] ) ) {
						$district = sanitize_text_field( wp_unslash( $_POST['billing_district'] ) );
					}
				}
				if ( ! empty( $district ) ) {
					$destination['district'] = intval( $district );
					$destination_id = intval( $district );
				}
				$destination_type = 'district';
			} else {
				$destination_id = intval( $destination['city'] );
				$destination_type = 'city';
			}
			// get costs.
			if ( isset( $destination_type ) && ! empty( $destination_id ) ) {
				$rates          = $this->core->get_cost( $destination_id, $weight, ( isset( $package['origin'] ) ? intval( $package['origin'] ) : 0 ) );
				$custom_costs   = $this->core->get_custom_cost( $destination, $destination_type, $weight );
				if ( 'replace' === $this->setting->get( 'custom_cost_type' ) && ! empty( $custom_costs ) ) {
					$rates = $custom_costs;
				} else {
					if ( ! empty( $custom_costs ) ) {
						$rates = array_merge( $custom_costs, $rates );
					}
				}
			}
		} elseif ( 'rajaongkir' === $this->setting->get( 'base_api' ) && 'starter' !== $this->setting->get( 'rajaongkir_type' ) && 'yes' === $this->setting->get( 'international_shipping' ) ) { // international shipping.
			$country_name = WC()->countries->countries[ $destination['country'] ];
			$country_data = $this->core->get_all_country();
			$destination_id = array_search( $this->helper->rajaongkir_country_name( $country_name ), $country_data, true );
			if ( $destination_id ) {
				$rates = $this->core->get_cost_international( $destination_id, $weight );
			}
		}

		if ( ! empty( $rates ) ) {

			foreach ( $rates as $i => $rate ) {
				$final_rate = array();
				$meta = array(
					'courier'   => $rate['courier'],
					'etd'       => $rate['time'],
				);

				// if multi vendor active.
				if ( isset( $package['vendor_id'] ) ) {
					$meta['seller_id'] = $package['vendor_id'];
				}

				// if filter courier active.
				if ( 'yes' === $this->setting->get( 'specific_service' ) && ( ! isset( $rate['source'] ) || 'custom' !== $rate['source'] ) ) {
					if ( ! in_array( sanitize_title( $rate['class'] ), $this->setting->get( 'specific_service_option' ), true ) ) {
						continue;
					}
				}

				// add timber packing.
				if ( true === $enable_timber_packing ) {
					$meta['timber_packing'] = apply_filters( 'pok_timber_packing_fee', floatval( $this->setting->get( 'timber_packing_multiplier' ) ) * $rate['cost'], $rate['courier'] );
					$rate['cost'] += $meta['timber_packing'];
				}

				// add insurance fee.
				if ( true === $enable_insurance ) {
					$meta['insurance'] = $this->helper->get_insurance( $rate['courier'], array_sum( wp_list_pluck( $package['contents'], 'line_total' ) ) );
					$rate['cost'] += $meta['insurance'];
				}

				// additional fee.
				if ( 'yes' === $this->setting->get( 'markup_fee' ) ) {
					$meta['markup'] = apply_filters( 'pok_custom_markup', floatval( $this->setting->get( 'markup_fee_amount' ) ), $rate );
					$rate['cost'] += $meta['markup'];
				}

				$label = strtoupper( $rate['courier'] );
				if ( ! empty( $rate['service'] ) && '-' !== $rate['service'] ) {
					$label .= ' - ';
					if ( isset( $rate['source'] ) && 'custom' === $rate['source'] ) {
						$label .= $rate['service'];
					} else {
						$label .= $this->helper->convert_service_name( $rate['courier'], $rate['service'], 'yes' === $this->setting->get( 'show_long_description' ) ? 'long' : 'short' );
					}
				}

				$final_rate = apply_filters(
					'pok_rate', array(
						'id'        => 'pok-' . $rate['courier'] . '-' . $i,
						'label'     => $label,
						'cost'      => $rate['cost'],
						'meta_data' => $meta,
					), $rate, $package
				);
				$this->add_rate( $final_rate );
			}
		}
	}

	/**
	 * Get checkout post data.
	 *
	 * @param  string $field Checkout field.
	 * @return mixed         Checkout field data.
	 */
	private function get_checkout_post_data( $field ) {
		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $return );
			$return = str_replace( '+',' ',$return );
			if ( isset( $return[ $field ] ) ) {
				return $return[ $field ];
			}
		}
		return false;
	}
}
