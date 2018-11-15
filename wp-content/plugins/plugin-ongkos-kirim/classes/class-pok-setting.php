<?php

/**
 * POK Setting
 */
class POK_Setting {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->option_name_setting      = 'pok_setting';
		$this->option_name_custom_costs = 'pok_custom_costs';
		$this->setting = get_option( $this->option_name_setting, $this->get_defaults() );
	}

	/**
	 * Get default values
	 *
	 * @param  string $index Defaults index.
	 * @return mixed         Default value.
	 */
	public function get_defaults( $index = null ) {
		$defaults = apply_filters(
			'pok_setting_defaults', array(
				'enable'                    => 'yes',
				'base_server'               => 'indonesia',
				'base_api'                  => 'nusantara',
				'rajaongkir_key'            => '',
				'rajaongkir_type'           => 'starter',
				'rajaongkir_status'         => array( false, '' ),
				'couriers'                  => array( 'jne', 'pos', 'tiki' ),
				'store_location'            => array(),
				'specific_service'          => 'no',
				'specific_service_option'   => array(),
				'international_shipping'    => 'no',
				'show_weight_on_checkout'   => 'no',
				'round_weight'              => 'auto',
				'round_weight_tolerance'    => 500,
				'unique_number'             => 'no',
				'unique_number_length'      => 1,
				'markup_fee'                => 'no',
				'markup_fee_amount'         => 0,
				'cache_expiration_costs'    => 72,
				'cache_expiration_addresses' => 168,
				'default_weight'            => 1,
				'custom_cost_courier'       => 'Custom',
				'custom_cost_type'          => 'append',
				'auto_fill_address'         => 'yes',
				'show_long_description'     => 'no',
				'enable_volume_calculation' => 'no',
				'enable_insurance'          => 'set',
				'enable_timber_packing'     => 'set',
				'timber_packing_multiplier' => 1,
			)
		);
		if ( ! is_null( $index ) ) {
			if ( isset( $defaults[ $index ] ) ) {
				return $defaults[ $index ];
			} else {
				return false;
			}
		}
		return $defaults;
	}

	/**
	 * Save settings
	 *
	 * @param  array $settings Setting values.
	 */
	public function save( $settings = array() ) {
		$new_value = wp_parse_args( $settings, $this->get_defaults() );
		update_option( $this->option_name_setting, $new_value, true );
		$this->setting = get_option( $this->option_name_setting, $this->get_defaults() );
	}

	/**
	 * Reset settings
	 */
	public function reset() {
		$this->save( $this->get_defaults() );
	}

	/**
	 * Get all settings into one array
	 *
	 * @return array Setting values
	 */
	public function get_all() {
		return wp_parse_args( $this->setting, $this->get_defaults() );
	}

	/**
	 * Get single setting
	 *
	 * @param  string $index Setting index.
	 * @return mixed         Setting value if setting exists.
	 */
	public function get( $index = '' ) {
		if ( isset( $this->setting[ $index ] ) ) {
			return $this->setting[ $index ];
		}
		return $this->get_defaults( $index );
	}

	/**
	 * Set single setting
	 *
	 * @param string $index Setting index.
	 * @param string $value Setting value.
	 */
	public function set( $index = '', $value = '' ) {
		if ( in_array( $index, array_keys( $this->setting ), true ) ) {
			$this->setting[ $index ] = isset( $value ) ? $value : $this->get_defaults( $index );
			update_option( $this->option_name_setting, $this->setting, true );
			$this->setting = get_option( $this->option_name_setting, $this->get_defaults() );
		}
	}

	/**
	 * Save custom fees
	 *
	 * @param  array $fees Custom fees.
	 */
	public function save_custom_costs( $fees = array() ) {
		update_option( $this->option_name_custom_costs, $fees, false );
	}

	/**
	 * Get custom fees
	 *
	 * @return array Custom fees
	 */
	public function get_custom_costs() {
		return get_option( $this->option_name_custom_costs, array() );
	}

	/**
	 * Reset custom costs
	 */
	public function reset_custom_costs() {
		update_option( $this->option_name_custom_costs, array(), false );
	}

	/**
	 * Setting migration from old version.
	 * If user previously installed version <=2.1.3, then migrate their setting to new one
	 */
	public function setting_migration() {
		// migrate settings.
		$setting = array(
			'base_server'               => get_option( 'nusantara_base_server', $this->get_defaults( 'base_server' ) ),
			'base_api'                  => get_option( 'nusantara_base_api', $this->get_defaults( 'base_api' ) ),
			'rajaongkir_key'            => get_option( 'nusantara_api_key_raja_ongkir', $this->get_defaults( 'rajaongkir_key' ) ),
			'rajaongkir_type'           => get_option( 'nusantara_raja_ongkir_type', $this->get_defaults( 'rajaongkir_type' ) ),
			'rajaongkir_status'         => get_option( 'nusantara_rajaongkir_key_status', $this->get_defaults( 'rajaongkir_status' ) ),
			'couriers'                  => get_option( 'nusantara_courir_type', $this->get_defaults( 'couriers' ) ),
			'store_location'            => get_option( 'nusantara_store_location', $this->get_defaults( 'store_location' ) ),
			'specific_service'          => get_option( 'nusantara_is_specific_courir_type', $this->get_defaults( 'specific_service' ) ),
			'specific_service_option'   => get_option( 'nusantara_specific_courir_type', $this->get_defaults( 'specific_service_option' ) ),
			'international_shipping'    => get_option( 'nusantara_international_shipping', $this->get_defaults( 'international_shipping' ) ),
			'show_weight_on_checkout'   => get_option( 'nusantara_shipping_cost_by_kg', $this->get_defaults( 'show_weight_on_checkout' ) ),
			'round_weight'              => get_option( 'nusantara_round_shipping_weight', $this->get_defaults( 'round_weight' ) ),
			'round_weight_tolerance'    => get_option( 'nusantara_round_shipping_weight_tolerance', $this->get_defaults( 'round_weight_tolerance' ) ),
			'unique_number'             => get_option( 'nusantara_with_unique_number', $this->get_defaults( 'unique_number' ) ),
			'unique_number_length'      => get_option( 'nusantara_unique_number', $this->get_defaults( 'unique_number_length' ) ),
			'markup_fee'                => get_option( 'nusantara_with_ongkos_kirim_tambahan', $this->get_defaults( 'markup_fee' ) ),
			'markup_fee_amount'         => get_option( 'nusantara_ongkos_kirim_tambahan', $this->get_defaults( 'markup_fee_amount' ) ),
			'cache_expiration_costs'    => get_option( 'nusantara_transient_request_interval', $this->get_defaults( 'cache_expiration_costs' ) ),
			'custom_cost_courier'       => get_option( 'nusantara_custom_courier', $this->get_defaults( 'custom_cost_courier' ) ),
			'custom_cost_type'          => get_option( 'nusantara_custom_ongkir_type', $this->get_defaults( 'custom_cost_type' ) ),
			'auto_fill_address'         => get_option( 'nusantara_save_returned_user_information', $this->get_defaults( 'auto_fill_address' ) ),
			'show_long_description'     => get_option( 'nusantara_show_long_description', $this->get_defaults( 'show_long_description' ) ),
		);
		$this->save( $setting );

		// migrate custom costs.
		$custom_costs = get_option( 'nusantara_manual_shipping', array() );
		$custom_costs = array_map( array( $this, 'rename_custom_costs_indexes' ), $custom_costs );
		$this->save_custom_costs( $custom_costs );
	}

	/**
	 * Renaming old custom cost indexes
	 *
	 * @param  array $cost Custom cost data.
	 * @return array       Custom cost data.
	 */
	private function rename_custom_costs_indexes( $cost ) {
		return array(
			'province_id'   => $cost['manualselectprovince'],
			'province_text' => $cost['manualselectprovince_text'],
			'city_id'       => $cost['manualselectcity'],
			'city_text'     => $cost['manualselectcity_text'],
			'district_id'   => $cost['manualselectdistrict'],
			'district_text' => $cost['manualselectdistrict_text'],
			'courier'       => $cost['ekspedisi'],
			'package_name'  => $cost['nunsatara_jenis'],
			'cost'          => $cost['nusantara_tarif'],
		);
	}

}
