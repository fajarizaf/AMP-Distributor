<?php

/**
 * POK Helper Class
 */
class POK_Helper {

	/**
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler $license ) {
		$this->license = $license;
		$this->setting = new POK_Setting();
	}

	/**
	 * Get courier names
	 *
	 * @param  string $courier Coureir code.
	 * @return string          Courier name.
	 */
	public function get_courier_name( $courier = '' ) {
		$couriers = apply_filters(
			'pok_courier_names', array(
				'jne'       => 'JNE',
				'pos'       => 'POS Indonesia',
				'tiki'      => 'TIKI',
				'jnt'       => 'J&T',
				'j&t'       => 'J&T', // alias.
				'wahana'    => 'Wahana',
				'esl'       => 'ESL (Eka Sari Lorena)',
				'ncs'       => 'NCS',
				'pcp'       => 'PCP Express',
				'rpx'       => 'RPX',
				'pandu'     => 'Pandu Logistics',
				'sicepat'   => 'Sicepat',
				'pahala'    => 'Pahala Express',
				'cahaya'    => 'Cahaya Logistik',
				'sap'       => 'SAP Express',
				'jet'       => 'JET Express',
				'indah'     => 'Indah Cargo',
				'dse'       => '21 Express',
				'slis'      => 'Solusi Express',
				'expedito'  => 'Expedito',
				'first'     => 'First Logistics',
				'star'      => 'Star Cargo',
				'nss'       => 'NSS Express',
			)
		);
		if ( isset( $couriers[ $courier ] ) ) {
			return $couriers[ $courier ];
		}
		return false;
	}

	/**
	 * Sanitize service name from API
	 *
	 * @param  string $courier Courier name.
	 * @param  string $service Original service name.
	 * @param  string $type    Long or Short name?.
	 * @return string          Sanitized name
	 */
	public function convert_service_name( $courier, $service, $type = 'long' ) {
		$services = apply_filters(
			'pok_courier_service_names', array(
				'jne'   => array(
					'box1kg'    => array(
						'long'  => '@BOX1KG',
						'short' => '@BOX1KG',
					),
					'box3kg'    => array(
						'long'  => '@BOX3KG',
						'short' => '@BOX3KG',
					),
					'box5kg'    => array(
						'long'  => '@BOX5KG',
						'short' => '@BOX5KG',
					),
					'ctc'   => array(
						'long'  => 'CTC (City to City)',
						'short' => 'CTC',
					),
					'ctcbdo'    => array(
						'long'  => 'CTCBDO (City to City Bandung)',
						'short' => 'CTCBDO',
					),
					'ctcoke'    => array(
						'long'  => 'CTCOKE (City to City OKE)',
						'short' => 'CTCOKE',
					),
					'ctcsps'    => array(
						'long'  => 'CTCSPS (City to City SPS)',
						'short' => 'CTCSPS',
					),
					'ctcyes'    => array(
						'long'  => 'CTCYES (City to City YES)',
						'short' => 'CTCYES',
					),
					'jtr'   => array(
						'long'  => 'JTR (JNE Trucking)',
						'short' => 'JTR',
					),
					'oke'   => array(
						'long'  => 'OKE (Ongkos Kirim Ekonomis)',
						'short' => 'OKE',
					),
					'pelik' => array(
						'long'  => 'Pelikan',
						'short' => 'PELIKAN',
					),
					'popb'  => array(
						'long'  => 'Pop Box',
						'short' => 'POPBOX',
					),
					'reg'   => array(
						'long'  => 'REG (Layanan Reguler)',
						'short' => 'REG',
					),
					'sps'   => array(
						'long'  => 'SPS (Super Speed)',
						'short' => 'SPS',
					),
					'yes'   => array(
						'long'  => 'YES (Yakin Esok Sampai)',
						'short' => 'YES',
					),
				),
				'tiki'  => array(
					'economi-service-eco'   => array(
						'long'  => 'ECO (Economi Service)',
						'short' => 'ECO',
					),
					'holiday-delivery-service-hds'  => array(
						'long'  => 'HDS (Holiday Delivery Service)',
						'short' => 'HDS',
					),
					'over-night-service-ons'    => array(
						'long'  => 'ONS (Over Night Service)',
						'short' => 'ONS',
					),
					'regular-service-reg'   => array(
						'long'  => 'REG (Regular Service)',
						'short' => 'REG',
					),
					'same-day-service-sds'  => array(
						'long'  => 'SDS (Same Day Service)',
						'short' => 'SDS',
					),
					'trc'   => array(
						'long'  => 'TRC (Trucking Service)',
						'short' => 'TRC',
					),
					'two-day-service-tds'   => array(
						'long'  => 'TDS (Two Day Service)',
						'short' => 'TDS',
					),
				),
				'pos'   => array(
					'express-next-day-barang'   => array(
						'long'  => 'Express Next Day Barang',
						'short' => 'Express Next Day Barang',
					),
					'express-sameday-barang'    => array(
						'long'  => 'Express Sameday Barang',
						'short' => 'Express Sameday Barang',
					),
					'express-next-day'  => array(
						'long'  => 'Express Next Day',
						'short' => 'Express Next Day',
					),
					'express-sameday'   => array(
						'long'  => 'Express Sameday',
						'short' => 'Express Sameday',
					),
					'paket-jumbo-ekonomi'   => array(
						'long'  => 'Paket Jumbo Ekonomi',
						'short' => 'Paket Jumbo Ekonomi',
					),
					'paketpos-dangerous-goods'  => array(
						'long'  => 'Paketpos Dangerous Goods',
						'short' => 'Paketpos Dangerous Goods',
					),
					'paketpos-valuable-goods'   => array(
						'long'  => 'Paketpos Valuable Goods',
						'short' => 'Paketpos Valuable Goods',
					),
					'paket-kilat'   => array(
						'long'  => 'Paket Kilat',
						'short' => 'Paket Kilat',
					),
					'paket-kilat-khusus'    => array(
						'long'  => 'Paket Kilat Khusus',
						'short' => 'Paket Kilat Khusus',
					),
					'paketpos-biasa'    => array(
						'long'  => 'Paketpos Biasa',
						'short' => 'Paketpos Biasa',
					),
				),
				'jnt'   => array(
					'ez'    => array(
						'long'  => 'EZ',
						'short' => 'EZ',
					),
				),
				'j&t'   => array( // alias.
					'ez'    => array(
						'long'  => 'EZ',
						'short' => 'EZ',
					),
				),
			)
		);
		if ( isset( $services[ strtolower( $courier ) ][ sanitize_title( $service ) ][ $type ] ) ) {
			return $services[ strtolower( $courier ) ][ sanitize_title( $service ) ][ $type ];
		}
		return $service;
	}

	/**
	 * Get license type
	 *
	 * @return string Type.
	 */
	public function get_license_type() {
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			return 'pro';
		} else {
			if ( 'pro' === $this->setting->get( 'rajaongkir_type' ) ) {
				return 'pro';
			} else {
				return 'default';
			}
		}
	}

	/**
	 * Get country on session
	 *
	 * @param  string $context    Context.
	 * @return string Country id.
	 */
	public function get_country_session( $context = 'billing' ) {
		if ( 'billing' === $context ) {
			$session_name = 'country';
		} else {
			$session_name = 'shipping_country';
		}
		$customer = maybe_unserialize( WC()->session->get( 'customer' ) );
		return isset( $customer[ $session_name ] ) ? $customer[ $session_name ] : 'ID';
	}

	/**
	 * Check the license status
	 *
	 * @return boolean License status.
	 */
	public function is_license_active() {
		return $this->license->get( 'active' );
	}

	/**
	 * Get rajaongkir status
	 *
	 * @return boolean Rajaongkir status
	 */
	public function is_rajaongkir_active() {
		$rajaongkir_key = $this->setting->get( 'rajaongkir_key' );
		if ( empty( $rajaongkir_key ) ) {
			return false;
		}
		$rajaongkir_status = $this->setting->get( 'rajaongkir_status' );
		if ( false === $rajaongkir_status[0] ) {
			return false;
		}
		return true;
	}

	/**
	 * Is plugin active
	 *
	 * @return boolean Status.
	 */
	public function is_plugin_active() {
		// for front.
		if ( 'no' === $this->setting->get( 'enable' ) ) {
			return false;
		}

		// plugin ongkos kirim license status.
		if ( ! $this->is_license_active() ) {
			return false;
		}

		// curl must active.
		if ( ! function_exists( 'curl_version' ) ) {
			return false;
		}

		// rajaongkir status.
		if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			if ( ! $this->is_rajaongkir_active() ) {
				return false;
			}
		}

		// base city.
		$base_city = $this->setting->get( 'store_location' );
		if ( empty( $base_city ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is admin active
	 *
	 * @return boolean Status
	 */
	public function is_admin_active() {
		// for admin.
		// plugin ongkos kirim license status.
		if ( ! $this->is_license_active() ) {
			return false;
		}

		// rajaongkir status.
		if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			if ( ! $this->is_rajaongkir_active() ) {
				return false;
			}
		}

		if ( $this->license->is_license_expired() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if woocommerce is active
	 *
	 * @return boolean Is active.
	 */
	public static function is_woocommerce_active() {
		return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
	}

	/**
	 * Clear all cached WC's shipping costs
	 */
	public function clear_cached_costs() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%wc_ship%'" );
	}

	/**
	 * Get product weight.
	 * If volume calculation enabled, the weight is calculated from product dimensions
	 * But if product weight is higher than weight from calculated dimensions, use the product weight instead.
	 *
	 * @param  object $product Product object.
	 * @return float           Product weight on kg.
	 */
	public function get_product_weight( $product ) {
		if ( 'yes' === $this->setting->get( 'enable_volume_calculation' ) && ! empty( $product->get_length() ) && ! empty( $product->get_width() ) && ! empty( $product->get_height() ) ) {
			$product_weight = ( $this->dimension_convert( $product->get_length() ) * $this->dimension_convert( $product->get_width() ) * $this->dimension_convert( $product->get_height() ) ) / 6000;
			if ( $product->has_weight() ) {
				$product_weight = max( $product_weight, $this->weight_convert( $product->get_weight() ) ); // get highest value between volumetric or weight.
			}
		} else {
			$product_weight = $product->has_weight() ? $this->weight_convert( $product->get_weight() ) : $this->setting->get( 'default_weight' );
		}
		return apply_filters( 'pok_get_product_weight', $product_weight, $product, $this->setting->get_all() );
	}

	/**
	 * Round weight
	 *
	 * @param  float $weight Weight.
	 * @return int           Rounded weight.
	 */
	public function round_weight( $weight = 0 ) {
		$method = $this->setting->get( 'round_weight' );
		if ( 'ceil' === $method ) {
			$round = ceil( $weight );
		} elseif ( 'floor' === $method ) {
			$round = floor( $weight );
		} else {
			$tolerance = $this->setting->get( 'round_weight_tolerance' ) / 1000;
			$fraction = fmod( $weight, 1 );
			if ( $fraction <= $tolerance ) {
				$round = floor( $weight );
			} else {
				$round = ceil( $weight );
			}
		}
		return apply_filters( 'pok_round_weight', $round );
	}

	public function get_total_weight( $contents ) {
		$weight = 0;
		foreach ( $contents as $content ) {
			if ( 'set' === $this->setting->get( 'enable_insurance' ) && ( 'yes' === get_post_meta( $content['data']->get_id(), 'enable_insurance', true ) || 'yes' === get_post_meta( $content['data']->get_parent_id(), 'enable_insurance', true ) ) ) {
				$enable_insurance = true;
			}
			if ( 'set' === $this->setting->get( 'enable_timber_packing' ) && ( 'yes' === get_post_meta( $content['data']->get_id(), 'enable_timber_packing', true ) || 'yes' === get_post_meta( $content['data']->get_parent_id(), 'enable_timber_packing', true ) ) ) {
				$enable_timber_packing = true;
			}
			$weight += ( $this->get_product_weight( $content['data'] ) * $content['quantity'] );
		}
		return $weight;
	}

	/**
	 * Convert current weight to kilo
	 *
	 * @param  float $weight Current weight.
	 * @return float          Converted weight.
	 */
	public function weight_convert( $weight = 0 ) {
		$wc_unit = strtolower( get_option( 'woocommerce_weight_unit', 'kg' ) );
		if ( 'kg' !== $wc_unit ) {
			switch ( $wc_unit ) {
				case 'g':
					$weight *= 0.001;
					break;
				case 'lbs':
					$weight *= 0.4535;
					break;
				case 'oz':
					$weight *= 0.0283495;
					break;
			}
		}
		return apply_filters( 'pok_weight_convert', $weight );
	}

	/**
	 * Convert current dimension to cm
	 *
	 * @param  float $dimension Current dimension.
	 * @return float            Converted dimension.
	 */
	public function dimension_convert( $dimension = 0 ) {
		$dimension = floatval( $dimension );
		$wc_unit = strtolower( get_option( 'woocommerce_dimension_unit', 'cm' ) );
		if ( 'cm' !== $wc_unit ) {
			switch ( $wc_unit ) {
				case 'm':
					$dimension *= 100;
					break;
				case 'mm':
					$dimension *= 0.1;
					break;
				case 'in':
					$dimension *= 2.54;
					break;
				case 'yd':
					$dimension *= 91.44;
					break;
			}
		}
		return apply_filters( 'pok_dimension_convert', $dimension );
	}

	/**
	 * Get insurance from specific courier
	 *
	 * @param  string $courier Courier type.
	 * @param  float  $total   Total price (optional).
	 * @return float           Insurance fee.
	 */
	public function get_insurance( $courier, $total = null ) {
		if ( is_null( $total ) ) {
			$total = WC()->cart->get_subtotal();
		}
		if ( 0 === floatval( $total ) ) {
			return 0;
		}
		$insurance = 0;
		switch ( $courier ) {
			case 'jne':
				$insurance = ( 0.002 * $total ) + 5000;
				break;
			case 'pos':
				$insurance = 0.0024 * $total;
				break;
			case 'tiki':
				$insurance = 0.003 * $total;
				break;
			case 'jnt':
				$insurance = 0.0025 * $total;
				break;
			case 'j&t': // alias.
				$insurance = 0.0025 * $total;
				break;
			case 'wahana':
				$insurance = 0.005 * $total;
				break;
			case 'rpx':
				if ( 20000000 <= $total ) {
					$insurance = 0.005 * $total;
				}
				break;
			case 'esl':
				$insurance = ( 0.002 * $total ) + 5000;
				break;
			case 'pandu':
				$insurance = 0.0035 * $total;
				break;
			case 'pahala':
				$insurance = 0.002 * $total;
				break;
			case 'indah':
				if ( ( 0.003 * $total ) < 101000 ) {
					$insurance = 101000;
				} else {
					$insurance = 0.003 * $total;
				}
				break;
			case 'star':
				if ( ( 0.003 * $total ) < 101000 ) {
					$insurance = 101000;
				} else {
					$insurance = 0.003 * $total;
				}
				break;
		}
		return apply_filters( 'pok_set_insurance', round( $insurance ), $courier, $total );
	}

	/**
	 * Convert rajaongkir country name
	 *
	 * @param  string $country Country name.
	 * @return string          Country name.
	 */
	public function rajaongkir_country_name( $country ) {
		// TODO: for future development, consider using Rajaongkir's countries data rather than Woocommerce's.
		$countries = array(
			'China'         => 'China (people_s rep)',
			'Iran'          => 'Iran (Islamic rep)',
			'Korea'         => 'Korea (rep)',
			'Laos'          => 'Laos People_s Dem Rep',
			'United States (US)' => 'United States of America',
			'Hong Kong'     => 'Hongkong',
			// dst...
		);
		if ( ! empty( $countries[ $country ] ) ) {
			return $countries[ $country ];
		}
		return $country;
	}

	/**
	 * Generate random numbers
	 *
	 * @param  integer $length Length.
	 * @return integer         Random number.
	 */
	public function random_number( $length = 1 ) {
		$char = '0123456789';
		$string = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$pos = rand( 0, strlen( $char ) - 1 );
			$string .= $char{$pos};
		}
		return intval( $string );
	}

	/**
	 * Convert currency
	 *
	 * @param  float  $price  Current price.
	 * @param  string $symbol Currency symbol.
	 * @return float          Converted price.
	 */
	public function currency_convert( $price = 0, $symbol = '' ) {
		global $pok_core;
		$rates = $pok_core->get_currency_rates();
		if ( empty( $symbol ) ) {
			$symbol = get_option( 'woocommerce_currency' );
		}
		if ( 'IDR' !== $symbol && ! empty( $rates['data'] ) && ! empty( $rates['data']->rates ) ) {
			$conv = (array) $rates['data']->rates;
			return $price * (float) $conv[ $symbol ];
		}
		return $price;
	}

	/**
	 * Check if multi vendor addon active
	 *
	 * @return boolean Is active?
	 */
	public function is_multi_vendor_addon_active() {
		return in_array( 'plugin-ongkos-kirim-multi-vendor/plugin-ongkos-kirim-multi-vendor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) && defined( 'POKMV_VERSION' );
	}

	/**
	 * Is insurance enable on given products
	 *
	 * @param  array $contents Cart contents.
	 * @return boolean           Enabled or not.
	 */
	public function is_enable_insurance( $contents ) {
		$enable = ( 'yes' === $this->setting->get( 'enable_insurance' ) ? true : false );
		foreach ( $contents as $content ) {
			if ( 'set' === $this->setting->get( 'enable_insurance' ) && ( 'yes' === get_post_meta( $content['data']->get_id(), 'enable_insurance', true ) || 'yes' === get_post_meta( $content['data']->get_parent_id(), 'enable_insurance', true ) ) ) {
				$enable = true;
			}
		}
		return $enable;
	}

	/**
	 * Is timber packing enable on given products
	 *
	 * @param  array $contents Cart contents.
	 * @return boolean           Enabled or not.
	 */
	public function is_enable_timber_packing( $contents ) {
		$enable = ( 'yes' === $this->setting->get( 'enable_insurance' ) ? true : false );
		foreach ( $contents as $content ) {
			if ( 'set' === $this->setting->get( 'enable_timber_packing' ) && ( 'yes' === get_post_meta( $content['data']->get_id(), 'enable_timber_packing', true ) || 'yes' === get_post_meta( $content['data']->get_parent_id(), 'enable_timber_packing', true ) ) ) {
				$enable = true;
			}
		}
		return $enable;
	}

}
