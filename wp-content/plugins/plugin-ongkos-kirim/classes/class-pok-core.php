<?php

/**
 * POK Core class
 */
class POK_Core {

	/**
	 * POK API
	 *
	 * @var object
	 */
	protected $api;

	/**
	 * Cache key prefix
	 *
	 * @var string
	 */
	protected $key_prefix;

	/**
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler $license ) {
		global $pok_helper;
		$this->api = new POK_API( $license );
		$this->setting = new POK_Setting();
		$this->key_prefix = 'pok_data_';
		$this->enable_cache = ! POK_DEBUG; // for debugging purpose.
		$this->helper = $pok_helper;
	}

	/**
	 * Check cache
	 *
	 * @param  string $key Cache key.
	 * @return boolean      Is exists or not
	 */
	private function is_cache_exists( $key ) {
		if ( $this->enable_cache ) {
			$data = get_option( $this->key_prefix . sanitize_title_for_query( $key ), false );
			if ( $data && ! empty( $data ) ) {
				if ( false !== get_transient( $this->key_prefix . sanitize_title_for_query( $key ) ) ) {
					return true;
				}
			} else {
				delete_transient( $this->key_prefix . sanitize_title_for_query( $key ) );
			}
		}
		return false;
	}

	/**
	 * Cache requested data
	 *
	 * @param  string  $key        Cache key.
	 * @param  mixed   $new_value  Cache value.
	 * @param  integer $expiration Cache expiration in seconds.
	 * @return mixed               Cached data.
	 */
	private function cache_it( $key, $new_value = null, $expiration = 86400 ) {
		$expiration = 60 * 60 * $expiration;
		if ( ! is_null( $new_value ) ) {
			if ( $this->enable_cache ) {
				update_option( $this->key_prefix . sanitize_title_for_query( $key ), $new_value, 'no' );
				set_transient( $this->key_prefix . sanitize_title_for_query( $key ), true, $expiration ); // we store data with option, so no need to set value on transient.
			}
			$return = $new_value;
		} else {
			$return = get_option( $this->key_prefix . sanitize_title_for_query( $key ), false );
		}
		return $return;
	}

	/**
	 * Delete all cached data by type
	 *
	 * @param  string $key_type Key type.
	 */
	public function purge_cache( $key_type = '' ) {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s", array(
					$this->key_prefix . $key_type . '%',
					'_transient_' . $this->key_prefix . $key_type . '%',
					'_transient_timeout_' . $this->key_prefix . $key_type . '%',
				)
			)
		);
	}

	/**
	 * Delete cache by key
	 *
	 * @param  string $key Cache key.
	 */
	public function delete_cache( $key ) {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s", array(
					$this->key_prefix . sanitize_title_for_query( $key ),
					'_transient_' . $this->key_prefix . sanitize_title_for_query( $key ),
					'_transient_timeout_' . $this->key_prefix . sanitize_title_for_query( $key ),
				)
			)
		);
	}

	/**
	 * Get courier options
	 *
	 * @param  string $vendor Vendor name.
	 * @param  string $type   Rajaongkir type.
	 * @return array          Courier list.
	 */
	public function get_courier( $vendor = 'nusantara', $type = 'pro' ) {
		if ( '' === $vendor ) {
			$vendor = $this->setting->get( 'base_api' );
		}
		if ( '' === $type ) {
			$type = $this->setting->get( 'rajaongkir_type' );
		}
		if ( 'nusantara' === $vendor ) { // tonjoo.
			$courier = array( 'jne', 'pos', 'tiki', 'jnt' );
		} else {
			if ( 'pro' === $type ) { // rajaongkir pro.
				$courier = array( 'jne', 'pos', 'tiki', 'jnt', 'wahana', 'esl', 'ncs', 'pcp', 'rpx', 'pandu', 'sicepat', 'pahala', 'cahaya', 'sap', 'jet', 'indah', 'dse', 'slis', 'expedito', 'first', 'star', 'nss' );
			} elseif ( 'basic' === $type ) { // rajaongkir basic.
				$courier = array( 'jne', 'pos', 'tiki', 'pcp', 'rpx', 'esl' );
			} else { // rajaongkir free.
				$courier = array( 'jne', 'pos', 'tiki' );
			}
		}
		return apply_filters( 'pok_couriers', $courier, $vendor, $type );
	}

	/**
	 * Get all couriers
	 *
	 * @return array All couriers.
	 */
	public function get_all_couriers() {
		return apply_filters( 'pok_all_couriers', array( 'jne', 'pos', 'tiki', 'jnt', 'wahana', 'esl', 'ncs', 'pcp', 'rpx', 'pandu', 'sicepat', 'pahala', 'cahaya', 'sap', 'jet', 'indah', 'dse', 'slis', 'expedito', 'first', 'star', 'nss' ) );
	}

	/**
	 * Get courier package (only for API tonjoo)
	 *
	 * @return array Courier package
	 */
	public function get_courier_service() {
		if ( ! $this->is_cache_exists( 'courier_service' ) ) {
			$result = $this->api->get_courier_service();
		}
		return $this->cache_it( 'courier_service', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_costs' ) );
	}

	/**
	 * Get province options
	 *
	 * @return array Province options
	 */
	public function get_province() {
		if ( ! $this->is_cache_exists( 'province' ) ) {
			$result = $this->api->get_province();
		}
		return $this->cache_it( 'province', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get city options based on province id
	 *
	 * @param  integer $province_id Province ID.
	 * @return array                City list.
	 */
	public function get_city( $province_id = 0 ) {
		if ( ! $this->is_cache_exists( 'city_' . $province_id ) ) {
			$result = $this->api->get_city( $province_id );
		}
		return $this->cache_it( 'city_' . $province_id, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get single city by id
	 *
	 * @param  integer $city_id City ID.
	 * @return array            City details.
	 */
	public function get_single_city( $city_id = 0 ) {
		if ( ! $this->is_cache_exists( 'city_single_' . $city_id ) ) {
			$result = $this->api->get_single_city( $city_id );
		}
		return $this->cache_it( 'city_single_' . $city_id, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get all city (only for API rajaongkir)
	 *
	 * @return array City list
	 */
	public function get_all_city() {
		if ( ! $this->is_cache_exists( 'all_city' ) ) {
			$result = $this->api->get_all_city();
		}
		return $this->cache_it( 'all_city', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Search city (only for API tonjoo)
	 *
	 * @param  string $search Search param.
	 * @return array          City list.
	 */
	public function search_city( $search = '' ) {
		if ( ! $this->is_cache_exists( 'search_city_' . $search ) ) {
			$result = $this->api->search_city( $search );
		}
		return $this->cache_it( 'search_city_' . $search, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get district by the City ID
	 *
	 * @param  integer $city_id City ID.
	 * @return array            District list.
	 */
	public function get_district( $city_id = 0 ) {
		if ( ! $this->is_cache_exists( 'district_' . $city_id ) ) {
			$result = $this->api->get_district( $city_id );
		}
		return $this->cache_it( 'district_' . $city_id, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get all countries
	 *
	 * @return array Contry
	 */
	public function get_all_country() {
		if ( ! $this->is_cache_exists( 'country' ) ) {
			$result = $this->api->get_all_country();
		}
		return $this->cache_it( 'country', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $destination Destination ID (city or district).
	 * @param  integer $weight      Weight in kilograms.
	 * @param  integer $set_origin  Set this if wanna get cost from specific origin (ignore setting).
	 * @return array                Costs.
	 */
	public function get_cost( $destination = 0, $weight, $set_origin = 0 ) {
		$courier = $this->setting->get( 'couriers' );
		$store_location = $this->setting->get( 'store_location' );
		if ( $weight < 1 ) {
			$weight = 1;
		}
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			$weight = $this->helper->round_weight( $weight );
		}
		$weight = $weight * 1000;
		if ( ( false === $store_location || empty( $store_location ) || empty( $store_location[0] ) ) && 0 === $set_origin ) {
			return false;
		}
		$origin = apply_filters( 'pok_origin', ( 0 < intval( $set_origin ) ? $set_origin : $store_location[0] ) ); // wrap it with filters, given the ability to change the origin.
		if ( ! $this->is_cache_exists( 'cost_' . $origin . '_' . $destination . '_' . $weight ) ) {
			$result = $this->api->get_cost( $origin, $destination, $weight, $courier );
		}
		return $this->cache_it( 'cost_' . $origin . '_' . $destination . '_' . $weight, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_costs' ) );
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $destination Destination ID (country).
	 * @param  integer $weight      Weight in kilograms.
	 * @param  integer $set_origin  Set this if wanna get cost from specific origin (ignore setting).
	 * @return array                Costs.
	 */
	public function get_cost_international( $destination, $weight, $set_origin = 0 ) {
		$courier = $this->setting->get( 'couriers' );
		$store_location = $this->setting->get( 'store_location' );
		$weight = $weight * 1000;
		if ( ( false === $store_location || empty( $store_location ) || empty( $store_location[0] ) ) && 0 === $set_origin ) {
			return false;
		}
		$origin = apply_filters( 'pok_origin', ( 0 < intval( $set_origin ) ? $set_origin : $store_location[0] ) ); // wrap it with filters, given the ability to change the origin.
		if ( ! $this->is_cache_exists( 'cost_international_' . $origin . '_' . $destination . '_' . $weight ) ) {
			$result = $this->api->get_cost_international( $origin, $destination, $weight, $courier );
		}
		return $this->cache_it( 'cost_international_' . $origin . '_' . $destination . '_' . $weight, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_costs' ) );
	}

	/**
	 * Get custom shipping cost
	 *
	 * @param  integer $destination      Destination ID (city/district).
	 * @param  string  $destination_type Destination type (city/district).
	 * @param  integer $weight           Weight in grams.
	 * @return array                     Costs.
	 */
	public function get_custom_cost( $destination, $destination_type, $weight ) {
		$data = $this->setting->get_custom_costs();
		$custom_courier = $this->setting->get( 'custom_cost_courier' );
		$weight = $this->helper->round_weight( $weight );
		$costs = array();
		if ( ! empty( $data ) ) {
			foreach ( $data as $d ) {
				$found = false;
				if ( '*' === $d['province_id'] ) {
					$found = true;
				} elseif ( intval( $destination['state'] ) === intval( $d['province_id'] ) ) {
					if ( '*' === $d['city_id'] ) {
						$found = true;
					} elseif ( intval( $destination['city'] ) === intval( $d['city_id'] ) ) {
						if ( 'city' === $destination_type ) {
							$found = true;
						} elseif ( 'district' === $destination_type ) {
							if ( '*' === $d['district_id'] || intval( $destination['district'] ) === intval( $d['district_id'] ) ) {
								$found = true;
							}
						}
					}
				}
				if ( $found ) {
					$courier = 'custom' === $d['courier'] ? $custom_courier : $d['courier'];
					if ( ( empty( $d['package_name'] ) || '-' === $d['package_name'] ) && ( '' === $courier ) ) {
						$class = __( 'Custom Shipping', 'pok' );
					} else {
						if ( empty( $d['package_name'] ) || '-' === $d['package_name'] ) {
							$class = strtoupper( $courier );
						} elseif ( '' === $courier ) {
							$class = $d['package_name'];
						} else {
							$class = strtoupper( $courier ) . ' - ' . $d['package_name'];
						}
					}
					$costs[] = array(
						'class'         => $class,
						'courier'       => strtolower( $courier ),
						'service'       => $d['package_name'],
						'description'   => 'custom',
						'cost'          => floatval( $d['cost'] ) * $weight,
						'time'          => '-',
						'source'        => 'custom',
					);
				}
			}
		}
		return $costs;
	}

	/**
	 * Get currency rates
	 *
	 * @return array Cached rates.
	 */
	public function get_currency_rates() {
		if ( ! $this->is_cache_exists( 'currency' ) ) {
			$result = $this->api->currency();
		}
		return $this->cache_it( 'currency', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_costs' ) );
	}

	/**
	 * Get rajaongkir status
	 *
	 * @param  string $api_key  API key.
	 * @param  string $api_type API type.
	 * @return boolean          API status.
	 */
	public function get_rajaongkir_status( $api_key, $api_type ) {
		return $this->api->get_rajaongkir_status( $api_key, $api_type );
	}
}
